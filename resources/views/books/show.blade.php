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
                <a href="{{ route('books.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Penerbit</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->penerbit ?? '-' }}</p>@if($book->kota)<p class="text-xs text-slate-400 mt-0.5">{{ $book->kota }}</p>@endif</div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Tahun</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->tahun_terbit ?? '-' }}</p></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">ISBN</p><p class="text-sm font-medium text-slate-800 mt-1 font-mono text-xs">{{ $book->isbn ?? '-' }}</p></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Kategori</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->kategori ?? '-' }}</p></div>
            @if($book->no_klasifikasi)<div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">No. Klasifikasi</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->no_klasifikasi }}</p></div>@endif
            @if($book->perolehan)<div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Perolehan</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->perolehan }}</p></div>@endif
            @if($book->tanggal_diterima)<div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Diterima</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->tanggal_diterima->format('d M Y') }}</p></div>@endif
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Jumlah</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $book->jumlah_buku }} eksemplar</p></div>
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Stok Tersedia</p><p class="text-sm font-bold mt-1 {{ $book->stok > 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $book->stok }} eksemplar</p></div>
        </div>
    </div>

    {{-- Barcode Card --}}
    @if($book->barcode)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Barcode Buku</h3>
            <button onclick="printBarcode()" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                Print Label
            </button>
        </div>
        <div id="barcodeDisplay" class="flex flex-col items-center py-4 bg-slate-50 rounded-xl">
            <svg id="bookBarcode"></svg>
            <p class="mt-2 text-sm font-mono text-slate-600 font-medium">{{ $book->barcode }}</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $book->judul }}</p>
        </div>
    </div>
    @endif

    {{-- Riwayat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Riwayat Peminjaman</h3></div>
        <div class="divide-y divide-slate-50">
            @forelse($book->borrowings as $b)
                <div class="px-6 py-3.5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $b->member->nama }}</p>
                        <p class="text-xs text-slate-500">{{ $b->tanggal_pinjam->format('d M Y') }} — {{ $b->tanggal_kembali->format('d M Y') }}</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $b->status === 'dipinjam' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ ucfirst($b->status) }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center"><p class="text-sm text-slate-400">Belum ada riwayat peminjaman</p></div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($book->barcode)
    JsBarcode("#bookBarcode", "{{ $book->barcode }}", { format:"CODE128", width:2.5, height:80, displayValue:true, fontSize:14, margin:10, lineColor:"#1e293b" });
    @endif
});
function printBarcode() {
    const w = window.open('', '_blank', 'width=400,height=280');
    const svg = document.getElementById('bookBarcode').outerHTML;
    w.document.write(`<html><head><title>Label Buku</title><style>body{font-family:sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:16px;}.title{font-size:11px;color:#475569;max-width:200px;text-align:center;margin-top:6px;}.code{font-size:10px;font-family:monospace;color:#94a3b8;margin-top:2px;text-align:center;}@media print{body{padding:8px}}</style></head><body>${svg}<p class="title">{{ addslashes($book->judul) }}</p><p class="code">{{ $book->barcode }}</p><script>window.onload=()=>window.print()<\/script></body></html>`);
    w.document.close();
}
</script>
@endsection
