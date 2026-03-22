## Alur Kerja Lengkap Sistem Perpustakaan SMA

---

## Sisi Pengunjung / Anggota

### Registrasi

Pengunjung membuka halaman web atau aplikasi mobile, lalu mengisi form registrasi berisi nama lengkap, email, password, nomor HP, dan tipe anggota. Jika memilih siswa, muncul dropdown tambahan untuk memilih kelas. Setelah submit, sistem langsung otomatis membuat akun di tabel `users`, generate kode anggota unik berdasarkan tipe dan tahun seperti `SIS-2025-001`, lalu generate string unik yang akan menjadi isi QR code dan disimpan di tabel `members` dengan status `pending`.

Anggota langsung bisa login dan melihat QR profil mereka di halaman profil, namun belum bisa meminjam buku karena status masih pending. Di layar mereka muncul informasi bahwa akun sedang menunggu verifikasi admin.

---

### Setelah Diverifikasi Admin

Begitu admin menyetujui akun, status berubah menjadi `aktif` dan anggota mendapat notifikasi push di HP mereka. Mulai saat ini QR profil mereka sudah fungsional untuk semua kegiatan di perpustakaan — presensi, peminjaman, dan pengembalian.

---

### Datang ke Perpustakaan

Anggota datang dan menunjukkan QR profil di aplikasi mobile ke petugas untuk di-scan sebagai presensi kunjungan. Petugas memilih kategori kunjungan: membaca, pengunjung biasa, atau peminjam. Sistem mencatat waktu dan tanggal kunjungan secara otomatis.

---

### Meminjam Buku

Anggota yang ingin meminjam buku cukup menunjukkan QR profil mereka ke petugas. Setelah petugas scan dan sistem memvalidasi (lolos pengecekan denda, kuota, dan status aktif), petugas scan barcode buku yang ingin dipinjam. Anggota bisa pinjam maksimal 2 buku berbeda dalam satu transaksi. Setelah konfirmasi, anggota langsung mendapat notifikasi di HP berisi detail peminjaman: judul buku, tanggal pinjam, dan tanggal harus dikembalikan.

Jika meminjam untuk keperluan lomba, petugas memilih keterangan `lomba` dan mengisi tanggal selesai lomba. Tanggal pengembalian otomatis mengikuti tanggal lomba selesai, bukan 3 hari normal.

---

### Mengembalikan Buku

Anggota datang ke perpustakaan dan kembali menunjukkan QR profil mereka. Petugas scan QR profil, sistem menampilkan daftar buku yang sedang dipinjam anggota tersebut. Petugas memilih buku mana yang dikembalikan dan mengisi kondisi buku setelah dikembalikan. Jika terlambat atau buku rusak/hilang, sistem langsung menghitung denda dan menampilkannya. Anggota membayar denda ke petugas, petugas konfirmasi pembayaran, dan status denda berubah lunas. Anggota mendapat notifikasi di HP bahwa buku sudah berhasil dikembalikan.

---

### Fitur di Aplikasi Mobile

Di aplikasi mobile anggota bisa melakukan banyak hal secara mandiri tanpa harus ke perpustakaan. Mereka bisa melihat buku apa saja yang sedang dipinjam beserta sisa hari sebelum jatuh tempo. Mereka bisa melihat riwayat semua peminjaman yang pernah dilakukan. Jika ada denda aktif, muncul notifikasi dan detail dendanya. Anggota juga bisa mencari buku berdasarkan judul, pengarang, atau kategori untuk mengecek ketersediaan sebelum datang ke perpustakaan. Ada fitur chatbot yang bisa memberikan rekomendasi buku berdasarkan preferensi atau pertanyaan bebas seperti "rekomendasikan buku fiksi ilmiah yang menarik". Di halaman profil selalu tersedia QR code yang bisa ditunjukkan ke petugas kapan saja.

---

---

## Sisi Admin

### Login dan Dashboard

Admin login dengan akun khusus yang memiliki role tertinggi. Halaman pertama yang dilihat adalah dashboard berisi ringkasan hari ini: berapa pengunjung hari ini, berapa peminjaman aktif, berapa buku yang jatuh tempo besok, berapa denda yang belum lunas, dan berapa anggota pending yang menunggu verifikasi. Semua angka ini realtime dan bisa diklik untuk langsung masuk ke halaman detailnya.

---

### Verifikasi Anggota Baru

Admin melihat daftar anggota dengan status pending. Untuk setiap anggota, admin bisa melihat data yang mereka isi saat registrasi. Admin bisa langsung approve atau tolak. Jika ditolak, admin mengisi alasan penolakan dan sistem mengirim notifikasi ke anggota. Jika disetujui, status anggota berubah menjadi aktif dan anggota bisa langsung menggunakan QR mereka.

---

### Manajemen Buku

Admin bisa menambah buku baru satu per satu dengan mengisi form lengkap: judul, pengarang, penerbit, tahun, kategori, nomor rak, dan jumlah eksemplar. Untuk penambahan massal, admin bisa import dari file Excel yang sudah disiapkan sesuai template. Sistem otomatis membuat data `book_copies` sebanyak jumlah eksemplar yang dimasukkan, masing-masing dengan barcode unik yang bisa langsung dicetak dan ditempel di buku fisik.

Admin juga bisa mengupdate kondisi buku kapan saja jika ada buku yang rusak atau perlu dievaluasi, serta melihat histori siapa saja yang pernah meminjam buku tertentu.

---

### Manajemen Anggota

Admin bisa melihat semua anggota dengan filter berdasarkan tipe (siswa, guru, umum), kelas, dan status. Untuk siswa, tampil dalam bentuk dropdown per kelas sehingga mudah dicari. Admin bisa mengedit data anggota, menonaktifkan atau mensuspend anggota bermasalah dengan mengisi alasan, dan mengaktifkan kembali anggota yang sebelumnya nonaktif. Admin juga bisa export seluruh data anggota ke Excel untuk keperluan rekap tahunan atau laporan ke kepala sekolah.

---

### Manajemen Peminjaman

Admin dan petugas bisa melihat semua peminjaman aktif yang sedang berjalan, termasuk yang sudah terlambat. Ada filter untuk melihat berdasarkan status: aktif, terlambat, selesai, atau hilang. Admin bisa melihat detail setiap transaksi: siapa peminjamnya, buku apa, kapan dipinjam, kapan harus kembali, dan apakah ada denda.

Untuk kasus lomba, admin bisa mengupdate tanggal selesai lomba jika ternyata lomba diperpanjang, sehingga due date ikut menyesuaikan tanpa memunculkan denda yang tidak adil.

---

### Manajemen Denda

Admin melihat daftar semua denda yang belum lunas. Bisa filter berdasarkan tipe denda: keterlambatan, kerusakan, atau kehilangan. Saat anggota datang membayar, petugas mengkonfirmasi pembayaran, mengisi nominal yang dibayar, dan sistem mencatat ke riwayat pembayaran. Admin juga bisa membebaskan denda jika ada alasan tertentu, misalnya buku rusak karena bencana, dengan mengisi catatan alasan pembebasan.

Nominal denda bisa diubah kapan saja di menu Pengaturan tanpa perlu bantuan programmer: denda per hari keterlambatan, denda rusak ringan, denda rusak berat, dan denda kehilangan semuanya bisa dikonfigurasi langsung.

---

### Laporan

Admin bisa mengakses berbagai laporan yang bisa di-filter berdasarkan rentang tanggal, kelas, atau tipe anggota. Laporan yang tersedia antara lain laporan kunjungan harian dan bulanan dengan breakdown per kategori kunjungan, laporan peminjaman aktif dan selesai, laporan denda dengan status pembayaran, laporan buku terpopuler berdasarkan jumlah peminjaman, laporan rating buku dari anggota, laporan kondisi buku termasuk yang rusak dan hilang, serta laporan anggota aktif per kelas. Semua laporan bisa diexport ke PDF untuk diserahkan ke kepala sekolah atau ke Excel untuk diolah lebih lanjut.

---

### Pengaturan Sistem

Di menu pengaturan, admin bisa mengubah nama perpustakaan dan sekolah, mengubah semua nominal denda, mengubah maksimum jumlah buku yang bisa dipinjam, mengubah lama hari peminjaman default, dan mengaktifkan atau menonaktifkan rules Rabu-Kamis untuk pengembalian. Semua perubahan langsung berlaku tanpa perlu restart sistem.

---

---

## Sisi Petugas

Petugas memiliki akses lebih terbatas dibanding admin. Mereka bisa melakukan semua operasi harian: scan QR untuk presensi, proses peminjaman, proses pengembalian, dan konfirmasi pembayaran denda. Namun petugas tidak bisa mengubah pengaturan sistem, menghapus data buku atau anggota, melihat laporan keuangan secara penuh, atau memberikan akses ke anggota yang di-suspend oleh admin. Semua aksi petugas tercatat di tabel `activity_logs` sehingga admin bisa mengaudit siapa mengerjakan apa dan kapan.
