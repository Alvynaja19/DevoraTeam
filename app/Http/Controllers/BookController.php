<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

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
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $books = $query->latest()->paginate(10)->withQueryString();
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
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|string|max:4',
            'isbn' => 'nullable|string|max:20|unique:books',
            'jumlah_buku' => 'required|integer|min:1',
            'kategori' => 'nullable|string|max:100',
        ]);

        $validated['stok'] = $validated['jumlah_buku'];

        Book::create($validated);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Book $book)
    {
        $book->load(['borrowings' => function ($q) {
            $q->with('member')->latest()->take(10);
        }]);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'pengarang' => 'required|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|string|max:4',
            'isbn' => 'nullable|string|max:20|unique:books,isbn,' . $book->id,
            'jumlah_buku' => 'required|integer|min:1',
            'kategori' => 'nullable|string|max:100',
        ]);

        // Adjust stok based on jumlah_buku change
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
}
