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
            'total_anggota'  => Member::where('status', 'aktif')->count(),
            'pending_approval'=> Member::where('status', 'pending')->count(),
            'active_loans'   => Loan::whereIn('status', ['aktif', 'diperpanjang'])->count(),
            'overdue_loans'   => Loan::where('status', 'terlambat')
                                     ->orWhere(fn($q) => $q->whereIn('status', ['aktif', 'diperpanjang'])->where('due_date', '<', today()))
                                     ->count(),
            'unpaid_fines'   => Fine::where('status', 'belum_lunas')->sum('amount'),
            'total_books'    => Book::count(),
            'visits_today'   => Visit::where('visit_date', today())->count(),
        ];

        $recentLoans = Loan::with(['member', 'items.copy.book', 'createdBy'])
            ->latest()
            ->take(10)
            ->get();

        $overdueLoanList = Loan::with(['member'])
            ->where(fn($q) => $q
                ->where('status', 'terlambat')
                ->orWhere(fn($q) => $q
                    ->whereIn('status', ['aktif', 'diperpanjang'])
                    ->where('due_date', '<', today())
                )
            )
            ->orderBy('due_date')
            ->take(10)
            ->get();

        $pendingMembers = Member::with(['user', 'kelas'])
            ->where('status', 'pending')
            ->oldest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats'          => $stats,
            'recentLoans'    => $recentLoans,
            'overdueLoans'   => $overdueLoanList,
            'pendingMembers' => $pendingMembers,
        ]);
    }
}
