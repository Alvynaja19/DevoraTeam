@extends('layouts.app')
@section('title', 'Tambah Buku')
@section('header', 'Tambah Buku Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <form method="POST" action="{{ route('books.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-1.5">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all @error('judul') border-red-300 @enderror">
                    @error('judul') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pengarang" class="block text-sm font-medium text-slate-700 mb-1.5">Pengarang / Penulis <span class="text-red-500">*</span></label>
                    <input type="text" name="pengarang" id="pengarang" value="{{ old('pengarang') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                    @error('pengarang') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="no_klasifikasi" class="block text-sm font-medium text-slate-700 mb-1.5">No. Klasifikasi</label>
                    <input type="text" name="no_klasifikasi" id="no_klasifikasi" value="{{ old('no_klasifikasi') }}" placeholder="cth: 360, 923.2"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="penerbit" class="block text-sm font-medium text-slate-700 mb-1.5">Penerbit</label>
                    <input type="text" name="penerbit" id="penerbit" value="{{ old('penerbit') }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="kota" class="block text-sm font-medium text-slate-700 mb-1.5">Kota Penerbit</label>
                    <input type="text" name="kota" id="kota" value="{{ old('kota') }}" placeholder="Jakarta, Surabaya..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="tahun_terbit" class="block text-sm font-medium text-slate-700 mb-1.5">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" id="tahun_terbit" value="{{ old('tahun_terbit') }}" maxlength="4" placeholder="{{ date('Y') }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="isbn" class="block text-sm font-medium text-slate-700 mb-1.5">ISBN</label>
                    <input type="text" name="isbn" id="isbn" value="{{ old('isbn') }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all @error('isbn') border-red-300 @enderror">
                    @error('isbn') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="jumlah_buku" class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Buku <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_buku" id="jumlah_buku" value="{{ old('jumlah_buku', 1) }}" min="1" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="kategori" class="block text-sm font-medium text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" name="kategori" id="kategori" value="{{ old('kategori') }}" placeholder="Novel, Pelajaran, dll"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="perolehan" class="block text-sm font-medium text-slate-700 mb-1.5">Perolehan</label>
                    <input type="text" name="perolehan" id="perolehan" value="{{ old('perolehan') }}" placeholder="Pembelian, Sumbangan..."
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
                <div>
                    <label for="tanggal_diterima" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Diterima</label>
                    <input type="date" name="tanggal_diterima" id="tanggal_diterima" value="{{ old('tanggal_diterima') }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 transition-all">
                </div>
            </div>

            <div class="flex items-center gap-2.5 px-4 py-3 bg-blue-50 border border-blue-100 rounded-xl text-xs text-blue-600">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Barcode unik akan dibuat otomatis setelah buku disimpan.
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all">Simpan Buku</button>
                <a href="{{ route('books.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
