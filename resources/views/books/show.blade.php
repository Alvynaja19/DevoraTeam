@extends('layouts.app')
@section('title', $book->judul)
@section('header', 'Detail Buku')

@section('content')
<div class="max-w-3xl space-y-6">
    {{-- Book Detail Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-800">{{ $book->judul }}</h2>
                <p class="text-slate-500 mt-1">{{ $book->pengarang }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('books.edit', $book) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 text-amber-700 hover:bg-amber-100 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                    Edit
                </a>
                <a href="{{ route('books.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Kembali
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Penerbit</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ $book->penerbit ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Tahun</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ $book->tahun_terbit ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">ISBN</p>
                <p class="text-sm font-medium text-slate-800 mt-1 font-mono">{{ $book->isbn ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Kategori</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ $book->kategori ?? '-' }}</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Jumlah</p>
                <p class="text-sm font-medium text-slate-800 mt-1">{{ $book->jumlah_buku }} eksemplar</p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Stok Tersedia</p>
                <p class="text-sm font-bold mt-1 {{ $book->stok > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $book->stok }} eksemplar</p>
            </div>
        </div>
    </div>

    {{-- Borrowing History --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">Riwayat Peminjaman</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($book->borrowings as $borrowing)
                <div class="px-6 py-3.5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $borrowing->member->nama }}</p>
                        <p class="text-xs text-slate-500">{{ $borrowing->tanggal_pinjam->format('d M Y') }} — {{ $borrowing->tanggal_kembali->format('d M Y') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ $borrowing->status === 'dipinjam' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ ucfirst($borrowing->status) }}
                    </span>
                </div>
            @empty
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-slate-400">Belum ada riwayat peminjaman</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
