<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalMembers = Member::count();
        $activeBorrowings = Borrowing::where('status', 'dipinjam')->count();
        $overdueBorrowings = Borrowing::where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', now())
            ->count();
        $totalStok = Book::sum('stok');

        $recentBorrowings = Borrowing::with(['book', 'member'])
            ->latest()
            ->take(5)
            ->get();

        $popularBooks = Book::withCount('borrowings')
            ->orderByDesc('borrowings_count')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'totalMembers',
            'activeBorrowings',
            'overdueBorrowings',
            'totalStok',
            'recentBorrowings',
            'popularBooks'
        ));
    }
}
