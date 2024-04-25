<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    use HasFactory;

    protected $fillable = ['review', 'rating'];

    public function reviews() {

        return $this->hasMany(Review::class);//jedna knjiga moze imati vise recenzija

    }

    //lokalni opseg upita
    public function scopeTitle(Builder $query, string $title): Builder {

        return $query->where('title', 'LIKE', '%' . $TITLE . '%');

    }
    //najpopularnije knjige
    public function sopePopular(Builder $qurery):Builder {

        return $query->withCount('reviews')
            ->orderBy('reviews_count', 'desc');
    }
    //sa najvisom ocjenom knjige
    public function scopeHighestRated(Builder $query): Builder {

        return $query->withAvg('reviews', 'rating')
            ->orderBy('reviews_avg_rating', 'desc');

    }
}
