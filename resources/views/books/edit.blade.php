@extends('layouts.app')
@section('title', 'Edit Buku')
@section('header', 'Edit Buku')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <form method="POST" action="{{ route('books.update', $book) }}" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-1.5">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul', $book->judul) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all @error('judul') border-red-300 @enderror">
                    @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pengarang" class="block text-sm font-medium text-slate-700 mb-1.5">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" name="pengarang" id="pengarang" value="{{ old('pengarang', $book->pengarang) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="no_klasifikasi" class="block text-sm font-medium text-slate-700 mb-1.5">No. Klasifikasi</label>
                    <input type="text" name="no_klasifikasi" id="no_klasifikasi" value="{{ old('no_klasifikasi', $book->no_klasifikasi) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="penerbit" class="block text-sm font-medium text-slate-700 mb-1.5">Penerbit</label>
                    <input type="text" name="penerbit" id="penerbit" value="{{ old('penerbit', $book->penerbit) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="kota" class="block text-sm font-medium text-slate-700 mb-1.5">Kota Penerbit</label>
                    <input type="text" name="kota" id="kota" value="{{ old('kota', $book->kota) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="tahun_terbit" class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit', $book->tahun_terbit) }}" maxlength="4"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="isbn" class="block text-sm font-medium text-slate-700 mb-1.5">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all @error('isbn') border-red-300 @enderror">
                    @error('isbn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jumlah_buku" class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Buku <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_buku" id="jumlah_buku" value="{{ old('jumlah_buku', $book->jumlah_buku) }}" min="1" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                    <p class="text-xs text-slate-400 mt-1">Stok saat ini: {{ $book->stok }}</p>
                </div>
                <div>
                    <label for="kategori" class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $book->kategori) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="perolehan" class="block text-sm font-medium text-slate-700 mb-1.5">Perolehan</label>
                    <input type="text" name="perolehan" id="perolehan" value="{{ old('perolehan', $book->perolehan) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="tanggal_diterima" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Diterima</label>
                    <input type="date" name="tanggal_diterima" id="tanggal_diterima"
                           value="{{ old('tanggal_diterima', $book->tanggal_diterima?->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
            </div>

            @if($book->barcode)
            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl">
                <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z"/></svg>
                <div><p class="text-xs text-slate-500">Barcode</p><p class="text-sm font-mono font-medium text-slate-700">{{ $book->barcode }}</p></div>
                <a href="{{ route('books.show', $book) }}" class="ml-auto text-xs text-indigo-600 hover:underline">Lihat & Print →</a>
            </div>
            @endif

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all">Update Buku</button>
                <a href="{{ route('books.show', $book) }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
