<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request) {
        $title = $request->input('title');
        $filter = $request->input('filter', '');

        $books = Book::when($title, fn($query, $title) => $query->title($title));

        switch ($filter) {
            case 'popular_last_month':
                $books = $books->popularLastMonth();
                break;
            case 'popular_last_6months':
                $books = $books->popularLast6Months();
                break;
            case 'highest_rated_last_month':
                $books = $books->highestRatedLastMonth();
                break;
            case 'highest_rated_last_6months':
                $books = $books->highestRatedLast6Months();
                break;
            default:
                $books = $books->latest()->withAvgRating()->withReviewsCount();
                break;
        }

        // Paginacija sa 10 knjiga po stranici
        $books = $books->paginate(10);

    return view('books.index', ['books' => $books]);
    
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $cacheKey = 'book:' . $id;

        $book = cache()->remember(
            $cacheKey,
             3600,
              fn() =>
            Book::with([
            'reviews' => fn($query) => $query->latest()
        ])->withAvgRating()->withReviewsCount()->findOrFail($id)
    );

        return view('books.show', ['book' => $book]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
