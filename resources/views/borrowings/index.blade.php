@extends('layouts.app')
@section('title', 'Data Peminjaman')
@section('header', 'Data Peminjaman')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row gap-4 justify-between">
        <form method="GET" action="{{ route('borrowings.index') }}" class="flex flex-1 max-w-lg gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, nama peminjam..."
                       class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400 transition-all">
            </div>
            <select name="status" onchange="this.form.submit()" class="px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-400">
                <option value="">Semua Status</option>
                <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
            </select>
            <button type="submit" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-xl transition-colors">Cari</button>
        </form>
        <a href="{{ route('borrowings.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl shadow-sm transition-all hover:shadow-md">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Pinjam Buku
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3.5">Buku</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3.5">Peminjam</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3.5">Tanggal Pinjam</th>
                        <th class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3.5">Batas Kembali</th>
                        <th class="text-center text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3.5">Status</th>
                        <th class="text-right text-xs font-semibold text-slate-500 uppercase tracking-wider px-6 py-3.5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($borrowings as $b)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-800">{{ $b->book->judul }}</p>
                                <p class="text-xs text-slate-400">{{ $b->book->pengarang }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-700">{{ $b->member->nama }}</p>
                                <p class="text-xs text-slate-400">{{ $b->member->nis }} — {{ $b->member->kelas }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $b->tanggal_pinjam->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm {{ $b->status === 'dipinjam' && $b->tanggal_kembali->lt(now()) ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                                    {{ $b->tanggal_kembali->format('d M Y') }}
                                </span>
                                @if($b->status === 'dipinjam' && $b->tanggal_kembali->lt(now()))
                                    <p class="text-xs text-red-500 font-medium">Terlambat {{ now()->diffInDays($b->tanggal_kembali) }} hari</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($b->status === 'dipinjam')
                                    @if($b->tanggal_kembali->lt(now()))
                                        <span class="px-2.5 py-1 bg-red-50 text-red-700 text-xs font-medium rounded-full animate-pulse">Terlambat</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-medium rounded-full">Dipinjam</span>
                                    @endif
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full">Dikembalikan</span>
                                    @if($b->denda > 0)
                                        <p class="text-xs text-red-500 mt-1">Denda: Rp {{ number_format($b->denda, 0, ',', '.') }}</p>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($b->status === 'dipinjam')
                                    <form method="POST" action="{{ route('borrowings.return', $b) }}" onsubmit="return confirm('Konfirmasi pengembalian buku?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-medium rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Kembalikan
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-slate-400">{{ $b->tanggal_dikembalikan?->format('d M Y') }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-slate-200 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                <p class="text-slate-500 font-medium">Belum ada data peminjaman</p>
                                <p class="text-sm text-slate-400 mt-1">Silakan buat peminjaman baru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($borrowings->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $borrowings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
