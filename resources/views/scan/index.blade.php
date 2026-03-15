@extends('layouts.app')
@section('title', 'Peminjaman Buku')
@section('header', 'Peminjaman Buku')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- STEP INDICATOR --}}
    <div class="flex items-center gap-2 text-xs font-semibold overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-hide">
        <div id="step-indicator-1" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl shadow-sm transition-all whitespace-nowrap">
            <span class="w-5 h-5 bg-white text-green-600 rounded-full flex items-center justify-center text-[10px] font-bold">1</span>
            Data Anggota
        </div>
        <div class="flex-1 min-w-[20px] h-px bg-slate-200"></div>
        <div id="step-indicator-2" class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-500 rounded-xl transition-all whitespace-nowrap">
            <span class="w-5 h-5 bg-slate-400 text-white rounded-full flex items-center justify-center text-[10px] font-bold">2</span>
            Data Buku
        </div>
        <div class="flex-1 min-w-[20px] h-px bg-slate-200"></div>
        <div id="step-indicator-3" class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-500 rounded-xl transition-all whitespace-nowrap">
            <span class="w-5 h-5 bg-slate-400 text-white rounded-full flex items-center justify-center text-[10px] font-bold">3</span>
            Konfirmasi
        </div>
        <div class="flex-1 min-w-[20px] h-px bg-slate-200"></div>
        <div id="step-indicator-4" class="flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-500 rounded-xl transition-all whitespace-nowrap">
            <span class="w-5 h-5 bg-slate-400 text-white rounded-full flex items-center justify-center text-[10px] font-bold">4</span>
            Selesai
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- STEP 1: SCAN ANGGOTA --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div id="step-1" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 relative overflow-hidden transition-all duration-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-bl-full -z-10 opacity-50"></div>
        
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-sm font-bold shadow-sm">1</span>
            Scan QR Anggota
        </h3>

        {{-- Input Area --}}
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-green-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    </div>
                    <input type="text" id="member-input"
                           placeholder="Scan QR anggota atau ketik manual..."
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all font-mono"
                           autofocus>
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleCamera('member')" id="btn-cam-member"
                            class="flex-1 sm:flex-none px-4 py-3.5 bg-slate-100 hover:bg-green-50 hover:text-green-700 hover:border-green-200 border-2 border-transparent text-slate-600 rounded-xl transition-all flex items-center justify-center gap-2 text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                        <span class="sm:hidden">Kamera</span>
                    </button>
                    <button onclick="lookupMember()" class="flex-1 sm:flex-none px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm hover:shadow transition-all text-sm font-semibold">
                        Cari
                    </button>
                </div>
            </div>

            {{-- Camera Viewfinder --}}
            <div id="camera-member" class="hidden transform transition-all">
                <div id="reader-member" class="rounded-2xl overflow-hidden border-4 border-slate-100 shadow-inner" style="width:100%"></div>
                <button onclick="stopCamera('member')" class="mt-3 w-full py-2.5 text-sm font-medium text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">Tutup Kamera</button>
            </div>

            {{-- Error --}}
            <div id="member-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span id="member-error-text" class="leading-relaxed"></span>
            </div>
        </div>

        {{-- Hasil Anggota --}}
        <div id="member-result" class="hidden mt-6 bg-slate-50 border-2 border-green-100 rounded-2xl p-5 transition-all">
            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 text-green-700 rounded-xl flex items-center justify-center font-bold text-lg" id="member-avatar">A</div>
                    <div>
                        <p class="font-bold text-slate-800 text-lg" id="member-nama"></p>
                        <p class="text-xs font-medium text-slate-500 mt-0.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                            <span id="member-info"></span>
                        </p>
                    </div>
                </div>
                <span id="member-pinjam-badge" class="text-xs font-semibold px-3 py-1.5 rounded-full whitespace-nowrap"></span>
            </div>
            
            {{-- Daftar buku yg sedang dipinjam --}}
            <div id="active-borrowings" class="hidden mt-5 pt-4 border-t border-slate-200">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Buku yg Sedang Dipinjam</p>
                <div id="borrowings-list" class="space-y-2"></div>
            </div>
            
            <button onclick="resetMember()" class="mt-4 w-full py-2.5 text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-200/50 rounded-xl transition-colors">Ganti Anggota Peminjam</button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- STEP 2: SCAN BUKU --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div id="step-2" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 opacity-50 pointer-events-none transition-all duration-300">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center text-sm font-bold shadow-sm" id="step2-num">2</span>
            Scan Barcode Buku
        </h3>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-green-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                    </div>
                    <input type="text" id="book-input"
                           placeholder="Scan barcode buku atau ketik manual..."
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all font-mono">
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleCamera('book')" id="btn-cam-book"
                            class="flex-1 sm:flex-none px-4 py-3.5 bg-slate-100 hover:bg-green-50 hover:text-green-700 hover:border-green-200 border-2 border-transparent text-slate-600 rounded-xl transition-all flex items-center justify-center gap-2 text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                        <span class="sm:hidden">Kamera</span>
                    </button>
                    <button onclick="lookupBook()" class="flex-1 sm:flex-none px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm hover:shadow transition-all text-sm font-semibold">
                        Cari
                    </button>
                </div>
            </div>

            <div id="camera-book" class="hidden transform transition-all">
                <div id="reader-book" class="rounded-2xl overflow-hidden border-4 border-slate-100 shadow-inner" style="width:100%"></div>
                <button onclick="stopCamera('book')" class="mt-3 w-full py-2.5 text-sm font-medium text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">Tutup Kamera</button>
            </div>

            <div id="book-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span id="book-error-text" class="leading-relaxed"></span>
            </div>
        </div>

        <div id="book-result" class="hidden mt-6 bg-slate-50 border border-slate-200 rounded-2xl p-5 transition-all">
            <div class="flex items-start sm:items-center justify-between flex-col sm:flex-row gap-4">
                <div>
                    <p class="font-bold text-slate-800 text-lg" id="book-judul"></p>
                    <p class="text-sm text-slate-500 mt-1" id="book-pengarang"></p>
                    <p class="text-xs text-slate-400 mt-1 font-mono" id="book-barcode"></p>
                </div>
                <span id="book-stok-badge" class="text-xs font-semibold px-3 py-1.5 rounded-full whitespace-nowrap"></span>
            </div>
            <button onclick="resetBook()" class="mt-4 w-full py-2.5 text-sm font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-200/50 rounded-xl transition-colors">Ganti Buku</button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- STEP 3: KONFIRMASI & AKSI --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div id="step-3" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 opacity-50 pointer-events-none transition-all duration-300">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 bg-slate-100 text-slate-500 rounded-xl flex items-center justify-center text-sm font-bold shadow-sm">3</span>
            Konfirmasi Transaksi
        </h3>

        <div id="action-error" class="hidden mb-5 text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-start gap-2">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span id="action-error-text" class="leading-relaxed"></span>
        </div>

        <div id="confirm-summary" class="hidden bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-6 space-y-3 text-sm">
            <div class="grid grid-cols-2 gap-2 border-b border-slate-200 pb-3">
                <span class="text-slate-500 font-medium">Anggota Peminjam</span>
                <span id="cs-member" class="font-bold text-slate-800 text-right"></span>
            </div>
            <div class="grid grid-cols-2 gap-2 border-b border-slate-200 pb-3">
                <span class="text-slate-500 font-medium">Buku</span>
                <span id="cs-book" class="font-bold text-slate-800 text-right truncate"></span>
            </div>
            <div class="grid grid-cols-2 gap-2 border-b border-slate-200 pb-3">
                <span class="text-slate-500 font-medium">Tanggal Pinjam</span>
                <span id="cs-tgl" class="font-bold text-slate-800 text-right"></span>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-1">
                <span class="text-slate-500 font-medium flex items-center gap-1.5"><svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Estimasi Kembali</span>
                <span class="font-bold text-amber-600 text-right">+3 hari (otomatis)</span>
            </div>
        </div>

        <div class="flex justify-center">
            <button id="btn-pinjam" onclick="processBorrow()"
                    class="w-full sm:w-auto px-8 py-4 bg-slate-800 hover:bg-black text-white font-semibold rounded-xl shadow-lg shadow-slate-200 hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2.5 opacity-50 cursor-not-allowed">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Proses Peminjaman
            </button>
        </div>
        <p class="text-xs text-slate-400 mt-4 text-center font-medium" id="action-hint">Scan anggota dan buku terlebih dahulu u/ membuka kunci tombol</p>
    </div>

    {{-- ═══════════════════════════════════════════ --}}
    {{-- STEP 4: QR TRANSAKSI (HASIL) --}}
    {{-- ═══════════════════════════════════════════ --}}
    <div id="step-4" class="hidden bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="text-center">
            <div id="result-icon" class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 id="result-title" class="text-xl font-bold text-slate-800 mb-1"></h3>
            <p id="result-subtitle" class="text-sm text-slate-500 mb-6"></p>

            {{-- Info Transaksi --}}
            <div id="result-detail" class="bg-slate-50 rounded-xl p-4 text-sm text-left space-y-1.5 mb-5">
            </div>

            <div class="flex gap-3 justify-center">
                <button onclick="resetAll()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>

</div>

{{-- ═══ LIBRARIES ═══ --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
const CSRF = "{{ csrf_token() }}";
const state = {
    member: null,
    book: null,
    returnBorrowingId: null,
    cameras: {
        member: null,
        book: null
    }
};

// ─── STEP 1: ANGGOTA ─────────────────────────────────────────────────────────
document.getElementById('member-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') lookupMember();
});

async function lookupMember() {
    const code = document.getElementById('member-input').value.trim();
    if (!code) return;

    setError('member', null);
    try {
        const res = await fetch(`/scan/member?code=${encodeURIComponent(code)}`);
        const data = await res.json();
        if (!res.ok) { setError('member', data.error); return; }
        setMember(data);
    } catch (e) { setError('member', 'Gagal menghubungi server.'); }
}

function setMember(data) {
    state.member = data;
    state.returnBorrowingId = null;

    document.getElementById('member-avatar').textContent = data.nama.substring(0, 2).toUpperCase();
    document.getElementById('member-nama').textContent = data.nama;
    
    // Siapkan Info Label Status beserta NIS/Kelas jika ada
    let infoHtml = ``;
    if(data.status === 'siswa') {
        infoHtml += `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 uppercase tracking-wider mr-2">Siswa</span>`;
    } else if(data.status === 'guru') {
        infoHtml += `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 uppercase tracking-wider mr-2">Guru</span>`;
    } else {
        infoHtml += `<span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-700 uppercase tracking-wider mr-2">Umum</span>`;
    }

    let identitas = [];
    if(data.nis) identitas.push(`NIS: <span class="font-mono">${data.nis}</span>`);
    if(data.kelas) identitas.push(`${data.kelas}`);
    
    if(identitas.length > 0) {
        infoHtml += identitas.join(' &nbsp;&bull;&nbsp; ');
    } else {
        infoHtml += `<span class="italic text-slate-400">Tidak ada info tambahan</span>`;
    }
    
    document.getElementById('member-info').innerHTML = infoHtml;

    const badge = document.getElementById('member-pinjam-badge');
    const count = data.jumlah_pinjam;
    badge.className = `text-xs font-semibold px-3 py-1.5 rounded-full whitespace-nowrap ${count >= 2 ? 'bg-red-100 text-red-700' : count > 0 ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700'}`;
    badge.textContent = count === 0 ? 'Belum Ada Pinjaman' : `${count}/2 Buku Dipinjam`;

    const activeBorrowingsEl = document.getElementById('active-borrowings');
    const listEl = document.getElementById('borrowings-list');
    listEl.innerHTML = '';
    if (data.active_borrowings.length > 0) {
        activeBorrowingsEl.classList.remove('hidden');
        data.active_borrowings.forEach(b => {
            const el = document.createElement('div');
            el.className = `flex flex-col sm:flex-row sm:items-center justify-between bg-white border rounded-xl px-4 py-3 cursor-pointer hover:border-green-400 hover:shadow-md transition-all gap-2 sm:gap-4 ${b.terlambat ? 'border-red-200 bg-red-50/50' : 'border-slate-200'}`;
            el.innerHTML = `
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-700 line-clamp-1">${b.judul}</p>
                    <p class="text-xs text-slate-500 font-mono mt-0.5">${b.barcode}</p>
                </div>
                <div class="text-left sm:text-right">
                    <span class="inline-block px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider ${b.terlambat ? 'bg-red-100 text-red-600' : 'bg-slate-100 text-slate-600'}">${b.terlambat ? '⚠ Lewat Batas Waktu' : 'Kembali: ' + b.tanggal_kembali}</span>
                    <button class="block sm:inline-block w-full sm:w-auto mt-2 sm:mt-0 sm:ml-3 px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-semibold rounded-lg transition-colors" onclick="selectReturn(${b.id}, '${b.judul.replace(/'/g, "\\'")}')">Pilih u/ Dikembalikan</button>
                </div>`;
            listEl.appendChild(el);
        });
    } else {
        activeBorrowingsEl.classList.add('hidden');
    }

    document.getElementById('member-result').classList.remove('hidden');
    stopCamera('member');
    activateStep(2);
    updateConfirmSummary();
}

function selectReturn(borrowingId, judul) {
    state.returnBorrowingId = borrowingId;
    document.getElementById('book-input').value = judul;
    document.getElementById('book-judul').textContent = judul;
    document.getElementById('book-pengarang').textContent = 'Dipilih dari riwayat peminjaman';
    document.getElementById('book-barcode').textContent = '-';
    document.getElementById('book-stok-badge').className = 'hidden';
    
    document.getElementById('book-result').classList.remove('hidden');
    document.getElementById('book-error').classList.add('hidden');
    state.book = { id: null, judul: judul, forced_return: true };
    activateStep(3);
    updateConfirmSummary();
    enableActionButtons();
}

function resetMember() {
    state.member = null;
    state.returnBorrowingId = null;
    document.getElementById('member-input').value = '';
    document.getElementById('member-result').classList.add('hidden');
    document.getElementById('member-error').classList.add('hidden');
    deactivateStep(2); deactivateStep(3);
    resetBook();
    disableActionButtons();
}

// ─── STEP 2: BUKU ─────────────────────────────────────────────────────────────
document.getElementById('book-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') lookupBook();
});

async function lookupBook() {
    const code = document.getElementById('book-input').value.trim();
    if (!code) return;

    setError('book', null);
    try {
        const res = await fetch(`/scan/book?code=${encodeURIComponent(code)}`);
        const data = await res.json();
        if (!res.ok) { setError('book', data.error); return; }
        setBook(data);
    } catch (e) { setError('book', 'Gagal menghubungi server.'); }
}

function setBook(data) {
    state.book = data;
    state.returnBorrowingId = null;

    document.getElementById('book-judul').textContent = data.judul;
    document.getElementById('book-pengarang').textContent = data.pengarang ?? '';
    document.getElementById('book-barcode').textContent = `BC: ${data.barcode} ⸰ ISBN: ${data.isbn ?? '-'}`;

    const badge = document.getElementById('book-stok-badge');
    badge.className = `text-xs font-semibold px-3 py-1.5 rounded-full whitespace-nowrap ${data.stok > 3 ? 'bg-emerald-100 text-emerald-700' : data.stok > 0 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'}`;
    badge.textContent = `Tersedia: ${data.stok} dari ${data.jumlah_buku}`;

    document.getElementById('book-result').classList.remove('hidden');
    stopCamera('book');
    activateStep(3);
    updateConfirmSummary();
    enableActionButtons();
}

function resetBook() {
    state.book = null;
    document.getElementById('book-input').value = '';
    document.getElementById('book-result').classList.add('hidden');
    document.getElementById('book-error').classList.add('hidden');
    deactivateStep(3);
    disableActionButtons();
}

// ─── STEP 3: KONFIRMASI ───────────────────────────────────────────────────────
function updateConfirmSummary() {
    const el = document.getElementById('confirm-summary');
    if (state.member && state.book) {
        el.classList.remove('hidden');
        document.getElementById('cs-member').textContent = `${state.member.nama} (${state.member.nis})`;
        document.getElementById('cs-book').textContent = state.book.judul;
        document.getElementById('cs-tgl').textContent = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    } else {
        el.classList.add('hidden');
    }
}

function enableActionButtons() {
    if (!state.member || !state.book) return;
    const pinjam = document.getElementById('btn-pinjam');
    pinjam.classList.remove('opacity-50', 'cursor-not-allowed');
    document.getElementById('action-hint').textContent = '💡 Pilih aksi transaksi di atas';
    document.getElementById('action-hint').classList.add('text-green-600');
}

function disableActionButtons() {
    document.getElementById('btn-pinjam').classList.add('opacity-50', 'cursor-not-allowed');
    document.getElementById('action-hint').textContent = 'Scan anggota dan buku terlebih dahulu u/ membuka kunci tombol';
    document.getElementById('action-hint').classList.remove('text-green-600');
}

async function processBorrow() {
    if (!state.member || !state.book) return;
    setError('action', null);
    try {
        const res = await fetch('/scan/borrow', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ member_id: state.member.id, book_id: state.book.id })
        });
        const data = await res.json();
        if (!res.ok) { setError('action', data.error); return; }
        showResult('pinjam', data);
    } catch (e) { setError('action', 'Gagal menghubungi server.'); }
}


// ─── STEP 4: HASIL ────────────────────────────────────────────────────────────
let lastResultData = null;

function showResult(type, data) {
    lastResultData = { type, data };
    document.getElementById('step-4').classList.remove('hidden');
    setTimeout(() => document.getElementById('step-4').scrollIntoView({ behavior: 'smooth', block: 'center' }), 100);

    const icon = document.getElementById('result-icon');
    const title = document.getElementById('result-title');
    const subtitle = document.getElementById('result-subtitle');
    const detail = document.getElementById('result-detail');

    icon.className = 'w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner border-4 border-white';
    icon.querySelector('svg').setAttribute('class', 'w-10 h-10 text-green-600');
    title.textContent = 'Peminjaman Berhasil!';
    subtitle.textContent = `${data.buku} dipinjam oleh ${data.member}`;
    detail.innerHTML = `
        <div class="grid grid-cols-2 gap-2 border-b border-white pb-2"><span class="text-slate-500 font-medium">Anggota</span><span class="font-bold text-right">${data.member}</span></div>
        <div class="grid grid-cols-2 gap-2 border-b border-white pb-2"><span class="text-slate-500 font-medium">Buku</span><span class="font-bold text-right">${data.buku}</span></div>
        <div class="grid grid-cols-2 gap-2 border-b border-white pb-2"><span class="text-slate-500 font-medium">Tgl Pinjam</span><span class="font-bold text-right">${data.tanggal_pinjam}</span></div>
        <div class="grid grid-cols-2 gap-2"><span class="text-slate-500 font-medium">Batas Kembali</span><span class="font-bold text-amber-600 text-right">${data.tanggal_kembali}</span></div>`;

    activateStepDone(4);
}


function resetAll() {
    state.member = null; state.book = null; state.returnBorrowingId = null;
    lastResultData = null;
    document.getElementById('member-input').value = '';
    document.getElementById('book-input').value = '';
    document.getElementById('member-result').classList.add('hidden');
    document.getElementById('book-result').classList.add('hidden');
    document.getElementById('member-error').classList.add('hidden');
    document.getElementById('book-error').classList.add('hidden');
    document.getElementById('action-error').classList.add('hidden');
    document.getElementById('confirm-summary').classList.add('hidden');
    document.getElementById('step-4').classList.add('hidden');
    deactivateStep(2); deactivateStep(3); deactivateStep(4);
    activateStep(1, true);
    disableActionButtons();
    setTimeout(() => {
        window.scrollTo({top: 0, behavior: 'smooth'});
        document.getElementById('member-input').focus();
    }, 100);
}

// ─── KAMERA ───────────────────────────────────────────────────────────────────
function toggleCamera(target) {
    const camEl = document.getElementById(`camera-${target}`);
    if (camEl.classList.contains('hidden')) {
        camEl.classList.remove('hidden');
        startCamera(target);
    } else {
        stopCamera(target);
    }
}

function startCamera(target) {
    const readerId = `reader-${target}`;
    if (state.cameras[target]) return;
    const scanner = new Html5Qrcode(readerId);
    state.cameras[target] = scanner;
    scanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 280, height: 180 } },
        (code) => {
            document.getElementById(`${target}-input`).value = code;
            stopCamera(target);
            if (target === 'member') lookupMember();
            else lookupBook();
        },
        () => {}
    ).catch(() => setError(target, 'Tidak bisa mengakses kamera perangkat Anda. Pastikan izin kamera sudah diberikan.'));
}

function stopCamera(target) {
    if (state.cameras[target]) {
        state.cameras[target].stop().catch(() => {});
        state.cameras[target] = null;
    }
    document.getElementById(`camera-${target}`)?.classList.add('hidden');
}

// ─── HELPERS ──────────────────────────────────────────────────────────────────
function setError(target, msg) {
    const id = target === 'action' ? 'action-error' : `${target}-error`;
    const el = document.getElementById(id);
    const textEl = document.getElementById(`${id}-text`);
    if (msg) { 
        if(textEl) textEl.textContent = msg; 
        else el.textContent = msg;
        el.classList.remove('hidden'); 
    }
    else { el.classList.add('hidden'); }
}

function activateStep(n, first = false) {
    const el = document.getElementById(`step-${n}`);
    if(el) el.classList.remove('opacity-50', 'pointer-events-none');
    const ind = document.getElementById(`step-indicator-${n}`);
    if (ind) ind.className = 'flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-xl shadow-sm transition-all whitespace-nowrap';
    if (ind) ind.querySelector('span').className = 'w-5 h-5 bg-white text-green-600 rounded-full flex items-center justify-center text-[10px] font-bold';
}

function activateStepDone(n) {
    const ind = document.getElementById(`step-indicator-${n}`);
    if (ind) ind.className = 'flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white rounded-xl shadow-md transition-all whitespace-nowrap';
}

function deactivateStep(n) {
    const el = document.getElementById(`step-${n}`);
    if (el) el.classList.add('opacity-50', 'pointer-events-none');
    const ind = document.getElementById(`step-indicator-${n}`);
    if (ind) ind.className = 'flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-500 rounded-xl transition-all whitespace-nowrap';
    if (ind) ind.querySelector('span').className = 'w-5 h-5 bg-slate-400 text-white rounded-full flex items-center justify-center text-[10px] font-bold';
}
</script>
@endsection
