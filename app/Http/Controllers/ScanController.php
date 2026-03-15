<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ScanController extends Controller
{
    /**
     * Halaman utama Peminjaman
     */
    public function index()
    {
        return view('scan.index');
    }

    /**
     * Halaman Pengembalian Buku
     */
    public function returnIndex()
    {
        return view('return.index');
    }

    /**
     * AJAX: Cari anggota berdasarkan qr_code atau NIS
     * GET /scan/member?code=MB-0001-XXXX
     */
    public function lookupMember(Request $request)
    {
        $code = trim($request->get('code', ''));

        if (empty($code)) {
            return response()->json(['error' => 'Kode tidak boleh kosong.'], 422);
        }

        $member = Member::where('qr_code', $code)
            ->orWhere('nis', $code)
            ->first();

        if (!$member) {
            return response()->json(['error' => 'Anggota tidak ditemukan.'], 404);
        }

        $activeBorrowings = $member->borrowings()
            ->with('book')
            ->where('status', 'dipinjam')
            ->get()
            ->map(fn($b) => [
                'id'              => $b->id,
                'judul'           => $b->book->judul,
                'barcode'         => $b->book->barcode,
                'tanggal_kembali' => $b->tanggal_kembali->format('d M Y'),
                'terlambat'       => now()->gt($b->tanggal_kembali),
            ]);

        return response()->json([
            'id'               => $member->id,
            'nama'             => $member->nama,
            'status'           => $member->status,
            'nis'              => $member->nis,
            'kelas'            => $member->kelas,
            'qr_code'          => $member->qr_code,
            'active_borrowings'=> $activeBorrowings,
            'jumlah_pinjam'    => $activeBorrowings->count(),
        ]);
    }

    /**
     * AJAX: Cari buku berdasarkan barcode atau ISBN
     * GET /scan/book?code=BK-0001-XXXX
     */
    public function lookupBook(Request $request)
    {
        $code = trim($request->get('code', ''));

        if (empty($code)) {
            return response()->json(['error' => 'Kode tidak boleh kosong.'], 422);
        }

        $book = Book::where('barcode', $code)
            ->orWhere('isbn', $code)
            ->first();

        if (!$book) {
            return response()->json(['error' => 'Buku tidak ditemukan.'], 404);
        }

        return response()->json([
            'id'             => $book->id,
            'judul'          => $book->judul,
            'pengarang'      => $book->pengarang,
            'penerbit'       => $book->penerbit,
            'barcode'        => $book->barcode,
            'isbn'           => $book->isbn,
            'stok'           => $book->stok,
            'jumlah_buku'    => $book->jumlah_buku,
            'tersedia'       => $book->stok > 0,
            'no_klasifikasi' => $book->no_klasifikasi,
        ]);
    }

    /**
     * AJAX: Proses peminjaman buku
     * POST /scan/borrow
     */
    public function borrow(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id'   => 'required|exists:books,id',
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $book   = Book::findOrFail($validated['book_id']);

        $activeBorrowings = $member->borrowings()->where('status', 'dipinjam')->count();
        if ($activeBorrowings >= 2) {
            return response()->json(['error' => 'Anggota sudah meminjam 2 buku. Kembalikan dulu sebelum meminjam lagi.'], 422);
        }

        if ($book->stok <= 0) {
            return response()->json(['error' => 'Stok buku habis!'], 422);
        }

        $sudahPinjam = $member->borrowings()
            ->where('book_id', $book->id)
            ->where('status', 'dipinjam')
            ->exists();
        if ($sudahPinjam) {
            return response()->json(['error' => 'Anggota sudah meminjam buku ini dan belum dikembalikan.'], 422);
        }

        $tanggalKembali = $this->hitungTanggalKembali(now());

        // Generate QR pengembalian sementara (akan diupdate setelah dapat id)
        $borrowing = Borrowing::create([
            'book_id'         => $book->id,
            'member_id'       => $member->id,
            'user_id'         => Auth::id(),
            'tanggal_pinjam'  => now()->toDateString(),
            'tanggal_kembali' => $tanggalKembali,
            'status'          => 'dipinjam',
            'return_qr'       => 'RET-TEMP',
        ]);

        // Generate QR return final dengan id borrowing + random
        $returnQr = 'RET-' . str_pad($borrowing->id, 5, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(6));
        $borrowing->update(['return_qr' => $returnQr]);

        $book->decrement('stok');

        return response()->json([
            'success'         => true,
            'borrowing_id'    => $borrowing->id,
            'member'          => $member->nama,
            'buku'            => $book->judul,
            'tanggal_pinjam'  => now()->format('d M Y'),
            'tanggal_kembali' => \Carbon\Carbon::parse($tanggalKembali)->format('d M Y'),
        ]);
    }

    /**
     * AJAX: Lookup borrowing berdasarkan return_qr
     * GET /return/lookup?code=RET-00001-XXXXXX
     */
    public function lookupReturnQr(Request $request)
    {
        $code = trim($request->get('code', ''));
        if (empty($code)) {
            return response()->json(['error' => 'Kode QR tidak boleh kosong.'], 422);
        }

        $borrowing = Borrowing::with(['book', 'member'])
            ->where('return_qr', $code)
            ->where('status', 'dipinjam')
            ->first();

        if (!$borrowing) {
            return response()->json(['error' => 'QR tidak valid atau buku sudah dikembalikan.'], 404);
        }

        return response()->json([
            'borrowing_id'    => $borrowing->id,
            'return_qr'       => $borrowing->return_qr,
            'member_nama'     => $borrowing->member->nama,
            'member_status'   => $borrowing->member->status,
            'book_judul'      => $borrowing->book->judul,
            'book_barcode'    => $borrowing->book->barcode,
            'tanggal_pinjam'  => $borrowing->tanggal_pinjam->format('d M Y'),
            'tanggal_kembali' => $borrowing->tanggal_kembali->format('d M Y'),
            'terlambat'       => now()->gt($borrowing->tanggal_kembali),
        ]);
    }

    /**
     * AJAX: Proses pengembalian buku
     * POST /return/process
     */
    public function returnBook(Request $request)
    {
        $validated = $request->validate([
            'borrowing_id' => 'required|exists:borrowings,id',
        ]);

        $borrowing = Borrowing::with(['book', 'member'])->findOrFail($validated['borrowing_id']);

        if ($borrowing->status === 'dikembalikan') {
            return response()->json(['error' => 'Buku ini sudah dikembalikan sebelumnya.'], 422);
        }

        $denda = 0;
        if (now()->gt($borrowing->tanggal_kembali)) {
            $hariTerlambat = now()->diffInDays($borrowing->tanggal_kembali);
            $denda = $hariTerlambat * 1000;
        }

        $borrowing->update([
            'tanggal_dikembalikan' => now()->toDateString(),
            'status'               => 'dikembalikan',
            'denda'                => $denda,
        ]);

        $borrowing->book->increment('stok');

        return response()->json([
            'success'      => true,
            'borrowing_id' => $borrowing->id,
            'member'       => $borrowing->member->nama,
            'buku'         => $borrowing->book->judul,
            'denda'        => $denda,
            'denda_format' => $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : 'Tidak ada denda',
        ]);
    }

    /**
     * Hitung tanggal kembali: +3 hari
     * Jika hasil jatuh Rabu/Kamis → geser ke Senin
     */
    private function hitungTanggalKembali(\Carbon\Carbon $dari): string
    {
        $tgl = $dari->copy()->addDays(3);

        if (in_array($tgl->dayOfWeek, [\Carbon\Carbon::WEDNESDAY, \Carbon\Carbon::THURSDAY])) {
            $tgl->next(\Carbon\Carbon::MONDAY);
        }

        return $tgl->toDateString();
    }
}
