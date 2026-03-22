<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function home()
    {
        // Stats
        $totalBooks      = Book::count();
        $totalCategories = Category::count();
        $totalMembers    = Member::where('status', 'aktif')->count();
        $totalLoans      = Loan::count();

        // 8 buku populer
        $popularBooks = Book::with('category')
            ->popular()
            ->limit(8)
            ->get()
            ->map(fn($b) => [
                'id'              => $b->id,
                'title'           => $b->title,
                'author'          => $b->author,
                'cover_image'     => $b->cover_image,
                'avg_rating'      => $b->avg_rating,
                'total_loans'     => $b->total_loans,
                'category'        => $b->category?->name,
                'available_count' => $b->availableCopies()->count(),
            ]);

        // Semua kategori dengan jumlah buku
        $categories = Category::withCount('books')
            ->orderByDesc('books_count')
            ->get(['id', 'name', 'code']);

        // Pengaturan perpustakaan
        $settings = Setting::whereIn('key', ['library_name', 'school_name'])
            ->pluck('value', 'key');

        return Inertia::render('Public/Home', [
            'stats' => [
                'total_books'      => $totalBooks,
                'total_categories' => $totalCategories,
                'total_members'    => $totalMembers,
                'total_loans'      => $totalLoans,
            ],
            'popularBooks' => $popularBooks,
            'categories'   => $categories,
            'settings'     => $settings,
        ]);
    }

    public function catalog(Request $request)
    {
        $query = Book::with('category')->withCount([
            'copies as available_count' => fn($q) =>
                $q->where('status', 'tersedia')->where('condition', '!=', 'hilang'),
        ]);

        // Filter search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Filter kategori
        if ($category = $request->get('category')) {
            $query->where('category_id', $category);
        }

        // Filter ketersediaan
        if ($request->get('availability') === 'tersedia') {
            $query->whereHas('copies', fn($q) =>
                $q->where('status', 'tersedia')->where('condition', '!=', 'hilang')
            );
        } elseif ($request->get('availability') === 'dipinjam') {
            $query->whereDoesntHave('copies', fn($q) =>
                $q->where('status', 'tersedia')->where('condition', '!=', 'hilang')
            );
        }

        $books = $query->orderByDesc('total_loans')
            ->paginate(24)
            ->withQueryString()
            ->through(fn($b) => [
                'id'              => $b->id,
                'title'           => $b->title,
                'author'          => $b->author,
                'publisher'       => $b->publisher,
                'year'            => $b->year,
                'cover_image'     => $b->cover_image,
                'avg_rating'      => $b->avg_rating,
                'total_loans'     => $b->total_loans,
                'rack_number'     => $b->rack_number,
                'category'        => $b->category?->name,
                'category_id'     => $b->category_id,
                'available_count' => $b->available_count,
            ]);

        $categories = Category::withCount('books')
            ->orderByDesc('books_count')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Public/Catalog', [
            'books'      => $books,
            'categories' => $categories,
            'filters'    => $request->only(['search', 'category', 'availability']),
        ]);
    }
}
