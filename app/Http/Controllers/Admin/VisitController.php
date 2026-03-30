<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class VisitController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'hari'); // hari, minggu, bulan

        $startDate = Carbon::today();
        $endDate = Carbon::today()->endOfDay();

        if ($period === 'minggu') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
        }
        elseif ($period === 'bulan') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($period === 'tahun') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        $stats = [
            'total_today' => Visit::whereBetween('created_at', [$startDate, $endDate])->count(),
            'reading' => Visit::whereBetween('created_at', [$startDate, $endDate])->where('category', 'membaca')->count(),
            'visitor' => Visit::whereBetween('created_at', [$startDate, $endDate])->where('category', 'pengunjung')->count(),
        ];

        $perPage = $request->input('per_page', 10);

        $visits = Visit::with('member')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->search, function ($query, $search) {
            $query->whereHas('member', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('member_code', 'like', "%{$search}%")
                        ->orWhere('nis_nip', 'like', "%{$search}%");
                }
                );
            })
            ->when($request->category, function ($query, $category) {
            $query->where('category', $category);
        })
            ->latest('created_at')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Visits/Index', [
            'stats' => $stats,
            'visits' => $visits,
            'filters' => $request->only(['search', 'category', 'per_page', 'period']),
        ]);
    }

    public function check(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $member = Member::where('member_code', $request->barcode)
            ->orWhere('nis_nip', $request->barcode)
            ->with('kelas')
            ->first();

        if (!$member) {
            return response()->json(['valid' => false, 'message' => 'Anggota tidak ditemukan.'], 404);
        }

        if (!$member->isAktif()) {
            return response()->json(['valid' => false, 'message' => 'Status anggota tidak aktif.']);
        }

        return response()->json([
            'valid' => true,
            'member' => $member
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'category' => 'required|in:membaca,pengunjung',
        ]);

        $member = Member::where('member_code', $request->barcode)
            ->orWhere('nis_nip', $request->barcode)
            ->first();
        if (!$member) {
            return back()->with('error', 'Barcode anggota tidak ditemukan.');
        }

        if (!$member->isAktif()) {
            return back()->with('error', 'Status anggota tidak aktif.');
        }

        // Anti-spam 5 menit
        $recentVisit = Visit::where('member_id', $member->id)
            ->where('visit_date', Carbon::today())
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->first();

        if ($recentVisit) {
            return back()->with('info', "Anggota {$member->name} sudah tercatat hadir beberapa saat lalu.");
        }

        Visit::create([
            'member_id' => $member->id,
            'visit_date' => Carbon::today(),
            'visit_time' => Carbon::now()->format('H:i:s'),
            'category' => $request->category,
            'scanned_by' => auth()->id(),
            'created_at' => Carbon::now(),
        ]);

        return back()->with('success', "Presensi {$member->name} ({$request->category}) berhasil dicatat.");
    }

    public function update(Request $request, Visit $visit)
    {
        $request->validate([
            'category'   => 'required|in:membaca,pengunjung',
        ]);

        $visit->update([
            'category'   => $request->category,
        ]);

        return back()->with('success', 'Tujuan kunjungan berhasil diperbarui.');
    }

    public function destroy(Visit $visit)
    {
        $visit->delete();
        return back()->with('success', 'Data kunjungan berhasil dihapus.');
    }
}