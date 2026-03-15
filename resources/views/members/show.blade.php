@extends('layouts.app')
@section('title', $member->nama)
@section('header', 'Detail Anggota')

@section('content')
<div class="max-w-3xl space-y-6">
    {{-- Member Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-sm">
                    {{ strtoupper(substr($member->nama, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        {{ $member->nama }}
                        @if($member->status === 'siswa')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wider">Siswa</span>
                        @elseif($member->status === 'guru')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 uppercase tracking-wider">Guru</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-700 uppercase tracking-wider">Umum</span>
                        @endif
                    </h2>
                    @if($member->nis || $member->kelas)
                        <p class="text-slate-500 mt-1 text-sm flex items-center gap-2">
                            @if($member->kelas)<span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded">{{ $member->kelas }}</span>@endif
                            @if($member->nis)<span class="font-mono text-xs">NIS: {{ $member->nis }}</span>@endif
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 text-amber-700 hover:bg-amber-100 text-sm font-medium rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                    Edit
                </a>
                <a href="{{ route('members.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-colors">Kembali</a>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Status</p><p class="text-sm font-bold text-slate-800 mt-1 uppercase">{{ $member->status }}</p></div>
            @if($member->nis)<div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">NIS</p><p class="text-sm font-medium text-slate-800 mt-1 font-mono">{{ $member->nis }}</p></div>@endif
            @if($member->kelas)<div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Kelas</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $member->kelas }}</p></div>@endif
            @if($member->telepon)<div class="bg-slate-50 rounded-xl p-4"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Telepon</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $member->telepon }}</p></div>@endif
            @if($member->alamat)<div class="bg-slate-50 rounded-xl p-4 sm:col-span-2"><p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Alamat</p><p class="text-sm font-medium text-slate-800 mt-1">{{ $member->alamat }}</p></div>@endif
        </div>
    </div>

    {{-- QR Code Anggota --}}
    @if($member->qr_code)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">QR Code Anggota</h3>
            <button onclick="printMemberCard()" class="inline-flex items-center gap-2 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659"/></svg>
                Print Kartu
            </button>
        </div>
        <div class="flex flex-col items-center py-4 bg-slate-50 rounded-xl">
            <div id="member-qr-display" class="p-3 bg-white rounded-xl shadow-sm"></div>
            <p class="mt-3 text-sm font-mono text-slate-600 font-medium">{{ $member->qr_code }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Tunjukkan QR ini saat meminjam buku</p>
        </div>
    </div>
    @endif

    {{-- Riwayat Peminjaman --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100"><h3 class="font-semibold text-slate-800">Riwayat Peminjaman</h3></div>
        <div class="divide-y divide-slate-50">
            @forelse($member->borrowings as $b)
                <div class="px-6 py-3.5 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $b->book->judul }}</p>
                        <p class="text-xs text-slate-500">{{ $b->tanggal_pinjam->format('d M Y') }} — {{ $b->tanggal_kembali->format('d M Y') }}</p>
                        @if($b->denda > 0)<p class="text-xs text-red-500">Denda: Rp {{ number_format($b->denda, 0, ',', '.') }}</p>@endif
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $b->status === 'dipinjam' ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700' }}">{{ ucfirst($b->status) }}</span>
                </div>
            @empty
                <div class="px-6 py-8 text-center"><p class="text-sm text-slate-400">Belum ada riwayat peminjaman</p></div>
            @endforelse
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($member->qr_code)
    new QRCode(document.getElementById('member-qr-display'), {
        text: "{{ $member->qr_code }}",
        width: 180, height: 180,
        colorDark: '#1e293b', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
    });
    @endif
});

function printMemberCard() {
    const qrEl = document.getElementById('member-qr-display').innerHTML;
    const w = window.open('', '_blank', 'width=400,height=320');
    w.document.write(`<html><head><title>Kartu Anggota</title>
    <style>body{font-family:sans-serif;display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;}
    .card{border:2px solid #e2e8f0;border-radius:12px;padding:16px 24px;text-align:center;max-width:220px;}
    .title{font-size:10px;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:#64748b;margin-bottom:8px;}
    .nama{font-size:15px;font-weight:700;color:#1e293b;margin:8px 0 2px;}
    .info{font-size:11px;color:#64748b;margin:2px 0;}
    .code{font-size:9px;font-family:monospace;color:#94a3b8;margin-top:6px;}
    @media print{body{padding:8px}.card{border-color:#cbd5e1}}</style>
    </head><body><div class="card">
    <p class="title">Kartu Anggota Perpustakaan</p>
    ${qrEl}
    <p class="nama">{{ addslashes($member->nama) }}</p>
    <p class="info">{{ $member->kelas }} · NIS: {{ $member->nis }}</p>
    <p class="code">{{ $member->qr_code }}</p>
    </div><script>window.onload=()=>window.print()<\/script></body></html>`);
    w.document.close();
}
</script>
@endsection
