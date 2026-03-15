@extends('layouts.public')

@section('content')
<section class="max-w-[1200px] mx-auto px-6 py-10 min-h-[70vh]">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 font-serif">Profil Saya</h1>
        <p class="text-gray-500 mt-2">Selamat datang, {{ $member->nama }}. Berikut adalah status keanggotaan dan peminjaman Anda.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <!-- SIDEBAR: KARTU ANGGOTA (QR 1) -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-3xl p-6 shadow-xl shadow-green-900/5 relative overflow-hidden border border-green-50">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-green-50 rounded-full blur-3xl opacity-50 pointer-events-none"></div>

                <div class="flex items-center gap-4 mb-6 relative z-10">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-md">
                        {{ strtoupper(substr($member->nama, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">{{ $member->nama }}</h2>
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-800 uppercase tracking-wider">
                            {{ $member->status }}
                        </span>
                    </div>
                </div>

                <div class="space-y-3 mb-8 relative z-10">
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Nomor Induk / NIS</p>
                        <p class="text-sm font-medium text-gray-800 font-mono">{{ $member->nis ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Kelas / Instansi</p>
                        <p class="text-sm font-medium text-gray-800">{{ $member->kelas ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Bergabung Pada</p>
                        <p class="text-sm font-medium text-gray-800">{{ $member->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <!-- QR 1: Kartu Anggota -->
                @if($member->qr_code)
                <div class="bg-green-50/50 rounded-2xl p-5 text-center border border-green-100/50 relative z-10">
                    <p class="text-xs text-green-700 font-medium mb-3">Tunjukkan QR ini saat meminjam</p>
                    <div class="bg-white p-3 rounded-xl inline-block shadow-sm" id="qrcode-container"></div>
                    <p class="mt-2 text-[10px] font-mono font-bold text-green-800/60 tracking-widest">{{ $member->qr_code }}</p>
                </div>
                @endif
                
            </div>
        </div>

        <!-- MAIN SECTION: RIWAYAT PEMINJAMAN -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-bold text-gray-900">Riwayat Peminjaman Buku</h2>
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">{{ $borrowings->count() }} Peminjaman</span>
                </div>

                @if($borrowings->isEmpty())
                <div class="text-center py-12 px-4 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Riwayat</h3>
                    <p class="text-sm text-gray-500">Anda belum pernah melakukan peminjaman buku.</p>
                </div>
                @else
                <div class="space-y-4">
                    @foreach($borrowings as $borrowing)
                        @php
                            $isOverdue = $borrowing->status === 'dipinjam' && now()->gt($borrowing->tanggal_kembali);
                        @endphp
                        
                        <div class="p-5 rounded-2xl border transition-all duration-300 
                            {{ $borrowing->status === 'dipinjam' ? ($isOverdue ? 'bg-red-50/30 border-red-100 hover:border-red-300' : 'bg-green-50/30 border-green-100 hover:border-green-300') : 'bg-gray-50/50 border-gray-100 hover:border-gray-200' }}">
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between">
                                <!-- Detail Buku -->
                                <div class="mb-4 sm:mb-0">
                                    <h3 class="font-bold text-gray-900 mb-1 flex items-center gap-2">
                                        {{ $borrowing->book->judul }}
                                        @if($borrowing->status === 'dipinjam')
                                            <span class="relative flex h-2 w-2">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                            </span>
                                        @endif
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                                        <span class="flex items-center gap-1.5 font-mono">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                            {{ $borrowing->book->barcode }}
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            Pinjam: {{ $borrowing->tanggal_pinjam->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Status & Denda -->
                                <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2">
                                    @if($borrowing->status === 'dipinjam')
                                        @if($isOverdue)
                                            <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-lg uppercase tracking-wide">Terlambat</span>
                                            <span class="text-xs text-red-600 font-medium">Lewat Tenggat</span>
                                        @else
                                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-lg uppercase tracking-wide">Dipinjam</span>
                                            <span class="text-xs text-green-600 font-medium">Batas: {{ $borrowing->tanggal_kembali->format('d M Y') }}</span>
                                        @endif
                                    @else
                                        <span class="px-3 py-1 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg uppercase tracking-wide">Dikembalikan</span>
                                        <span class="text-xs text-gray-500 font-medium">Pada: {{ \Carbon\Carbon::parse($borrowing->tanggal_dikembalikan)->format('d M Y') }}</span>
                                    @endif

                                    @if($borrowing->denda > 0)
                                        <span class="px-2 py-0.5 mt-1 bg-red-50 border border-red-100 text-red-600 text-[10px] font-bold rounded">
                                            Denda: Rp {{ number_format($borrowing->denda, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- QR 2: Pengembalian Buku (hanya jika masih dipinjam) --}}
                            @if($borrowing->status === 'dipinjam')
                            <div class="mt-4 pt-4 border-t border-dashed {{ $isOverdue ? 'border-red-200' : 'border-green-200' }}">
                                <button
                                    onclick="toggleReturnQR({{ $borrowing->id }})"
                                    class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-white hover:bg-green-50 border border-green-200 hover:border-green-400 text-green-700 text-sm font-semibold rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                                    </svg>
                                    Kembalikan Buku — Tunjukkan QR ke Petugas
                                </button>

                                <div id="return-qr-{{ $borrowing->id }}" class="hidden mt-4 text-center">
                                    @if($borrowing->return_qr)
                                        <p class="text-xs text-slate-500 mb-3 font-medium">Tunjukkan QR ini kepada petugas untuk proses pengembalian</p>
                                        <div class="inline-block bg-white border-2 border-green-100 rounded-2xl p-4 shadow-sm">
                                            <div id="qr-return-{{ $borrowing->id }}" data-qr="{{ $borrowing->return_qr }}" class="mx-auto"></div>
                                            <p class="text-[10px] font-mono text-slate-400 mt-2 tracking-widest">{{ $borrowing->return_qr }}</p>
                                        </div>
                                    @else
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                                            <p>QR pengembalian belum tersedia.</p>
                                            <p class="text-xs mt-1">Hubungi petugas perpustakaan untuk bantuan.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                        </div>
                    @endforeach
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

{{-- QR Code Library --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const renderedQRs = {};

    document.addEventListener("DOMContentLoaded", function() {
        // QR 1: Kartu Anggota (untuk meminjam)
        @if($member->qr_code)
        new QRCode(document.getElementById("qrcode-container"), {
            text: "{{ $member->qr_code }}",
            width: 130, height: 130,
            colorDark: "#065f46",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
        @endif
    });

    // Toggle QR Pengembalian per buku
    function toggleReturnQR(borrowingId) {
        const panel = document.getElementById('return-qr-' + borrowingId);
        if (!panel) return;
        if (panel.classList.contains('hidden')) {
            panel.classList.remove('hidden');
            // Render QR hanya sekali
            const qrEl = document.getElementById('qr-return-' + borrowingId);
            if (qrEl && !renderedQRs[borrowingId] && qrEl.dataset.qr) {
                new QRCode(qrEl, {
                    text: qrEl.dataset.qr,
                    width: 160, height: 160,
                    colorDark: "#1e293b",
                    colorLight: "#ffffff",
                    correctLevel: QRCode.CorrectLevel.M
                });
                renderedQRs[borrowingId] = true;
            }
        } else {
            panel.classList.add('hidden');
        }
    }
</script>
@endsection
