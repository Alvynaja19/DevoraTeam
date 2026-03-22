## Teknologi Lengkap Proyek Perpustakaan SMA

---

## Backend — Laravel 13

Laravel 13 adalah pondasi utama seluruh sistem. Versi ini membutuhkan PHP 8.3 minimum dan membawa banyak peningkatan performa dibanding versi sebelumnya.

**Laravel Sanctum** digunakan untuk autentikasi. Sanctum menangani dua jenis autentikasi sekaligus: session-based untuk web (admin dan petugas login lewat browser), dan token-based untuk mobile (Flutter berkomunikasi menggunakan Bearer Token yang diterbitkan Sanctum).

**Spatie Laravel-Permission** digunakan untuk manajemen role dan permission. Library ini menangani perbedaan hak akses antara admin, petugas, dan anggota. Admin bisa approve anggota dan ubah settings, petugas hanya bisa proses transaksi harian, anggota hanya bisa lihat data milik sendiri.

**Maatwebsite Laravel-Excel** digunakan untuk fitur import dan export data. Import data buku dari Excel, import data anggota massal, dan export semua laporan ke format Excel.

**barryvdh/laravel-dompdf** digunakan untuk generate laporan dalam format PDF. Laporan bulanan, laporan denda, dan laporan kunjungan yang bisa dicetak dan diserahkan ke kepala sekolah.

**milon/barcode** digunakan untuk generate barcode eksemplar buku. Setiap kali admin menambah eksemplar baru, library ini menghasilkan gambar barcode yang bisa dicetak dan ditempel di buku fisik.

**Laravel Scheduler** digunakan untuk menjalankan tugas otomatis berdasarkan jadwal waktu. Tugas utamanya adalah cron job yang berjalan setiap hari pagi untuk mendeteksi peminjaman yang jatuh tempo besok, lalu mengirimkan push notification H-1 ke semua anggota yang bersangkutan. Scheduler juga menjalankan tugas otomatis mengubah status peminjaman menjadi `terlambat` ketika sudah melewati due date.

**Laravel Queues** digunakan untuk memproses pekerjaan berat di background tanpa memperlambat response ke pengguna. Pengiriman push notification ke banyak anggota sekaligus, pengiriman email, dan generate laporan besar semuanya diproses lewat queue.

**Laravel Observer** digunakan untuk memantau perubahan data di Model secara otomatis. Misalnya ketika admin mengubah status anggota dari `pending` menjadi `aktif`, Observer otomatis mencatat ke `activity_logs` dan membuat record di tabel `notifications` tanpa perlu ditulis manual di setiap controller.

**Laravel AI SDK** — fitur baru di Laravel 13 yang digunakan untuk chatbot rekomendasi buku. SDK ini menjadi jembatan antara Laravel dan layanan AI eksternal seperti OpenAI atau Gemini. API key disimpan aman di server, tidak pernah terekspos ke aplikasi mobile.

**Laravel Service Classes** adalah pola arsitektur yang digunakan untuk memisahkan logika bisnis dari controller. Proyek ini akan punya beberapa service class utama: `LoanService` untuk logika peminjaman dan kalkulasi due date, `ReturnService` untuk logika pengembalian dan kalkulasi denda, `MemberService` untuk registrasi dan verifikasi anggota, dan `NotificationService` untuk pengiriman notifikasi.

---

## Frontend Web — Vue 3 + Inertia.js

**Inertia.js** adalah jembatan antara Laravel dan Vue 3. Dengan Inertia, Vue bekerja langsung menggunakan routing Laravel tanpa perlu membuat REST API terpisah khusus untuk web. Controller Laravel mengembalikan Inertia response berisi komponen Vue beserta datanya, Vue merender di browser tanpa full page reload layaknya SPA. Hasilnya adalah pengalaman pengguna yang cepat seperti SPA namun tetap menggunakan autentikasi dan routing Laravel sepenuhnya.

**Vue 3** dengan Composition API digunakan untuk semua komponen UI di web. Vue 3 jauh lebih modular dibanding Vue 2 dan performa rendernya lebih baik. Komponen yang dibutuhkan antara lain halaman scan QR menggunakan kamera, form peminjaman interaktif, tabel laporan dengan filter dinamis, dan dashboard dengan grafik real-time.

**Vite** digunakan sebagai build tool. Vite sangat cepat karena menggunakan native ES modules, hot module replacement hampir instan, dan bundling produksi jauh lebih efisien dibanding Webpack yang dipakai versi Laravel lama.

**Tailwind CSS** digunakan untuk styling seluruh tampilan web. Tailwind adalah utility-first CSS framework yang memungkinkan styling dilakukan langsung di HTML tanpa menulis file CSS terpisah. Konsisten, cepat, dan mudah dikustomisasi untuk tampilan profesional.

**Pinia** digunakan sebagai state management Vue 3. Pinia menyimpan state global yang dibutuhkan di banyak komponen sekaligus, seperti data anggota yang sedang di-scan, daftar buku yang sedang dipilih dalam transaksi peminjaman, dan status notifikasi.

**html5-qrcode** adalah library JavaScript yang digunakan untuk fitur scan QR dan barcode dari kamera di browser web tanpa perlu hardware scanner khusus. Ini memungkinkan petugas menggunakan kamera laptop atau webcam untuk scan QR profil anggota dan barcode buku.

**Chart.js** digunakan untuk menampilkan grafik dan visualisasi data di dashboard admin. Grafik kunjungan harian, grafik peminjaman bulanan, grafik buku terpopuler, dan grafik kategori buku semuanya dirender menggunakan Chart.js di dalam komponen Vue.

---

## Frontend Mobile — Flutter

**Flutter** adalah framework Google untuk membuat aplikasi mobile cross-platform menggunakan bahasa Dart. Satu codebase Flutter menghasilkan aplikasi yang berjalan di Android dan iOS sekaligus. Flutter dipilih karena ekosistem barcode scanner-nya paling stabil dan performanya mendekati aplikasi native.

**mobile_scanner** adalah package Flutter untuk fitur scan barcode dan QR code menggunakan kamera HP. Package ini lebih stabil dan akurat dibanding alternatif lain di berbagai kondisi pencahayaan dan ukuran layar Android yang beragam.

**firebase_messaging** adalah package Flutter untuk menerima push notification dari Firebase Cloud Messaging. Notifikasi H-1 pengembalian, notifikasi denda baru, dan notifikasi aktivasi akun semuanya diterima melalui package ini.

**dio** adalah HTTP client untuk Flutter yang digunakan untuk berkomunikasi dengan Laravel API. Dio mendukung interceptor sehingga token autentikasi Sanctum bisa otomatis disertakan di setiap request tanpa perlu ditulis manual.

**qr_flutter** digunakan untuk merender QR profil anggota di halaman profil aplikasi mobile. QR dirender langsung dari `qr_token` yang disimpan di database tanpa perlu menyimpan file gambar QR di server.

**flutter_secure_storage** digunakan untuk menyimpan token autentikasi Sanctum secara aman di perangkat mobile. Token disimpan di secure enclave perangkat, tidak bisa dibaca oleh aplikasi lain.

**Provider atau Riverpod** digunakan sebagai state management Flutter. Menyimpan state global seperti data profil anggota yang login, daftar peminjaman aktif, dan status notifikasi yang belum dibaca.

---

## Database — MySQL

**MySQL 8.0** atau lebih baru digunakan sebagai database utama. Versi 8.0 dipilih karena mendukung kolom tipe JSON natively (digunakan di `activity_logs` dan `notifications`), FULLTEXT search untuk fitur pencarian buku, dan window functions untuk query laporan yang kompleks.

**Stored Procedures** digunakan untuk logika bisnis yang melibatkan banyak tabel sekaligus: `sp_validate_member` untuk validasi saat scan QR, `sp_calculate_due_date` untuk kalkulasi tanggal pengembalian, dan `sp_process_return` untuk proses pengembalian lengkap termasuk kalkulasi denda.

**Views** digunakan untuk menyederhanakan query yang sering dipakai: `v_active_loans`, `v_due_tomorrow`, `v_popular_books`, dan sebagainya. Controller Laravel cukup query view tanpa perlu menulis JOIN yang panjang berulang kali.

**Triggers** digunakan untuk menjaga konsistensi data secara otomatis: trigger rating otomatis update `avg_rating` di tabel `books`, trigger eksemplar otomatis update `total_copies` di tabel `books`.

---

## Infrastruktur Pendukung

**Redis** digunakan untuk dua keperluan utama. Pertama sebagai queue driver — semua background job seperti pengiriman push notification dan generate laporan diproses melalui Redis queue. Kedua sebagai cache driver — hasil query berat seperti laporan statistik dan daftar buku populer di-cache di Redis sehingga tidak perlu query database berulang kali.

**Firebase Cloud Messaging (FCM)** adalah layanan Google untuk mengirim push notification ke perangkat Android dan iOS. Laravel mengirim pesan ke FCM server, FCM yang mengantarkan ke perangkat anggota.

**Laravel Reverb atau Pusher** bisa digunakan untuk fitur real-time jika diperlukan, misalnya dashboard admin yang menampilkan jumlah pengunjung hari ini yang terupdate langsung tanpa perlu refresh halaman.

---

## Layanan AI

**Laravel AI SDK + OpenAI atau Gemini** digunakan untuk fitur chatbot rekomendasi buku di mobile app. Anggota bisa bertanya bebas tentang rekomendasi buku dan AI akan menjawab berdasarkan koleksi perpustakaan. Karena SDK berjalan di backend Laravel, API key tidak pernah terekspos ke mobile dan semua percakapan bisa dilog untuk analisis.

---

## Tools Development

**Composer** adalah package manager PHP untuk mengelola semua dependency Laravel dan library pihak ketiga.

**npm** adalah package manager JavaScript untuk mengelola dependency frontend seperti Vue, Tailwind, dan Vite.

**Laravel Telescope** digunakan selama development untuk debugging. Telescope mencatat semua request, query database, queue job, dan scheduled command sehingga mudah menemukan masalah.

**Laravel Pint** adalah code formatter PHP resmi dari Laravel untuk memastikan kode tetap konsisten mengikuti standar PSR-12.

---

## Ringkasan Stack

| Lapisan | Teknologi |
|---|---|
| Backend | Laravel 13, PHP 8.3, MySQL 8 |
| Web Frontend | Vue 3, Inertia.js, Tailwind CSS, Vite |
| Mobile | Flutter, Dart |
| Autentikasi | Laravel Sanctum |
| Real-time | Redis, FCM, Laravel Reverb |
| AI / Chatbot | Laravel AI SDK, OpenAI / Gemini |
| Queue & Cache | Redis, Laravel Queue |
| Laporan | Laravel Excel, DomPDF |
| Barcode Web | html5-qrcode |
| Barcode Mobile | mobile_scanner, qr_flutter |
