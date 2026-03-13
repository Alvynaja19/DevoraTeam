@extends('layouts.app')
@section('title', 'Tambah Anggota')
@section('header', 'Tambah Anggota Baru')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <form method="POST" action="{{ route('members.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label for="nama" class="block text-sm font-medium text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('nama') border-red-300 @enderror">
                    @error('nama') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="nis" class="block text-sm font-medium text-slate-700 mb-1.5">NIS <span class="text-red-500">*</span></label>
                    <input type="text" name="nis" id="nis" value="{{ old('nis') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('nis') border-red-300 @enderror">
                    @error('nis') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="kelas" class="block text-sm font-medium text-slate-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}" required placeholder="Contoh: X-1"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all @error('kelas') border-red-300 @enderror">
                    @error('kelas') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="alamat" class="block text-sm font-medium text-slate-700 mb-1.5">Alamat</label>
                    <input type="text" name="alamat" id="alamat" value="{{ old('alamat') }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
                </div>
                <div>
                    <label for="telepon" class="block text-sm font-medium text-slate-700 mb-1.5">Telepon</label>
                    <input type="text" name="telepon" id="telepon" value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all hover:shadow-md">
                    Simpan Anggota
                </button>
                <a href="{{ route('members.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
