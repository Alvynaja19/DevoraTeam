## Konsep Aplikasi Sistem Perpustakaan SMA

---

## Gambaran Umum

Aplikasi ini adalah sistem manajemen perpustakaan digital yang terdiri dari tiga platform sekaligus: web untuk admin dan petugas, web untuk pengunjung umum, dan aplikasi mobile untuk anggota. Ketiganya terhubung ke satu backend yang sama sehingga data selalu sinkron secara real-time. Konsep utamanya adalah menggantikan proses manual seperti pencatatan di buku besar, kartu katalog fisik, dan slip peminjaman kertas menjadi sistem digital yang terotomatisasi.

---

## Konsep Identitas Digital — QR Profil

Inti dari seluruh sistem ini adalah **QR profil anggota**. Setiap anggota memiliki satu QR code unik yang menjadi identitas digital mereka di perpustakaan. QR ini menggantikan kartu anggota fisik.

QR profil digunakan untuk tiga hal sekaligus: presensi kunjungan saat anggota datang ke perpustakaan, proses peminjaman buku, dan proses pengembalian buku. Artinya anggota tidak perlu membawa apapun selain membuka aplikasi mobile dan menunjukkan QR mereka ke petugas.

Isi dari QR ini hanyalah sebuah string UUID unik seperti `550e8400-e29b-41d4-a716-446655440000`. Bukan nama, bukan nomor HP, bukan data sensitif apapun. Ketika petugas scan, sistem yang akan mencari data lengkap anggota berdasarkan UUID tersebut. Ini membuat QR tetap aman meskipun ada orang lain yang melihat atau memfoto layar anggota.

QR profil berbeda sepenuhnya dari barcode buku. Barcode buku ditempel di fisik buku dan berisi kode eksemplar. Keduanya adalah sistem yang terpisah dengan tujuan berbeda.

---

## Konsep Dua Jenis Barcode

Sistem ini menggunakan dua jenis kode yang berbeda dan tidak boleh tertukar.

**QR profil anggota** — berbentuk QR code, ditampilkan di aplikasi mobile anggota. Berisi UUID unik anggota. Digunakan oleh petugas untuk mengidentifikasi siapa anggotanya.

**Barcode buku (eksemplar)** — berbentuk barcode linear seperti yang biasa ditempel di produk. Ditempel secara fisik di setiap buku. Berisi kode eksemplar unik. Digunakan oleh petugas saat proses peminjaman untuk mengidentifikasi buku mana yang dipinjam.

Pemisahan ini penting karena satu judul buku bisa punya banyak eksemplar fisik. Misalnya perpustakaan punya 3 eksemplar "Bumi Manusia" — ketiganya punya barcode berbeda meskipun judulnya sama. Sistem harus tahu eksemplar mana yang dipinjam, bukan hanya judulnya.

---

## Konsep Tiga Lapisan Pengguna

**Admin** adalah pengelola sistem tertinggi. Admin bisa melakukan segalanya: verifikasi anggota baru, mengubah konfigurasi denda, mengelola semua data buku dan anggota, melihat semua laporan, dan membebaskan denda jika diperlukan. Admin juga satu-satunya yang bisa mensuspend anggota dan memberikan alasannya.

**Petugas** adalah operator harian di perpustakaan. Petugas menangani semua transaksi: scan QR untuk presensi, proses peminjaman, proses pengembalian, dan konfirmasi pembayaran denda. Petugas tidak bisa mengubah konfigurasi sistem atau menghapus data master.

**Anggota** adalah pengguna akhir — siswa, guru, dan masyarakat umum. Anggota berinteraksi dengan sistem melalui aplikasi mobile dan halaman web publik. Mereka bisa melihat koleksi buku, memeriksa status peminjaman mereka sendiri, melihat denda, dan berkomunikasi dengan chatbot untuk rekomendasi buku.

---

## Konsep Register Mandiri

Pengunjung bisa mendaftar sendiri tanpa perlu datang ke petugas. Prosesnya sepenuhnya online melalui web atau aplikasi mobile.

Saat mendaftar, pengunjung mengisi data diri. Jika memilih tipe siswa, muncul dropdown pilihan kelas. Begitu tombol daftar ditekan, sistem langsung membuat akun dan men-generate QR profil secara otomatis pada saat itu juga. Anggota bisa langsung melihat QR mereka di halaman profil.

Namun status akun masih `pending` artinya QR belum bisa digunakan untuk meminjam. Admin perlu memverifikasi data terlebih dahulu. Jika disetujui, status berubah menjadi `aktif` dan anggota mendapat notifikasi di HP bahwa akun sudah bisa digunakan. Jika ditolak, anggota mendapat notifikasi beserta alasan penolakan.

Konsep ini menjaga keamanan data perpustakaan sekaligus memberikan kemudahan bagi calon anggota yang tidak perlu antri di perpustakaan hanya untuk mendaftar.

---

## Konsep Peminjaman dengan Validasi Berlapis

Setiap kali petugas scan QR profil anggota, sistem menjalankan pengecekan berlapis secara otomatis sebelum mengizinkan peminjaman.

Lapisan pertama adalah pengecekan status akun. Sistem memastikan anggota statusnya `aktif`, bukan `pending`, `suspended`, `nonaktif`, atau `ditolak`. Masing-masing kondisi memberikan pesan yang berbeda ke petugas beserta saran tindakan yang perlu diambil.

Lapisan kedua adalah pengecekan denda. Jika anggota masih punya denda yang belum lunas, peminjaman diblokir. Sistem menampilkan detail denda tersebut sehingga petugas bisa langsung memproses pembayaran di halaman yang sama tanpa perlu berpindah menu.

Lapisan ketiga adalah pengecekan kuota. Setiap anggota hanya boleh meminjam maksimal 2 buku secara bersamaan. Jika sudah meminjam 2 buku dan belum dikembalikan, sistem menampilkan daftar buku yang sedang dipinjam beserta due date-nya.

Semua pengecekan ini dijalankan oleh stored procedure `sp_validate_member` di database sehingga hasilnya konsisten baik diakses dari web maupun dari aplikasi mobile.

---

## Konsep Kalkulasi Tanggal Pengembalian

Tanggal pengembalian tidak diisi manual oleh petugas melainkan dihitung otomatis oleh sistem menggunakan aturan yang sudah dikonfigurasi admin.

Untuk peminjaman biasa (pembaca), sistem menambahkan 3 hari kerja dari tanggal pinjam. Sabtu dan Minggu dilewati karena bukan hari kerja. Setelah mendapat tanggal hasilnya, sistem mengecek apakah hari tersebut jatuh di hari Rabu atau Kamis. Jika ya, tanggal digeser ke Senin minggu berikutnya. Logika ini mengakomodasi kondisi perpustakaan yang mungkin tutup atau sibuk di pertengahan minggu.

Untuk peminjaman keperluan lomba, tanggal pengembalian mengikuti tanggal selesai lomba yang diisi petugas. Ini mencegah anggota yang sedang persiapan lomba terkena denda karena membutuhkan buku lebih lama dari biasanya.

---

## Konsep Manajemen Eksemplar Buku

Sistem membedakan antara data judul buku dan data eksemplar fisik buku. Ini adalah perbedaan konseptual yang paling penting di modul buku.

Tabel `books` menyimpan informasi tentang judul: nama buku, pengarang, penerbit, kategori, deskripsi, sampul, dan lokasi rak. Satu record di tabel ini mewakili satu judul.

Tabel `book_copies` menyimpan informasi tentang fisik buku: barcode unik tiap eksemplar, kondisi (baik/rusak/hilang), dan status (tersedia/dipinjam/direservasi). Satu judul bisa punya banyak record di tabel ini.

Saat peminjaman, yang dipindahkan statusnya adalah eksemplar, bukan judul. Saat anggota mengembalikan buku rusak, kondisi yang diupdate adalah eksemplar spesifik tersebut, bukan semua buku dengan judul yang sama.

---

## Konsep Denda Dinamis

Semua nominal denda disimpan di tabel `settings` database, bukan dikodekan langsung di program. Artinya admin bisa mengubah nominal denda kapan saja melalui halaman pengaturan tanpa perlu meminta programmer mengubah kode.

Ada empat jenis denda yang bisa dikonfigurasi secara terpisah: denda keterlambatan per hari, denda buku rusak ringan, denda buku rusak berat, dan denda buku hilang.

Denda keterlambatan dihitung secara otomatis saat proses pengembalian dengan rumus: jumlah hari terlambat dikali nominal per hari. Denda kerusakan dan kehilangan adalah nominal tetap yang langsung ditambahkan ke tagihan anggota.

Satu buku yang dikembalikan bisa menghasilkan lebih dari satu denda sekaligus. Misalnya buku dikembalikan terlambat 5 hari sekaligus dalam kondisi rusak ringan — anggota akan punya dua tagihan denda sekaligus untuk buku tersebut.

---

## Konsep Notifikasi Real-time

Sistem memiliki dua saluran notifikasi yang berjalan bersamaan.

**Push notification ke mobile** menggunakan Firebase Cloud Messaging. Notifikasi dikirim untuk kejadian penting: akun baru diaktifkan atau ditolak admin, pengingat H-1 sebelum jatuh tempo, buku sudah terlambat dikembalikan, denda baru muncul, denda sudah lunas, buku reservasi sudah tersedia, dan konfirmasi perpanjangan berhasil.

**Notifikasi H-1** dijalankan oleh Laravel Scheduler yang berjalan otomatis setiap hari pagi. Scheduler membaca view `v_due_tomorrow` yang berisi semua peminjaman yang jatuh tempo besok, lalu mengirim push notification ke semua anggota yang ada di daftar tersebut. Proses ini berjalan di background tanpa perlu ada petugas yang melakukannya secara manual.

---

## Konsep Laporan

Semua data transaksi yang masuk ke sistem secara otomatis bisa dijadikan laporan. Admin bisa memfilter laporan berdasarkan rentang tanggal, kelas, tipe anggota, atau kategori tertentu.

Laporan bisa diexport ke dua format: PDF untuk diserahkan ke kepala sekolah atau keperluan arsip resmi, dan Excel untuk diolah lebih lanjut atau dianalisis sendiri oleh admin.

Data kunjungan yang dikumpulkan dari scan QR harian menghasilkan laporan statistik yang berguna: berapa pengunjung per hari, hari apa perpustakaan paling ramai, kelas mana yang paling aktif mengunjungi perpustakaan, dan tren kunjungan dari bulan ke bulan.

---

## Konsep Mobile App sebagai Perpanjangan Layanan

Aplikasi mobile bukan sekadar versi mobile dari web, melainkan layanan mandiri untuk anggota yang bisa digunakan dari mana saja dan kapan saja.

Anggota bisa mencari buku dan melihat ketersediaannya sebelum datang ke perpustakaan, sehingga tidak buang waktu datang hanya untuk mendapati buku yang dicari sedang dipinjam. Mereka bisa memantau status peminjaman dan sisa hari sebelum jatuh tempo. Mereka bisa melihat dan memantau status denda mereka.

Fitur chatbot di mobile menggunakan AI (OpenAI atau Gemini) untuk memberikan rekomendasi buku secara personal. Anggota bisa bertanya seperti "rekomendasikan novel Indonesia yang bagus" atau "saya suka buku tentang psikologi, apa yang cocok?" dan sistem akan memberikan rekomendasi yang relevan berdasarkan koleksi perpustakaan.

QR profil yang selalu tersedia di halaman profil mobile membuat anggota tidak perlu membawa kartu fisik ke perpustakaan. Cukup buka HP, tunjukkan QR, selesai.
