<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Services\FineService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FineController extends Controller
{
    public function __construct(private FineService $fineService) {}

    public function index(Request $request)
    {
        $fines = Fine::with(['loanItem.loan.member', 'loanItem.copy.book'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->whereHas('loanItem.loan.member',
                fn($m) => $m->where('name', 'like', "%{$s}%")))
            ->latest()->paginate(20)->withQueryString();

        return Inertia::render('Admin/Fines/Index', [
            'fines'   => $fines,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function pay(Fine $fine, Request $request)
    {
        $request->validate([
            'amount_paid'    => 'required|integer|min:1',
            'receipt_number' => 'nullable|string|max:50',
        ]);

        $this->fineService->pay($fine, $request->amount_paid, $request->user(), $request->receipt_number);

        return back()->with('success', 'Pembayaran denda berhasil dicatat.');
    }

    public function free(Fine $fine, Request $request)
    {
        $request->validate(['reason' => 'required|string|max:255']);
        $this->fineService->free($fine, $request->reason, $request->user());
        return back()->with('success', 'Denda berhasil dibebaskan.');
    }
}
