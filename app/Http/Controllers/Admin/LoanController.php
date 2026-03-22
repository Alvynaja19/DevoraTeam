<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Member;
use App\Models\BookCopy;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LoanController extends Controller
{
    public function __construct(private LoanService $loanService) {}

    public function index(Request $request)
    {
        $loans = Loan::with(['member', 'items.copy.book', 'createdBy'])
            ->when($request->status,    fn($q, $s) => $q->where('status', $s))
            ->when($request->search,    fn($q, $s) => $q->whereHas('member', fn($m) => $m->where('name', 'like', "%{$s}%")
                ->orWhere('member_code', 'like', "%{$s}%")))
            ->when($request->overdue,   fn($q) => $q->where('due_date', '<', today())
                ->whereIn('status', ['aktif', 'diperpanjang']))
            ->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Loans/Index', [
            'loans'   => $loans,
            'filters' => $request->only(['search', 'status', 'overdue']),
        ]);
    }

    public function riwayat(Request $request)
    {
        $loans = Loan::with(['member', 'items.copy.book', 'createdBy'])
            ->when($request->status,  fn($q, $s) => $q->where('status', $s))
            ->when($request->search,  fn($q, $s) => $q->whereHas('member', fn($m) => $m->where('name', 'like', "%{$s}%")
                ->orWhere('member_code', 'like', "%{$s}%")))
            ->when($request->overdue, fn($q) => $q->where('due_date', '<', today())
                ->whereIn('status', ['aktif', 'diperpanjang']))
            ->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Sirkulasi/Index', [
            'page'    => 'riwayat',
            'loans'   => $loans,
            'filters' => $request->only(['search', 'status', 'overdue']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Loans/Create');
    }

    // Validasi anggota via member code (untuk scan QR)
    public function validateMember(Request $request)
    {
        $request->validate(['member_code' => 'required|string']);

        $member = Member::with(['user'])->where('member_code', $request->member_code)->first();

        if (!$member) {
            return response()->json(['valid' => false, 'message' => 'Anggota tidak ditemukan.'], 404);
        }

        $validation = $this->loanService->validateMember($member);

        return response()->json([
            'valid'   => $validation['valid'],
            'message' => $validation['message'],
            'member'  => $member,
            'quota'   => $validation['quota_remaining'] ?? null,
        ]);
    }

    // Validasi buku via barcode
    public function validateBook(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $copy = BookCopy::with('book.category')
            ->where('barcode', $request->barcode)
            ->orWhere('copy_code', $request->barcode)
            ->first();

        if (!$copy) {
            return response()->json(['valid' => false, 'message' => 'Buku tidak ditemukan.'], 404);
        }

        if ($copy->status !== 'tersedia') {
            return response()->json(['valid' => false, 'message' => "Buku berstatus '{$copy->status}', tidak tersedia."], 422);
        }

        return response()->json(['valid' => true, 'copy' => $copy]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_code'  => 'required|string',
            'barcodes'  => 'required|array|min:1|max:2',
            'barcodes.*'=> 'required|string',
            'loan_type' => 'required|in:pembaca,lomba',
        ]);

        $member = Member::where('member_code', $request->member_code)->firstOrFail();
        $validation = $this->loanService->validateMember($member);

        if (!$validation['valid']) {
            return back()->withErrors(['member_code' => $validation['message']]);
        }

        $loan = $this->loanService->create($member, $request->barcodes, $request->user(), $request->loan_type);

        return redirect()->route('sirkulasi.riwayat')->with('success', 'Peminjaman berhasil dibuat.');
    }

    public function show(Loan $loan)
    {
        $loan->load(['member.user', 'items.copy.book', 'extensions', 'createdBy']);
        return Inertia::render('Admin/Loans/Show', ['loan' => $loan]);
    }

    public function extend(Loan $loan, Request $request)
    {
        try {
            $this->loanService->extend($loan, $request->user());
            return back()->with('success', 'Peminjaman berhasil diperpanjang.');
        } catch (\Exception $e) {
            return back()->withErrors(['extend' => $e->getMessage()]);
        }
    }
}
