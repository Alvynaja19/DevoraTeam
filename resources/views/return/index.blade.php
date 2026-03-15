@extends('layouts.app')
@section('title', 'Pengembalian Buku')
@section('header', 'Pengembalian Buku')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- HERO TITLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 relative overflow-hidden">
        <div class="absolute -bottom-8 -right-8 w-40 h-40 bg-green-50 rounded-full opacity-50 pointer-events-none"></div>
        <h2 class="text-2xl font-bold text-slate-800 mb-1">Scan QR Pengembalian</h2>
        <p class="text-sm text-slate-500">Mintalah anggota menunjukkan QR Pengembalian dari halaman <strong>Profil Saya</strong> mereka.</p>
    </div>

    {{-- STEP 1: Scan QR Return --}}
    <div id="step-scan" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8">
        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-3">
            <span class="w-8 h-8 bg-green-100 text-green-600 rounded-xl flex items-center justify-center text-sm font-bold shadow-sm">1</span>
            Scan QR Pengembalian Anggota
        </h3>

        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-green-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5z"/></svg>
                    </div>
                    <input type="text" id="return-qr-input"
                           placeholder="Scan atau ketik kode QR pengembalian (RET-...)..."
                           class="w-full pl-11 pr-4 py-3.5 bg-slate-50 border-2 border-slate-200 rounded-xl text-sm focus:outline-none focus:bg-white focus:border-green-500 focus:ring-4 focus:ring-green-500/10 transition-all font-mono"
                           autofocus>
                </div>
                <div class="flex gap-2">
                    <button onclick="toggleReturnCamera()" id="btn-cam-return"
                            class="flex-1 sm:flex-none px-4 py-3.5 bg-slate-100 hover:bg-green-50 hover:text-green-700 hover:border-green-200 border-2 border-transparent text-slate-600 rounded-xl transition-all flex items-center justify-center gap-2 text-sm font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                        <span class="sm:hidden">Kamera</span>
                    </button>
                    <button onclick="lookupReturnQr()" class="flex-1 sm:flex-none px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-sm hover:shadow transition-all text-sm font-semibold">
                        Cari
                    </button>
                </div>
            </div>

            {{-- Camera --}}
            <div id="camera-return" class="hidden transform transition-all">
                <div id="reader-return" class="rounded-2xl overflow-hidden border-4 border-slate-100 shadow-inner" style="width:100%"></div>
                <button onclick="stopReturnCamera()" class="mt-3 w-full py-2.5 text-sm font-medium text-slate-500 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">Tutup Kamera</button>
            </div>

            {{-- Error --}}
            <div id="return-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3 flex items-start gap-2">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span id="return-error-text" class="leading-relaxed"></span>
            </div>
        </div>

        {{-- Preview hasil —}}
        <div id="return-result" class="hidden mt-6 bg-slate-50 border-2 border-green-100 rounded-2xl p-5 space-y-4">
            
            {{-- Info Anggota --}}
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 text-green-700 rounded-xl flex items-center justify-center font-bold text-lg" id="ret-avatar">?</div>
                <div>
                    <p class="font-bold text-slate-800 text-lg" id="ret-member-nama"></p>
                    <span id="ret-member-status" class="text-xs font-bold px-2 py-0.5 bg-green-50 text-green-700 rounded uppercase tracking-wide"></span>
                </div>
            </div>

            <div class="border-t border-slate-200 pt-4 space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Buku</span>
                    <span class="font-bold text-slate-800 text-right max-w-[60%]" id="ret-book-judul"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Barcode Buku</span>
                    <span class="font-mono text-slate-700" id="ret-book-barcode"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tgl Pinjam</span>
                    <span class="font-medium text-slate-700" id="ret-tgl-pinjam"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Batas Kembali</span>
                    <span class="font-medium" id="ret-tgl-kembali"></span>
                </div>
                <div id="ret-terlambat-badge" class="hidden">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 bg-red-50 border border-red-100 px-3 py-1.5 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                        Buku Terlambat! Akan dikenakan denda.
                    </span>
                </div>
            </div>

            <button id="btn-proses-kembali" onclick="processReturn()"
                    class="w-full py-4 mt-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow hover:shadow-md transition-all flex items-center justify-center gap-2.5">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg>
                Proses Pengembalian Buku
            </button>
        </div>
    </div>

    {{-- STEP 2: Hasil Pengembalian --}}
    <div id="step-result" class="hidden bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 text-center">
        <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner border-4 border-white">
            <svg class="w-10 h-10 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-1">Pengembalian Berhasil!</h3>
        <p class="text-sm text-slate-500 mb-6" id="result-subtitle"></p>

        <div id="result-detail" class="bg-slate-50 rounded-xl p-4 text-sm text-left space-y-2 mb-6"></div>

        <button onclick="resetReturn()" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition-colors text-sm flex items-center gap-2 mx-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
            Transaksi Baru
        </button>
    </div>

</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
const CSRF = "{{ csrf_token() }}";
let currentBorrowingId = null;
let returnCameraScanner = null;

document.getElementById('return-qr-input').addEventListener('keydown', e => {
    if (e.key === 'Enter') lookupReturnQr();
});

async function lookupReturnQr() {
    const code = document.getElementById('return-qr-input').value.trim();
    if (!code) return;
    setReturnError(null);

    try {
        const res = await fetch(`/return/lookup?code=${encodeURIComponent(code)}`);
        const data = await res.json();
        if (!res.ok) { setReturnError(data.error); return; }
        showReturnPreview(data);
    } catch(e) { 
        console.error("lookupReturnQr error:", e);
        setReturnError('Gagal menghubungi server.'); 
    }
}

function showReturnPreview(data) {
    currentBorrowingId = data.borrowing_id;
    document.getElementById('ret-avatar').textContent = data.member_nama.substring(0, 2).toUpperCase();
    document.getElementById('ret-member-nama').textContent = data.member_nama;
    document.getElementById('ret-member-status').textContent = data.member_status;
    document.getElementById('ret-book-judul').textContent = data.book_judul;
    document.getElementById('ret-book-barcode').textContent = data.book_barcode;
    document.getElementById('ret-tgl-pinjam').textContent = data.tanggal_pinjam;

    const kembaliEl = document.getElementById('ret-tgl-kembali');
    kembaliEl.textContent = data.tanggal_kembali;
    kembaliEl.className = data.terlambat ? 'font-bold text-red-600' : 'font-medium text-slate-700';

    const badge = document.getElementById('ret-terlambat-badge');
    data.terlambat ? badge.classList.remove('hidden') : badge.classList.add('hidden');

    document.getElementById('return-result').classList.remove('hidden');
}

async function processReturn() {
    if (!currentBorrowingId) return;
    const btn = document.getElementById('btn-proses-kembali');
    btn.disabled = true;
    btn.textContent = 'Memproses...';

    try {
        const res = await fetch('/return/process', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ borrowing_id: currentBorrowingId })
        });
        const data = await res.json();
        if (!res.ok) { setReturnError(data.error); btn.disabled = false; btn.textContent = 'Proses Pengembalian Buku'; return; }
        showReturnResult(data);
    } catch(e) { 
        console.error("processReturn error:", e);
        setReturnError('Gagal menghubungi server.'); 
        btn.disabled = false; 
        btn.textContent = 'Proses Pengembalian Buku'; 
    }
}

function showReturnResult(data) {
    document.getElementById('step-scan').classList.add('hidden');
    document.getElementById('step-result').classList.remove('hidden');
    document.getElementById('result-subtitle').textContent = `${data.buku} dikembalikan oleh ${data.member}`;
    document.getElementById('result-detail').innerHTML = `
        <div class="flex justify-between border-b border-slate-200 pb-2"><span class="text-slate-500">Anggota</span><span class="font-bold">${data.member}</span></div>
        <div class="flex justify-between border-b border-slate-200 pb-2"><span class="text-slate-500">Buku</span><span class="font-bold text-right max-w-[60%]">${data.buku}</span></div>
        <div class="flex justify-between pt-1"><span class="text-slate-500">Status Denda</span><span class="font-bold ${data.denda > 0 ? 'text-red-600' : 'text-emerald-600'}">${data.denda_format}</span></div>`;
}

function resetReturn() {
    currentBorrowingId = null;
    document.getElementById('return-qr-input').value = '';
    document.getElementById('return-result').classList.add('hidden');
    document.getElementById('step-scan').classList.remove('hidden');
    document.getElementById('step-result').classList.add('hidden');
    setReturnError(null);
    const btn = document.getElementById('btn-proses-kembali');
    btn.disabled = false;
    btn.innerHTML = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3"/></svg> Proses Pengembalian Buku`;
    document.getElementById('return-qr-input').focus();
}

function toggleReturnCamera() {
    const camEl = document.getElementById('camera-return');
    if (camEl.classList.contains('hidden')) {
        camEl.classList.remove('hidden');
        startReturnCamera();
    } else {
        stopReturnCamera();
    }
}

function startReturnCamera() {
    if (returnCameraScanner) return;
    returnCameraScanner = new Html5Qrcode('reader-return');
    returnCameraScanner.start(
        { facingMode: 'environment' },
        { fps: 10, qrbox: { width: 280, height: 180 } },
        (code) => {
            document.getElementById('return-qr-input').value = code;
            stopReturnCamera();
            lookupReturnQr();
        },
        () => {}
    ).catch(() => setReturnError('Tidak bisa mengakses kamera. Pastikan izin kamera sudah diberikan.'));
}

function stopReturnCamera() {
    if (returnCameraScanner) {
        returnCameraScanner.stop().catch(() => {});
        returnCameraScanner = null;
    }
    document.getElementById('camera-return')?.classList.add('hidden');
}

function setReturnError(msg) {
    const el = document.getElementById('return-error');
    if (msg) {
        document.getElementById('return-error-text').textContent = msg;
        el.classList.remove('hidden');
    } else {
        el.classList.add('hidden');
    }
}
</script>
@endsection
