<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['review', 'rating'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    ////lokalni opseg upita
    public function scopeTitle(Builder $query, string $title): Builder|QueryBuilder
    {
        return $query->where('title', 'LIKE', '%' . $title . '%');
    }

    //najpopularnije knjige
    public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withCount([
            'reviews' => function (Builder $q) use ($from, $to) {
                return $this->dateRangeFilter($q, $from, $to);
            }
        ])->orderByDesc('reviews_count');
    }

    //knjige sa najvecim ocjenama
    public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
    {
        return $query->withAvg([
            'reviews' => function (Builder $q) use ($from, $to) {
                return $this->dateRangeFilter($q, $from, $to);
            }
        ], 'rating')->orderByDesc('reviews_avg_rating');
    }


    public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder
    {
        return $query->having('reviews_count', '>=', $minReviews);
    }
    

    private function dateRangeFilter(Builder $query, $from = null, $to = null)
    {
        if ($from && !$to) {
            $query->where('created_at', '>=', $from);
        } elseif (!$from && $to) {
            $query->where('created_at', '<=', $to);
        } elseif ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }
        return $query;
    }
}
