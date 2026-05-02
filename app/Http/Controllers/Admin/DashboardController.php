<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Loan;
use App\Models\Fine;
use App\Models\Visit;
use App\Models\Book;
use App\Models\BookCopy;
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

        // ── Chart data ─────────────────────────────────────────────────
        // Hari dalam bulan ini (1 s/d hari ini)
        $daysInThisMonth = now()->day;
        $thisMonthStart  = now()->startOfMonth();

        $rawThisMonth = Loan::select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$thisMonthStart, now()->endOfDay()])
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get()->keyBy('day');

        // Hari dalam bulan lalu (1 s/d hari yg sama di bulan lalu)
        $lastMonthStart = now()->subMonth()->startOfMonth();
        $lastMonthEnd   = now()->subMonth()->setDay($daysInThisMonth)->endOfDay();

        $rawLastMonth = Loan::select(DB::raw('DAY(created_at) as day'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get()->keyBy('day');

        $chartDays = [];
        for ($d = 1; $d <= $daysInThisMonth; $d++) {
            $chartDays[] = [
                'label'      => $d,
                'this_month' => (int)($rawThisMonth[$d]->total ?? 0),
                'last_month' => (int)($rawLastMonth[$d]->total ?? 0),
            ];
        }

        // Minggu ini vs minggu lalu (per hari, 7 hari)
        $rawThisWeek = Loan::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()->keyBy('date');

        $rawLastWeek = Loan::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->whereBetween('created_at', [now()->subDays(13)->startOfDay(), now()->subDays(7)->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->get()->keyBy('date');

        $chartWeekCompare = [];
        for ($i = 6; $i >= 0; $i--) {
            $thisDay = now()->subDays($i)->format('Y-m-d');
            $lastDay = now()->subDays($i + 7)->format('Y-m-d');
            $chartWeekCompare[] = [
                'label'      => now()->subDays($i)->locale('id')->isoFormat('ddd D/M'),
                'this_month' => (int)($rawThisWeek[$thisDay]->total ?? 0),
                'last_month' => (int)($rawLastWeek[$lastDay]->total ?? 0),
            ];
        }

        // 12 bulan terakhir vs 12 bulan sebelumnya (per bulan)
        $rawYear = Loan::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(23)->startOfMonth())
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy(fn($r) => $r->year . '-' . str_pad($r->month, 2, '0', STR_PAD_LEFT));

        $chartYearCompare = [];
        for ($i = 11; $i >= 0; $i--) {
            $m    = now()->subMonths($i);
            $mPrev = now()->subMonths($i + 12);
            $chartYearCompare[] = [
                'label'      => $m->locale('id')->isoFormat('MMM YY'),
                'this_month' => (int)($rawYear[$m->format('Y-m')]->total ?? 0),
                'last_month' => (int)($rawYear[$mPrev->format('Y-m')]->total ?? 0),
            ];
        }

        // Summary bulan ini vs bulan lalu
        $thisMonthTotal = (int)array_sum(array_column($chartDays, 'this_month'));
        $lastMonthTotal = (int)array_sum(array_column($chartDays, 'last_month'));

        return Inertia::render('Admin/Dashboard', [
            'stats'              => $stats,
            'recentLoans'        => $recentLoans,
            'overdueLoans'       => $overdueLoans,
            'popularBooks'       => $popularBooks,
            'chartDays'          => $chartDays,
            'chartWeekCompare'   => $chartWeekCompare,
            'chartYearCompare'   => $chartYearCompare,
            'loanSummary'        => ['this_month' => $thisMonthTotal, 'last_month' => $lastMonthTotal],
        ]);
    }
}
