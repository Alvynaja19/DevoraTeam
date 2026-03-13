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
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('judul') border-red-300 @enderror">
                    @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pengarang" class="block text-sm font-medium text-slate-700 mb-1.5">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" name="pengarang" id="pengarang" value="{{ old('pengarang', $book->pengarang) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('pengarang') border-red-300 @enderror">
                    @error('pengarang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="penerbit" class="block text-sm font-medium text-slate-700 mb-1.5">Penerbit</label>
                    <input type="text" name="penerbit" id="penerbit" value="{{ old('penerbit', $book->penerbit) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
                </div>
                <div>
                    <label for="tahun_terbit" class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit', $book->tahun_terbit) }}" maxlength="4"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
                </div>
                <div>
                    <label for="isbn" class="block text-sm font-medium text-slate-700 mb-1.5">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('isbn') border-red-300 @enderror">
                    @error('isbn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jumlah_buku" class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Buku <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_buku" id="jumlah_buku" value="{{ old('jumlah_buku', $book->jumlah_buku) }}" min="1" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
                    <p class="text-xs text-slate-400 mt-1">Stok saat ini: {{ $book->stok }}</p>
                </div>
                <div>
                    <label for="kategori" class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori', $book->kategori) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all hover:shadow-md">
                    Update Buku
                </button>
                <a href="{{ route('books.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
