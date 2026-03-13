@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<x-common.page-breadcrumb pageTitle="Dashboard" />

<div class="space-y-6">
    {{-- Welcome Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Selamat Datang, {{ Auth::user()->name }}! 👋
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Sistem E-Library SMAN 4 Jember — Kelola data buku, anggota, dan peminjaman dengan mudah.
        </p>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        {{-- Total Buku --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/[0.12]">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Buku</p>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ \App\Models\Book::sum('jumlah_buku') }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- Total Anggota --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/[0.12]">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Anggota</p>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ \App\Models\Member::count() }}
                    </h4>
                </div>
            </div>
        </div>

        {{-- Peminjaman Aktif --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 dark:bg-brand-500/[0.12]">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Peminjaman Aktif</p>
                    <h4 class="text-2xl font-bold text-gray-800 dark:text-white/90">
                        {{ \App\Models\Borrowing::where('status', 'dipinjam')->count() }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <a href="{{ route('books.index') }}"
           class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all hover:shadow-theme-md hover:border-brand-200 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-500/30">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white/90 group-hover:text-brand-500">Manajemen Buku</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tambah, edit, dan kelola buku</p>
                </div>
            </div>
        </a>

        <a href="{{ route('members.index') }}"
           class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all hover:shadow-theme-md hover:border-brand-200 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-500/30">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white/90 group-hover:text-brand-500">Data Anggota</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Kelola data anggota</p>
                </div>
            </div>
        </a>

        <a href="{{ route('borrowings.index') }}"
           class="group rounded-2xl border border-gray-200 bg-white p-5 transition-all hover:shadow-theme-md hover:border-brand-200 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-brand-500/30">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/[0.12]">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800 dark:text-white/90 group-hover:text-brand-500">Peminjaman</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Atur peminjaman & pengembalian</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection