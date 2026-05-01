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
use Carbon\Carbon;

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

        // ── Chart data ─────────────────────────────────────────
        // Per Minggu: 7 hari terakhir, per hari
        $weekStart = now()->subDays(6)->startOfDay();
        $rawWeek = Loan::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $weekStart)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()->keyBy('date');

        $chartWeek = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $chartWeek[] = [
                'label' => now()->subDays($i)->locale('id')->isoFormat('ddd, D MMM'),
                'total' => $rawWeek->has($day) ? (int)$rawWeek[$day]->total : 0,
            ];
        }

        // Per Bulan: 30 hari terakhir, per hari
        $monthStart = now()->subDays(29)->startOfDay();
        $rawMonth = Loan::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', $monthStart)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()->keyBy('date');

        $chartMonth = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $chartMonth[] = [
                'label' => now()->subDays($i)->locale('id')->isoFormat('D MMM'),
                'total' => $rawMonth->has($day) ? (int)$rawMonth[$day]->total : 0,
            ];
        }

        // Per Tahun: 12 bulan terakhir, per bulan
        $yearStart = now()->subMonths(11)->startOfMonth();
        $rawYear = Loan::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', $yearStart)
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy(fn($r) => $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT));

        $chartYear = [];
        for ($i = 11; $i >= 0; $i--) {
            $m   = now()->subMonths($i);
            $key = $m->format('Y-m');
            $chartYear[] = [
                'label' => $m->locale('id')->isoFormat('MMM YY'),
                'total' => $rawYear->has($key) ? (int)$rawYear[$key]->total : 0,
            ];
        }

        // Summary bulan ini vs bulan lalu
        $thisMonth = Loan::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)->count();
        $lastMonth = Loan::whereYear('created_at', now()->subMonth()->year)
            ->whereMonth('created_at', now()->subMonth()->month)->count();

        return Inertia::render('Admin/Dashboard', [
            'stats'        => $stats,
            'recentLoans'  => $recentLoans,
            'overdueLoans' => $overdueLoans,
            'popularBooks' => $popularBooks,
            'chartWeek'    => $chartWeek,
            'chartMonth'   => $chartMonth,
            'chartYear'    => $chartYear,
            'loanSummary'  => ['this_month' => $thisMonth, 'last_month' => $lastMonth],
        ]);
    }
}
