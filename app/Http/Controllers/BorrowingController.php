<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['book', 'member', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%");
            })->orWhereHas('member', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $borrowings = $query->latest()->paginate(10)->withQueryString();

        return view('borrowings.index', compact('borrowings'));
    }

    public function create()
    {
        $books = Book::where('stok', '>', 0)->orderBy('judul')->get();
        $members = Member::orderBy('nama')->get();

        return view('borrowings.create', compact('books', 'members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'member_id' => 'required|exists:members,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after:tanggal_pinjam',
        ]);

        $book = Book::findOrFail($validated['book_id']);

        if ($book->stok <= 0) {
            return back()->with('error', 'Stok buku habis!')->withInput();
        }

        Borrowing::create([
            'book_id' => $validated['book_id'],
            'member_id' => $validated['member_id'],
            'user_id' => Auth::id(),
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali' => $validated['tanggal_kembali'],
            'status' => 'dipinjam',
        ]);

        $book->decrement('stok');

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman berhasil dicatat!');
    }

    public function returnBook(Borrowing $borrowing)
    {
        if ($borrowing->status === 'dikembalikan') {
            return back()->with('error', 'Buku sudah dikembalikan sebelumnya!');
        }

        $today = now()->toDateString();
        $denda = 0;

        if (now()->gt($borrowing->tanggal_kembali)) {
            $daysLate = now()->diffInDays($borrowing->tanggal_kembali);
            $denda = $daysLate * 1000; // Rp 1.000 per hari
        }

        $borrowing->update([
            'tanggal_dikembalikan' => $today,
            'status' => 'dikembalikan',
            'denda' => $denda,
        ]);

        $borrowing->book->increment('stok');

        $message = 'Buku berhasil dikembalikan!';
        if ($denda > 0) {
            $message .= ' Denda keterlambatan: Rp ' . number_format($denda, 0, ',', '.');
        }

        return back()->with('success', $message);
    }
}
