@extends('layouts.app')
@section('title', 'Pinjam Buku')
@section('header', 'Pinjam Buku Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <form method="POST" action="{{ route('borrowings.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="book_id" class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Buku <span class="text-red-500">*</span></label>
                    <select name="book_id" id="book_id" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('book_id') border-red-300 @enderror">
                        <option value="">-- Pilih Buku --</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>
                                {{ $book->judul }} — {{ $book->pengarang }} (Stok: {{ $book->stok }})
                            </option>
                        @endforeach
                    </select>
                    @error('book_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="member_id" class="block text-sm font-medium text-slate-700 mb-1.5">Pilih Anggota <span class="text-red-500">*</span></label>
                    <select name="member_id" id="member_id" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('member_id') border-red-300 @enderror">
                        <option value="">-- Pilih Anggota --</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                {{ $member->nama }} — {{ $member->nis }} ({{ $member->kelas }})
                            </option>
                        @endforeach
                    </select>
                    @error('member_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_pinjam" class="block text-sm font-medium text-slate-700 mb-1.5">Tanggal Pinjam <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('tanggal_pinjam') border-red-300 @enderror">
                    @error('tanggal_pinjam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="tanggal_kembali" class="block text-sm font-medium text-slate-700 mb-1.5">Batas Pengembalian <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_kembali" id="tanggal_kembali" value="{{ old('tanggal_kembali', date('Y-m-d', strtotime('+7 days'))) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('tanggal_kembali') border-red-300 @enderror">
                    @error('tanggal_kembali') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    <span class="font-medium">Informasi</span>
                </div>
                <p>Denda keterlambatan: <span class="font-semibold">Rp 1.000</span> per hari setelah batas pengembalian.</p>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl shadow-sm transition-all hover:shadow-md">
                    Proses Peminjaman
                </button>
                <a href="{{ route('borrowings.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
