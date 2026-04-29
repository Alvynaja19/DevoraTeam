<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Laporan Denda
     */
    public function fineReport(Request $request)
    {
        $period = $request->input('period', 'hari');
        $startDate = $this->getStartDate($period, $request);
        $endDate = $this->getEndDate($period, $request);

        // Stats
        $finesQuery = Fine::whereBetween('created_at', [$startDate, $endDate]);

        $stats = [
            'total_belum_lunas' => (clone $finesQuery)->where('status', 'belum_lunas')->sum('amount'),
            'count_belum_lunas' => (clone $finesQuery)->where('status', 'belum_lunas')->count(),
            'total_lunas'       => (clone $finesQuery)->where('status', 'lunas')->sum('amount'),
            'count_lunas'       => (clone $finesQuery)->where('status', 'lunas')->count(),
            'total_dibebaskan'  => (clone $finesQuery)->where('status', 'dibebaskan')->sum('amount'),
            'count_dibebaskan'  => (clone $finesQuery)->where('status', 'dibebaskan')->count(),
        ];

        // Fines table data
        $fines = Fine::with(['loanItem.loan.member', 'loanItem.copy.book'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->whereHas('loanItem.loan.member',
                fn($m) => $m->where('name', 'like', "%{$s}%")))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Reports/FineReport', [
            'stats'      => $stats,
            'fines'      => $fines,
            'filters'    => $request->only(['search', 'status', 'period', 'start_date', 'end_date']),
            'periodInfo' => [
                'start' => $startDate->format('Y-m-d'),
                'end'   => $endDate->format('Y-m-d'),
                'label' => $this->getPeriodLabel($period, $startDate, $endDate),
            ],
        ]);
    }

    /**
     * Laporan Presensi
     */
    public function attendanceReport(Request $request)
    {
        $period = $request->input('period', 'minggu');
        $startDate = $this->getStartDate($period, $request);
        $endDate = $this->getEndDate($period, $request);

        // Stats
        $visitsQuery = Visit::whereBetween('visit_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        $totalPengunjung = (clone $visitsQuery)->where('category', 'pengunjung')->count();
        $totalPembaca    = (clone $visitsQuery)->where('category', 'membaca')->count();
        $totalAll        = $totalPengunjung + $totalPembaca;
        $dayCount        = max(1, $startDate->diffInDays($endDate) + 1);

        $stats = [
            'total_pengunjung' => $totalPengunjung,
            'total_pembaca'    => $totalPembaca,
            'total_all'        => $totalAll,
            'avg_daily'        => round($totalAll / $dayCount, 1),
        ];

        // Chart data — group by visit_date
        $chartRaw = Visit::select(
                'visit_date',
                'category',
                DB::raw('COUNT(*) as jumlah')
            )
            ->whereBetween('visit_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->groupBy('visit_date', 'category')
            ->orderBy('visit_date')
            ->get();

        // Create keyed map for faster lookup: date-category => count
        $chartMap = [];
        foreach ($chartRaw as $row) {
            $key = $row->visit_date->format('Y-m-d') . '|' . $row->category;
            $chartMap[$key] = $row->jumlah;
        }

        // Build chart data with all dates in range
        $labels = [];
        $pengunjungData = [];
        $pembacaData = [];

        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');

            // Use keyed map for reliable lookup
            $pengunjungKey = $dateStr . '|pengunjung';
            $pembacaKey = $dateStr . '|membaca';

            $pengunjungData[] = $chartMap[$pengunjungKey] ?? 0;
            $pembacaData[] = $chartMap[$pembacaKey] ?? 0;

            $current->addDay();
        }

        $chartData = [
            'labels'     => $labels,
            'pengunjung' => $pengunjungData,
            'pembaca'    => $pembacaData,
        ];

        // Table data — daily summary
        $dailySummary = [];
        for ($i = 0; $i < count($labels); $i++) {
            $dailySummary[] = [
                'date'       => $labels[$i],
                'pengunjung' => $pengunjungData[$i],
                'pembaca'    => $pembacaData[$i],
                'total'      => $pengunjungData[$i] + $pembacaData[$i],
            ];
        }

        return Inertia::render('Admin/Reports/AttendanceReport', [
            'stats'        => $stats,
            'chartData'    => $chartData,
            'dailySummary' => $dailySummary,
            'filters'      => $request->only(['period', 'start_date', 'end_date']),
            'periodInfo'   => [
                'start' => $startDate->format('Y-m-d'),
                'end'   => $endDate->format('Y-m-d'),
                'label' => $this->getPeriodLabel($period, $startDate, $endDate),
            ],
        ]);
    }

    private function getStartDate(string $period, Request $request): Carbon
    {
        return match ($period) {
            'hari'    => $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today(),
            'kemarin' => Carbon::yesterday(),
            'minggu'  => Carbon::now()->startOfWeek(),
            'bulan'   => Carbon::now()->startOfMonth(),
            'custom'  => Carbon::parse($request->input('start_date', today()))->startOfDay(),
            default   => Carbon::today(),
        };
    }

    private function getEndDate(string $period, Request $request): Carbon
    {
        return match ($period) {
            'hari'    => $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay(),
            'kemarin' => Carbon::yesterday()->endOfDay(),
            'minggu'  => Carbon::now()->endOfWeek(),
            'bulan'   => Carbon::now()->endOfMonth(),
            'custom'  => Carbon::parse($request->input('end_date', today()))->endOfDay(),
            default   => Carbon::today()->endOfDay(),
        };
    }

    private function getPeriodLabel(string $period, Carbon $start, Carbon $end): string
    {
        return match ($period) {
            'hari'    => 'Hari Ini — ' . $start->translatedFormat('d F Y'),
            'kemarin' => 'Kemarin — ' . $start->translatedFormat('d F Y'),
            'minggu'  => 'Minggu Ini — ' . $start->translatedFormat('d M') . ' s/d ' . $end->translatedFormat('d M Y'),
            'bulan'   => 'Bulan Ini — ' . $start->translatedFormat('F Y'),
            'custom'  => $start->translatedFormat('d M Y') . ' — ' . $end->translatedFormat('d M Y'),
            default   => $start->translatedFormat('d F Y'),
        };
    }
}
