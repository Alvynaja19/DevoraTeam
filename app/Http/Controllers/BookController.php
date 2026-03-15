<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Imports\BookImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('pengarang', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $books      = $query->latest()->paginate(10)->withQueryString();
        $categories = Book::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');

        return view('books.index', compact('books', 'categories'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul'            => 'required|string|max:255',
            'pengarang'        => 'required|string|max:255',
            'penerbit'         => 'nullable|string|max:255',
            'kota'             => 'nullable|string|max:100',
            'tahun_terbit'     => 'nullable|string|max:4',
            'isbn'             => 'nullable|string|max:30|unique:books',
            'jumlah_buku'      => 'required|integer|min:1',
            'kategori'         => 'nullable|string|max:100',
            'no_klasifikasi'   => 'nullable|string|max:50',
            'perolehan'        => 'nullable|string|max:100',
            'tanggal_diterima' => 'nullable|date',
        ]);

        $validated['stok'] = $validated['jumlah_buku'];
        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Book $book)
    {
        $book->load(['borrowings' => fn($q) => $q->with('member')->latest()->take(10)]);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'judul'            => 'required|string|max:255',
            'pengarang'        => 'required|string|max:255',
            'penerbit'         => 'nullable|string|max:255',
            'kota'             => 'nullable|string|max:100',
            'tahun_terbit'     => 'nullable|string|max:4',
            'isbn'             => 'nullable|string|max:30|unique:books,isbn,' . $book->id,
            'jumlah_buku'      => 'required|integer|min:1',
            'kategori'         => 'nullable|string|max:100',
            'no_klasifikasi'   => 'nullable|string|max:50',
            'perolehan'        => 'nullable|string|max:100',
            'tanggal_diterima' => 'nullable|date',
        ]);

        $diff = $validated['jumlah_buku'] - $book->jumlah_buku;
        $validated['stok'] = max(0, $book->stok + $diff);

        $book->update($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy(Book $book)
    {
        if ($book->borrowings()->where('status', 'dipinjam')->exists()) {
            return back()->with('error', 'Tidak bisa menghapus buku yang sedang dipinjam!');
        }

        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
    }

    // ─── Import Excel ─────────────────────────────────────────────────────────

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'Format harus .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        try {
            $import = new BookImport();
            Excel::import($import, $request->file('file'));

            $msg = "Import selesai: {$import->getImportedCount()} buku ditambahkan.";
            if ($import->getSkippedCount() > 0) {
                $msg .= " {$import->getSkippedCount()} baris dilewati (duplikat/kosong).";
            }

            return redirect()->route('books.index')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->route('books.index')->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }
}
