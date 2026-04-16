<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Fine;
use App\Models\Visit;
use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_anggota' => Member::where('status', 'aktif')->count(),
            'active_loans'  => Loan::whereIn('status', ['aktif', 'diperpanjang'])->count(),
            'overdue_loans' => Loan::where(fn($q) => $q
                    ->where('status', 'terlambat')
                    ->orWhere(fn($q) => $q->whereIn('status', ['aktif', 'diperpanjang'])->where('due_date', '<', today()))
                )->count(),
            'unpaid_fines'  => Fine::where('status', 'belum_lunas')->sum('amount'),
            'total_books'   => Book::count(),
            'visits_today'  => Visit::where('visit_date', today())->count(),
        ];

        $recentLoans = Loan::with(['member', 'items.copy.book', 'createdBy'])
            ->latest()
            ->take(10)
            ->get();

        $overdueLoans = Loan::with(['member'])
            ->where(fn($q) => $q
                ->where('status', 'terlambat')
                ->orWhere(fn($q) => $q
                    ->whereIn('status', ['aktif', 'diperpanjang'])
                    ->where('due_date', '<', today())
                )
            )
            ->orderBy('due_date')
            ->take(8)
            ->get();

        $popularBooks = DB::table('loan_items')
            ->join('loans', 'loan_items.loan_id', '=', 'loans.id')
            ->join('book_copies', 'loan_items.copy_id', '=', 'book_copies.id')
            ->join('books', 'book_copies.book_id', '=', 'books.id')
            ->select('books.id', 'books.title', 'books.cover_image', 'books.author', DB::raw('count(loan_items.id) as borrow_count'))
            ->where('loans.created_at', '>=', now()->startOfWeek())
            ->groupBy('books.id', 'books.title', 'books.cover_image', 'books.author')
            ->orderByDesc('borrow_count')
            ->limit(3)
            ->get();

        $popularBooks->transform(function ($book) {
            if ($book->cover_image && !\Illuminate\Support\Str::startsWith($book->cover_image, 'http')) {
                $book->cover_image = url('storage/' . $book->cover_image);
            }
            return $book;
        });

        return Inertia::render('Admin/Dashboard', [
            'stats'        => $stats,
            'recentLoans'  => $recentLoans,
            'overdueLoans' => $overdueLoans,
            'popularBooks' => $popularBooks,
        ]);
    }
}
