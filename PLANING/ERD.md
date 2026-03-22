# ERD — Sistem Perpustakaan SMA

> **Stack:** Laravel 13 · MySQL 8 · Vue 3 + Inertia.js · Flutter  
> **Versi Schema:** v2  
> **Dibuat:** 2025

---

## Daftar Isi

1. [Diagram Relasi](#1-diagram-relasi)
2. [Tabel Master](#2-tabel-master)
   - [users](#21-users)
   - [classes](#22-classes)
   - [members](#23-members)
   - [categories](#24-categories)
   - [books](#25-books)
   - [book_copies](#26-book_copies)
3. [Tabel Transaksi](#3-tabel-transaksi)
   - [loans](#31-loans)
   - [loan_items](#32-loan_items)
   - [loan_extensions](#33-loan_extensions)
   - [fines](#34-fines)
   - [fine_payments](#35-fine_payments)
   - [reservations](#36-reservations)
   - [visits](#37-visits)
   - [book_ratings](#38-book_ratings)
4. [Tabel Sistem](#4-tabel-sistem)
   - [settings](#41-settings)
   - [notifications](#42-notifications)
   - [fcm_tokens](#43-fcm_tokens)
   - [activity_logs](#44-activity_logs)
5. [Tabel E-Book](#5-tabel-e-book)
   - [ebook_bookmarks](#51-ebook_bookmarks)
   - [ebook_reading_progress](#52-ebook_reading_progress)
   - [ebook_cache](#53-ebook_cache)
6. [Views](#6-views)
7. [Stored Procedures](#7-stored-procedures)
8. [Triggers](#8-triggers)
9. [Enum Reference](#9-enum-reference)
10. [Index & Performa](#10-index--performa)

---

## 1. Diagram Relasi

```
┌─────────┐       ┌───────────┐       ┌─────────┐
│  users  │──────<│  members  │>──────│ classes │
└─────────┘  1:1  └───────────┘  M:1  └─────────┘
                       │
          ┌────────────┼────────────┬────────────┬────────────────────┐
          │            │            │            │                    │
          ▼            ▼            ▼            ▼                    ▼
       ┌──────┐  ┌──────────┐ ┌──────────┐ ┌────────────┐  ┌──────────────────┐
       │loans │  │reserva-  │ │  visits  │ │book_ratings│  │ ebook_bookmarks  │
       └──────┘  │  tions   │ └──────────┘ └────────────┘  ├──────────────────┤
          │      └──────────┘                    │          │ebook_reading_    │
          │           │                          │          │    progress      │
     ┌────┴────┐       │                    ┌────┴────┐     └──────────────────┘
     │         │       │                    │  books  │
     ▼         ▼       ▼                    └────┬────┘
┌────────┐ ┌─────────────────────────────┐       │
│ loan_  │ │  reservations.book_id ──────┤  ┌────┴──────┐
│ items  │ └─────────────────────────────┘  │book_copies│
└────┬───┘                                  └───────────┘
     │
     ├──────────────────┐
     │                  │
     ▼                  ▼
┌─────────┐    ┌──────────────────┐
│  fines  │    │  loan_extensions │
└────┬────┘    └──────────────────┘
     │
     ▼
┌───────────────┐     ┌─────────────┐
│ fine_payments │     │ ebook_cache │ (tidak ada FK, standalone cache)
└───────────────┘     └─────────────┘
```

### Ringkasan Relasi

| Tabel Asal       | Relasi | Tabel Tujuan          | Keterangan                                  |
|------------------|--------|-----------------------|---------------------------------------------|
| `users`          | 1:1    | `members`             | 1 akun = 1 data anggota                    |
| `classes`        | 1:N    | `members`             | 1 kelas punya banyak anggota siswa          |
| `members`        | 1:N    | `loans`               | 1 anggota bisa punya banyak transaksi pinjam|
| `members`        | 1:N    | `visits`              | 1 anggota bisa kunjungan berkali-kali       |
| `members`        | 1:N    | `reservations`        | 1 anggota bisa antre banyak buku            |
| `members`        | 1:N    | `book_ratings`        | 1 anggota bisa rating banyak buku           |
| `members`        | 1:N    | `ebook_bookmarks`     | 1 anggota bisa bookmark banyak e-book       |
| `members`        | 1:N    | `ebook_reading_progress` | 1 anggota punya progres baca per e-book  |
| `categories`     | 1:N    | `books`               | 1 kategori punya banyak judul buku          |
| `books`          | 1:N    | `book_copies`         | 1 judul buku punya banyak eksemplar fisik   |
| `books`          | 1:N    | `reservations`        | 1 judul buku bisa diantrean banyak anggota  |
| `books`          | 1:N    | `book_ratings`        | 1 judul buku bisa punya banyak rating       |
| `loans`          | 1:N    | `loan_items`          | 1 transaksi bisa mencakup 1-2 buku          |
| `loans`          | 1:N    | `loan_extensions`     | 1 transaksi bisa diperpanjang beberapa kali |
| `loan_items`     | 1:N    | `fines`               | 1 item bisa punya >1 denda (terlambat+rusak)|
| `fines`          | 1:N    | `fine_payments`       | 1 denda bisa dibayar cicil                  |
| `book_copies`    | 1:N    | `loan_items`          | 1 eksemplar bisa dipinjam berkali-kali      |
| `users`          | 1:N    | `activity_logs`       | 1 user bisa punya banyak log aktivitas      |
| `members`        | 1:N    | `notifications`       | 1 anggota bisa punya banyak notifikasi      |
| `users`          | 1:N    | `fcm_tokens`          | 1 user bisa login di banyak perangkat       |
| —                | —      | `ebook_cache`         | Standalone cache, tidak ada FK ke tabel lain|

---

## 2. Tabel Master

### 2.1 `users`

Akun login sistem. Admin dan petugas dibuat manual oleh superadmin. Anggota mendaftar sendiri melalui halaman registrasi.

| Kolom            | Tipe                                      | Null | Default            | Keterangan                              |
|------------------|-------------------------------------------|------|--------------------|-----------------------------------------|
| `id`             | `BIGINT UNSIGNED`                         | NO   | AUTO_INCREMENT     | Primary key                             |
| `name`           | `VARCHAR(100)`                            | NO   | —                  | Nama lengkap                            |
| `email`          | `VARCHAR(150)`                            | NO   | —                  | Email untuk login (unique)              |
| `password`       | `VARCHAR(255)`                            | NO   | —                  | Password ter-hash (bcrypt)              |
| `role`           | `ENUM('admin','petugas','anggota')`       | NO   | `anggota`          | Hak akses pengguna                      |
| `remember_token` | `VARCHAR(100)`                            | YES  | NULL               | Token remember me                       |
| `created_at`     | `TIMESTAMP`                               | NO   | CURRENT_TIMESTAMP  | Waktu dibuat                            |
| `updated_at`     | `TIMESTAMP`                               | NO   | CURRENT_TIMESTAMP  | Waktu terakhir diubah                   |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `email`
- `INDEX` → `role`

---

### 2.2 `classes`

Data kelas untuk anggota siswa. Tampil sebagai dropdown saat registrasi jika tipe anggota = siswa.

| Kolom        | Tipe              | Null | Default           | Keterangan                          |
|--------------|-------------------|------|-------------------|-------------------------------------|
| `id`         | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                         |
| `name`       | `VARCHAR(30)`     | NO   | —                 | Nama kelas, cth: `XI IPA 1`         |
| `grade`      | `TINYINT UNSIGNED`| NO   | —                 | Tingkat: `10`, `11`, atau `12`      |
| `major`      | `VARCHAR(50)`     | YES  | NULL              | Jurusan: IPA, IPS, Bahasa, dll      |
| `is_active`  | `TINYINT(1)`      | NO   | `1`               | `0` = kelas sudah lulus/tidak aktif |
| `created_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu dibuat                        |
| `updated_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah               |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `grade`

---

### 2.3 `members`

Data anggota perpustakaan. Anggota mendaftar sendiri, sistem auto-generate `member_code` dan `qr_token` saat registrasi. Status awal selalu `pending` hingga admin menyetujui.

| Kolom              | Tipe                                                      | Null | Default           | Keterangan                                          |
|--------------------|-----------------------------------------------------------|------|-------------------|-----------------------------------------------------|
| `id`               | `BIGINT UNSIGNED`                                         | NO   | AUTO_INCREMENT    | Primary key                                         |
| `user_id`          | `BIGINT UNSIGNED`                                         | NO   | —                 | FK → `users.id` (wajib, cascade delete)             |
| `class_id`         | `BIGINT UNSIGNED`                                         | YES  | NULL              | FK → `classes.id` (wajib untuk siswa)               |
| `member_code`      | `VARCHAR(20)`                                             | NO   | —                 | Auto-generate. Format: `SIS-2025-001` (unique)      |
| `qr_token`         | `VARCHAR(100)`                                            | NO   | —                 | UUID v4. Isi QR profil anggota (unique)              |
| `name`             | `VARCHAR(100)`                                            | NO   | —                 | Nama lengkap anggota                                |
| `type`             | `ENUM('siswa','guru','umum')`                             | NO   | —                 | Tipe keanggotaan                                    |
| `nis_nip`          | `VARCHAR(30)`                                             | YES  | NULL              | NIS untuk siswa, NIP untuk guru                     |
| `phone`            | `VARCHAR(20)`                                             | YES  | NULL              | Nomor HP                                            |
| `address`          | `TEXT`                                                    | YES  | NULL              | Alamat lengkap                                      |
| `photo`            | `VARCHAR(255)`                                            | YES  | NULL              | Path foto profil                                    |
| `status`           | `ENUM('pending','aktif','nonaktif','suspended','ditolak')`| NO   | `pending`         | Status keanggotaan                                  |
| `expired_at`       | `DATE`                                                    | YES  | NULL              | Masa berlaku kartu. NULL = tidak ada batas          |
| `verified_at`      | `TIMESTAMP`                                               | YES  | NULL              | Waktu admin menyetujui akun                         |
| `verified_by`      | `BIGINT UNSIGNED`                                         | YES  | NULL              | FK → `users.id` (admin yang approve)                |
| `rejection_reason` | `TEXT`                                                    | YES  | NULL              | Alasan penolakan (jika status = ditolak)            |
| `suspend_reason`   | `TEXT`                                                    | YES  | NULL              | Alasan suspend (jika status = suspended)            |
| `notes`            | `TEXT`                                                    | YES  | NULL              | Catatan tambahan admin                              |
| `created_at`       | `TIMESTAMP`                                               | NO   | CURRENT_TIMESTAMP | Waktu registrasi                                    |
| `updated_at`       | `TIMESTAMP`                                               | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                               |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `member_code`
- `UNIQUE KEY` → `qr_token`
- `UNIQUE KEY` → `user_id` (1 user = 1 data anggota)
- `INDEX` → `status`, `type`, `class_id`
- `FOREIGN KEY` → `user_id` references `users(id)` ON DELETE CASCADE
- `FOREIGN KEY` → `class_id` references `classes(id)` ON DELETE SET NULL
- `FOREIGN KEY` → `verified_by` references `users(id)` ON DELETE SET NULL

> **Format `member_code`:**  
> `SIS-YYYY-NNN` → Siswa  
> `GUR-YYYY-NNN` → Guru  
> `UMM-YYYY-NNN` → Umum  
> `NNN` = nomor urut 3 digit padded (001, 002, dst)

---

### 2.4 `categories`

Kategori / klasifikasi buku perpustakaan.

| Kolom         | Tipe              | Null | Default           | Keterangan                         |
|---------------|-------------------|------|-------------------|------------------------------------|
| `id`          | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                        |
| `name`        | `VARCHAR(80)`     | NO   | —                 | Nama kategori, cth: `Fiksi Ilmiah` |
| `code`        | `VARCHAR(10)`     | NO   | —                 | Kode unik, cth: `FIK`, `SCI`       |
| `description` | `TEXT`            | YES  | NULL              | Deskripsi kategori                 |
| `created_at`  | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu dibuat                       |
| `updated_at`  | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah              |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `code`

---

### 2.5 `books`

Master data judul buku. Satu record mewakili satu judul buku, berapapun jumlah eksemplar fisiknya. `total_copies` dan `avg_rating` di-update otomatis via trigger.

| Kolom          | Tipe               | Null | Default           | Keterangan                                    |
|----------------|--------------------|------|-------------------|-----------------------------------------------|
| `id`           | `BIGINT UNSIGNED`  | NO   | AUTO_INCREMENT    | Primary key                                   |
| `category_id`  | `BIGINT UNSIGNED`  | NO   | —                 | FK → `categories.id`                          |
| `isbn`         | `VARCHAR(20)`      | YES  | NULL              | ISBN buku (unique jika diisi)                 |
| `title`        | `VARCHAR(255)`     | NO   | —                 | Judul buku                                    |
| `author`       | `VARCHAR(200)`     | NO   | —                 | Nama pengarang                                |
| `publisher`    | `VARCHAR(150)`     | YES  | NULL              | Penerbit                                      |
| `year`         | `YEAR`             | YES  | NULL              | Tahun terbit                                  |
| `edition`      | `VARCHAR(20)`      | YES  | NULL              | Edisi buku                                    |
| `language`     | `VARCHAR(30)`      | NO   | `Indonesia`       | Bahasa buku                                   |
| `pages`        | `SMALLINT UNSIGNED`| YES  | NULL              | Jumlah halaman                                |
| `description`  | `TEXT`             | YES  | NULL              | Sinopsis / deskripsi                          |
| `cover_image`  | `VARCHAR(255)`     | YES  | NULL              | Path gambar sampul                            |
| `rack_number`  | `VARCHAR(20)`      | YES  | NULL              | Nomor rak lokasi fisik buku di perpustakaan   |
| `total_copies` | `SMALLINT UNSIGNED`| NO   | `0`               | Jumlah eksemplar aktif (auto via trigger)     |
| `avg_rating`   | `DECIMAL(3,2)`     | NO   | `0.00`            | Rata-rata rating 1-5 (auto via trigger)       |
| `total_loans`  | `INT UNSIGNED`     | NO   | `0`               | Total peminjaman (counter popularitas buku)   |
| `created_at`   | `TIMESTAMP`        | NO   | CURRENT_TIMESTAMP | Waktu ditambahkan                             |
| `updated_at`   | `TIMESTAMP`        | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                         |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `isbn`
- `INDEX` → `category_id`
- `FULLTEXT INDEX` → `title`, `author` (untuk fitur pencarian buku)
- `FOREIGN KEY` → `category_id` references `categories(id)`

---

### 2.6 `book_copies`

Eksemplar fisik per buku. Setiap eksemplar punya `barcode` unik yang dicetak dan ditempel di buku fisik. Saat peminjaman, petugas scan barcode eksemplar ini — bukan barcode judul buku.

| Kolom       | Tipe                                                      | Null | Default           | Keterangan                                         |
|-------------|-----------------------------------------------------------|------|-------------------|----------------------------------------------------|
| `id`        | `BIGINT UNSIGNED`                                         | NO   | AUTO_INCREMENT    | Primary key                                        |
| `book_id`   | `BIGINT UNSIGNED`                                         | NO   | —                 | FK → `books.id`                                    |
| `copy_code` | `VARCHAR(30)`                                             | NO   | —                 | Kode eksemplar unik, cth: `BK-001-E1` (unique)     |
| `barcode`   | `VARCHAR(50)`                                             | NO   | —                 | Barcode unik ditempel di buku fisik (unique)       |
| `condition` | `ENUM('baik','rusak_ringan','rusak_berat','hilang')`      | NO   | `baik`            | Kondisi fisik buku saat ini                        |
| `status`    | `ENUM('tersedia','dipinjam','direservasi','tidak_aktif')` | NO   | `tersedia`        | Status ketersediaan eksemplar                      |
| `notes`     | `TEXT`                                                    | YES  | NULL              | Catatan kondisi atau keterangan khusus             |
| `created_at`| `TIMESTAMP`                                               | NO   | CURRENT_TIMESTAMP | Waktu eksemplar ditambahkan                        |
| `updated_at`| `TIMESTAMP`                                               | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                              |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `copy_code`
- `UNIQUE KEY` → `barcode`
- `INDEX` → `book_id`, `status`
- `FOREIGN KEY` → `book_id` references `books(id)` ON DELETE CASCADE

> **Format `copy_code`:**  
> `BK-{book_id_padded}-E{nomor_eksemplar}`  
> Contoh: Buku ke-1 eksemplar ke-3 → `BK-001-E3`

---

## 3. Tabel Transaksi

### 3.1 `loans`

Header transaksi peminjaman. Satu record mewakili satu sesi peminjaman yang bisa mencakup 1-2 buku sekaligus.

| Kolom                  | Tipe                                                    | Null | Default           | Keterangan                                              |
|------------------------|---------------------------------------------------------|------|-------------------|---------------------------------------------------------|
| `id`                   | `BIGINT UNSIGNED`                                       | NO   | AUTO_INCREMENT    | Primary key                                             |
| `loan_code`            | `VARCHAR(30)`                                           | NO   | —                 | Kode transaksi unik, cth: `TRX-20250901-001`            |
| `member_id`            | `BIGINT UNSIGNED`                                       | NO   | —                 | FK → `members.id`                                       |
| `loan_date`            | `DATE`                                                  | NO   | —                 | Tanggal peminjaman                                      |
| `due_date`             | `DATE`                                                  | NO   | —                 | Tanggal wajib kembali (auto via `sp_calculate_due_date`)|
| `loan_type`            | `ENUM('pembaca','lomba')`                               | NO   | `pembaca`         | Tujuan peminjaman                                       |
| `competition_end_date` | `DATE`                                                  | YES  | NULL              | Tanggal selesai lomba (wajib jika `loan_type = lomba`)  |
| `extended_count`       | `TINYINT UNSIGNED`                                      | NO   | `0`               | Jumlah perpanjangan yang sudah dilakukan                |
| `extended_due_date`    | `DATE`                                                  | YES  | NULL              | Due date terbaru setelah perpanjangan terakhir          |
| `status`               | `ENUM('aktif','diperpanjang','terlambat','selesai','hilang')` | NO | `aktif`     | Status transaksi peminjaman                             |
| `created_by`           | `BIGINT UNSIGNED`                                       | NO   | —                 | FK → `users.id` (petugas/admin yang input)              |
| `notes`                | `TEXT`                                                  | YES  | NULL              | Catatan transaksi                                       |
| `created_at`           | `TIMESTAMP`                                             | NO   | CURRENT_TIMESTAMP | Waktu transaksi dibuat                                  |
| `updated_at`           | `TIMESTAMP`                                             | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                                   |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `loan_code`
- `INDEX` → `member_id`, `due_date`, `status`, `created_at`
- `FOREIGN KEY` → `member_id` references `members(id)`
- `FOREIGN KEY` → `created_by` references `users(id)`

> **Format `loan_code`:**  
> `TRX-YYYYMMDD-NNN`  
> `NNN` = nomor urut harian, reset setiap hari

---

### 3.2 `loan_items`

Detail buku per transaksi peminjaman. Satu `loans` bisa punya 1-2 `loan_items`. Denda, kondisi buku setelah kembali, dan siapa yang menerima pengembalian dicatat di level ini.

| Kolom              | Tipe                                                     | Null | Default           | Keterangan                                    |
|--------------------|----------------------------------------------------------|------|-------------------|-----------------------------------------------|
| `id`               | `BIGINT UNSIGNED`                                        | NO   | AUTO_INCREMENT    | Primary key                                   |
| `loan_id`          | `BIGINT UNSIGNED`                                        | NO   | —                 | FK → `loans.id`                               |
| `copy_id`          | `BIGINT UNSIGNED`                                        | NO   | —                 | FK → `book_copies.id`                         |
| `returned_at`      | `TIMESTAMP`                                              | YES  | NULL              | Waktu dikembalikan. NULL = belum dikembalikan |
| `condition_after`  | `ENUM('baik','rusak_ringan','rusak_berat','hilang')`     | YES  | NULL              | Kondisi buku saat dikembalikan                |
| `returned_by`      | `BIGINT UNSIGNED`                                        | YES  | NULL              | FK → `users.id` (petugas penerima)            |
| `notes`            | `TEXT`                                                   | YES  | NULL              | Catatan kondisi atau keterangan               |
| `created_at`       | `TIMESTAMP`                                              | NO   | CURRENT_TIMESTAMP | Waktu item dibuat                             |
| `updated_at`       | `TIMESTAMP`                                              | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                         |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `loan_id`, `copy_id`, `returned_at`
- `FOREIGN KEY` → `loan_id` references `loans(id)` ON DELETE CASCADE
- `FOREIGN KEY` → `copy_id` references `book_copies(id)`
- `FOREIGN KEY` → `returned_by` references `users(id)` ON DELETE SET NULL

---

### 3.3 `loan_extensions`

Riwayat setiap perpanjangan peminjaman. Diperlukan untuk audit trail dan mencegah perpanjangan melebihi batas yang dikonfigurasi di `settings`.

| Kolom          | Tipe              | Null | Default           | Keterangan                              |
|----------------|-------------------|------|-------------------|-----------------------------------------|
| `id`           | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                             |
| `loan_id`      | `BIGINT UNSIGNED` | NO   | —                 | FK → `loans.id`                         |
| `old_due_date` | `DATE`            | NO   | —                 | Due date sebelum diperpanjang           |
| `new_due_date` | `DATE`            | NO   | —                 | Due date baru setelah diperpanjang      |
| `extended_by`  | `BIGINT UNSIGNED` | NO   | —                 | FK → `users.id` (yang approve)          |
| `notes`        | `TEXT`            | YES  | NULL              | Alasan perpanjangan                     |
| `created_at`   | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu perpanjangan dilakukan            |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `loan_id`
- `FOREIGN KEY` → `loan_id` references `loans(id)` ON DELETE CASCADE
- `FOREIGN KEY` → `extended_by` references `users(id)`

---

### 3.4 `fines`

Denda per `loan_item`. Satu item bisa menghasilkan lebih dari satu denda — misalnya terlambat sekaligus buku rusak akan menghasilkan dua record denda terpisah. Semua nominal dibaca dari `settings` saat insert.

| Kolom          | Tipe                                           | Null | Default           | Keterangan                                    |
|----------------|------------------------------------------------|------|-------------------|-----------------------------------------------|
| `id`           | `BIGINT UNSIGNED`                              | NO   | AUTO_INCREMENT    | Primary key                                   |
| `loan_item_id` | `BIGINT UNSIGNED`                              | NO   | —                 | FK → `loan_items.id`                          |
| `fine_type`    | `ENUM('keterlambatan','kerusakan','kehilangan')`| NO   | —                 | Jenis denda                                   |
| `days_late`    | `SMALLINT UNSIGNED`                            | YES  | NULL              | Jumlah hari terlambat (hanya untuk keterlambatan) |
| `amount`       | `INT UNSIGNED`                                 | NO   | —                 | Nominal denda dalam rupiah                    |
| `status`       | `ENUM('belum_lunas','lunas','dibebaskan')`      | NO   | `belum_lunas`     | Status pembayaran denda                       |
| `paid_at`      | `TIMESTAMP`                                    | YES  | NULL              | Waktu denda dinyatakan lunas                  |
| `confirmed_by` | `BIGINT UNSIGNED`                              | YES  | NULL              | FK → `users.id` (petugas yang konfirmasi)     |
| `freed_by`     | `BIGINT UNSIGNED`                              | YES  | NULL              | FK → `users.id` (admin yang bebaskan)         |
| `freed_reason` | `TEXT`                                         | YES  | NULL              | Alasan pembebasan denda                       |
| `notes`        | `TEXT`                                         | YES  | NULL              | Catatan tambahan                              |
| `created_at`   | `TIMESTAMP`                                    | NO   | CURRENT_TIMESTAMP | Waktu denda dibuat                            |
| `updated_at`   | `TIMESTAMP`                                    | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                         |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `loan_item_id`, `status`, `fine_type`
- `FOREIGN KEY` → `loan_item_id` references `loan_items(id)` ON DELETE CASCADE
- `FOREIGN KEY` → `confirmed_by` references `users(id)` ON DELETE SET NULL
- `FOREIGN KEY` → `freed_by` references `users(id)` ON DELETE SET NULL

---

### 3.5 `fine_payments`

Riwayat pembayaran denda. Mendukung pembayaran cicil — satu denda bisa dilunasi melalui beberapa kali pembayaran.

| Kolom             | Tipe              | Null | Default           | Keterangan                          |
|-------------------|-------------------|------|-------------------|-------------------------------------|
| `id`              | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                         |
| `fine_id`         | `BIGINT UNSIGNED` | NO   | —                 | FK → `fines.id`                     |
| `amount_paid`     | `INT UNSIGNED`    | NO   | —                 | Nominal yang dibayarkan (Rp)        |
| `payment_date`    | `DATE`            | NO   | —                 | Tanggal pembayaran                  |
| `receipt_number`  | `VARCHAR(50)`     | YES  | NULL              | Nomor kwitansi                      |
| `received_by`     | `BIGINT UNSIGNED` | NO   | —                 | FK → `users.id` (petugas penerima)  |
| `notes`           | `TEXT`            | YES  | NULL              | Catatan pembayaran                  |
| `created_at`      | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu pembayaran dicatat            |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `fine_id`
- `FOREIGN KEY` → `fine_id` references `fines(id)` ON DELETE CASCADE
- `FOREIGN KEY` → `received_by` references `users(id)`

---

### 3.6 `reservations`

Antrean buku yang sedang dipinjam orang lain. Saat buku dikembalikan, sistem otomatis notifikasi anggota pertama dalam antrean.

| Kolom          | Tipe                                                              | Null | Default           | Keterangan                                        |
|----------------|-------------------------------------------------------------------|------|-------------------|---------------------------------------------------|
| `id`           | `BIGINT UNSIGNED`                                                 | NO   | AUTO_INCREMENT    | Primary key                                       |
| `member_id`    | `BIGINT UNSIGNED`                                                 | NO   | —                 | FK → `members.id`                                 |
| `book_id`      | `BIGINT UNSIGNED`                                                 | NO   | —                 | FK → `books.id`                                   |
| `queue_number` | `TINYINT UNSIGNED`                                                | NO   | `1`               | Urutan antrean untuk judul yang sama              |
| `status`       | `ENUM('menunggu','tersedia','diambil','dibatalkan','kadaluarsa')` | NO   | `menunggu`        | Status reservasi                                  |
| `notified_at`  | `TIMESTAMP`                                                       | YES  | NULL              | Waktu notifikasi "buku tersedia" dikirim          |
| `expires_at`   | `TIMESTAMP`                                                       | YES  | NULL              | Batas waktu pengambilan setelah notifikasi        |
| `notes`        | `TEXT`                                                            | YES  | NULL              | Catatan reservasi                                 |
| `created_at`   | `TIMESTAMP`                                                       | NO   | CURRENT_TIMESTAMP | Waktu reservasi dibuat                            |
| `updated_at`   | `TIMESTAMP`                                                       | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                             |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `member_id`, `book_id`, `status`
- `FOREIGN KEY` → `member_id` references `members(id)`
- `FOREIGN KEY` → `book_id` references `books(id)`

---

### 3.7 `visits`

Presensi kunjungan harian perpustakaan via scan QR profil anggota.

| Kolom        | Tipe                                         | Null | Default           | Keterangan                              |
|--------------|----------------------------------------------|------|-------------------|-----------------------------------------|
| `id`         | `BIGINT UNSIGNED`                            | NO   | AUTO_INCREMENT    | Primary key                             |
| `member_id`  | `BIGINT UNSIGNED`                            | NO   | —                 | FK → `members.id`                       |
| `visit_date` | `DATE`                                       | NO   | —                 | Tanggal kunjungan                       |
| `visit_time` | `TIME`                                       | NO   | —                 | Jam kunjungan                           |
| `category`   | `ENUM('membaca','pengunjung','peminjam')`     | NO   | `pengunjung`      | Tujuan kunjungan                        |
| `scanned_by` | `BIGINT UNSIGNED`                            | YES  | NULL              | FK → `users.id` (petugas yang scan)     |
| `notes`      | `VARCHAR(255)`                               | YES  | NULL              | Catatan kunjungan                       |
| `created_at` | `TIMESTAMP`                                  | NO   | CURRENT_TIMESTAMP | Waktu kunjungan dicatat                 |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `INDEX` → `member_id`, `visit_date`, `category`
- `FOREIGN KEY` → `member_id` references `members(id)`
- `FOREIGN KEY` → `scanned_by` references `users(id)` ON DELETE SET NULL

---

### 3.8 `book_ratings`

Rating dan ulasan buku dari anggota. Satu anggota hanya bisa memberi rating satu kali per judul. `avg_rating` di tabel `books` di-update otomatis oleh trigger.

| Kolom        | Tipe              | Null | Default           | Keterangan                              |
|--------------|-------------------|------|-------------------|-----------------------------------------|
| `id`         | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                             |
| `book_id`    | `BIGINT UNSIGNED` | NO   | —                 | FK → `books.id`                         |
| `member_id`  | `BIGINT UNSIGNED` | NO   | —                 | FK → `members.id`                       |
| `rating`     | `TINYINT UNSIGNED`| NO   | —                 | Nilai 1 sampai 5 bintang                |
| `review`     | `TEXT`            | YES  | NULL              | Ulasan / komentar anggota               |
| `created_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu rating diberikan                  |
| `updated_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                   |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `(book_id, member_id)` — 1 anggota hanya bisa rating 1x per buku
- `FOREIGN KEY` → `book_id` references `books(id)` ON DELETE CASCADE
- `FOREIGN KEY` → `member_id` references `members(id)`

---

## 4. Tabel Sistem

### 4.1 `settings`

Konfigurasi sistem yang bisa diedit admin kapan saja melalui halaman pengaturan tanpa menyentuh kode program.

| Kolom         | Tipe                                     | Null | Default           | Keterangan                           |
|---------------|------------------------------------------|------|-------------------|--------------------------------------|
| `id`          | `BIGINT UNSIGNED`                        | NO   | AUTO_INCREMENT    | Primary key                          |
| `key`         | `VARCHAR(80)`                            | NO   | —                 | Nama konfigurasi (unique)            |
| `value`       | `TEXT`                                   | NO   | —                 | Nilai konfigurasi                    |
| `type`        | `ENUM('string','integer','boolean','json')` | NO | `string`         | Tipe data value                      |
| `group`       | `VARCHAR(40)`                            | NO   | `general`         | Grup konfigurasi                     |
| `description` | `VARCHAR(255)`                           | YES  | NULL              | Penjelasan konfigurasi               |
| `updated_by`  | `BIGINT UNSIGNED`                        | YES  | NULL              | FK → `users.id` (admin terakhir edit)|
| `created_at`  | `TIMESTAMP`                              | NO   | CURRENT_TIMESTAMP | Waktu dibuat                         |
| `updated_at`  | `TIMESTAMP`                              | NO   | CURRENT_TIMESTAMP | Waktu terakhir diubah                |

**Default Settings:**

| Key                     | Value  | Group       | Keterangan                                    |
|-------------------------|--------|-------------|-----------------------------------------------|
| `denda_per_hari`        | `1000` | denda       | Denda keterlambatan per hari (Rp)             |
| `denda_rusak_ringan`    | `10000`| denda       | Denda buku rusak ringan (Rp)                  |
| `denda_rusak_berat`     | `30000`| denda       | Denda buku rusak berat (Rp)                   |
| `denda_hilang`          | `50000`| denda       | Denda buku hilang (Rp)                        |
| `max_pinjam`            | `2`    | peminjaman  | Maksimum buku dipinjam per anggota            |
| `lama_pinjam_hari`      | `3`    | peminjaman  | Lama peminjaman default (hari kerja)          |
| `max_perpanjangan`      | `1`    | peminjaman  | Maksimum perpanjangan per transaksi           |
| `aktif_rules_rabu_kamis`| `true` | peminjaman  | Aktifkan rules Rabu/Kamis → kembali Senin     |
| `batas_ambil_reservasi` | `2`    | reservasi   | Hari batas ambil buku reservasi setelah notif |
| `nama_perpustakaan`     | `...`  | general     | Nama perpustakaan                             |
| `nama_sekolah`          | `...`  | general     | Nama sekolah                                  |

---

### 4.2 `notifications`

Notifikasi in-app dan push notification ke mobile (via FCM).

| Kolom        | Tipe                | Null | Default           | Keterangan                                      |
|--------------|---------------------|------|-------------------|-------------------------------------------------|
| `id`         | `BIGINT UNSIGNED`   | NO   | AUTO_INCREMENT    | Primary key                                     |
| `member_id`  | `BIGINT UNSIGNED`   | NO   | —                 | FK → `members.id`                               |
| `type`       | `ENUM(...)`         | NO   | —                 | Lihat Enum Reference                            |
| `title`      | `VARCHAR(150)`      | NO   | —                 | Judul notifikasi                                |
| `body`       | `TEXT`              | NO   | —                 | Isi notifikasi                                  |
| `data`       | `JSON`              | YES  | NULL              | Payload tambahan: `loan_id`, `fine_id`, dll     |
| `is_read`    | `TINYINT(1)`        | NO   | `0`               | `0` = belum dibaca, `1` = sudah dibaca          |
| `sent_at`    | `TIMESTAMP`         | YES  | NULL              | Waktu push notification dikirim ke FCM          |
| `read_at`    | `TIMESTAMP`         | YES  | NULL              | Waktu notifikasi dibuka anggota                 |
| `created_at` | `TIMESTAMP`         | NO   | CURRENT_TIMESTAMP | Waktu notifikasi dibuat                         |

---

### 4.3 `fcm_tokens`

Token Firebase Cloud Messaging per perangkat mobile. Satu user bisa punya lebih dari satu token jika login di beberapa perangkat.

| Kolom        | Tipe              | Null | Default           | Keterangan                                 |
|--------------|-------------------|------|-------------------|--------------------------------------------|
| `id`         | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                                |
| `user_id`    | `BIGINT UNSIGNED` | NO   | —                 | FK → `users.id`                            |
| `token`      | `TEXT`            | NO   | —                 | FCM registration token dari perangkat      |
| `device`     | `VARCHAR(100)`    | YES  | NULL              | Info perangkat, cth: `Samsung A52 Android 13` |
| `created_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu token didaftarkan                    |
| `updated_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu token diperbarui                     |

---

### 4.4 `activity_logs`

Audit trail semua aksi admin dan petugas. Dicatat otomatis oleh Laravel Observer atau Service class.

| Kolom        | Tipe              | Null | Default           | Keterangan                                       |
|--------------|-------------------|------|-------------------|--------------------------------------------------|
| `id`         | `BIGINT UNSIGNED` | NO   | AUTO_INCREMENT    | Primary key                                      |
| `user_id`    | `BIGINT UNSIGNED` | YES  | NULL              | FK → `users.id` (NULL jika aksi sistem otomatis) |
| `action`     | `VARCHAR(80)`     | NO   | —                 | Nama aksi, cth: `approve_member`, `create_loan`  |
| `model_type` | `VARCHAR(80)`     | YES  | NULL              | Nama model, cth: `App\Models\Member`             |
| `model_id`   | `BIGINT UNSIGNED` | YES  | NULL              | ID record yang terpengaruh                       |
| `old_values` | `JSON`            | YES  | NULL              | Nilai data sebelum diubah                        |
| `new_values` | `JSON`            | YES  | NULL              | Nilai data setelah diubah                        |
| `ip_address` | `VARCHAR(45)`     | YES  | NULL              | IP address pengguna                              |
| `user_agent` | `VARCHAR(255)`    | YES  | NULL              | Browser / device info                            |
| `created_at` | `TIMESTAMP`       | NO   | CURRENT_TIMESTAMP | Waktu aksi dilakukan                             |

---

## 5. Tabel E-Book

Tiga tabel berikut menangani integrasi buku digital dari sumber eksternal: **Open Library**, **Google Books**, dan **Project Gutenberg**. Berbeda dengan buku fisik, e-book tidak disimpan di server — sistem hanya menyimpan referensi (ID eksternal) dan metadata-nya.

### 5.1 `ebook_bookmarks`

Buku digital yang disimpan / di-bookmark anggota dari sumber eksternal. Anggota bisa menandai buku sebagai favorit dan melacak kapan terakhir dibaca.

| Kolom         | Tipe                                                       | Null | Default           | Keterangan                                    |
|---------------|------------------------------------------------------------|------|-------------------|-----------------------------------------------|
| `id`          | `BIGINT UNSIGNED`                                          | NO   | AUTO_INCREMENT    | Primary key                                   |
| `member_id`   | `BIGINT UNSIGNED`                                          | NO   | —                 | FK → `members.id` (cascade delete)            |
| `source`      | `ENUM('openlibrary','googlebooks','gutenberg')`            | NO   | —                 | Sumber API buku digital                       |
| `external_id` | `VARCHAR(100)`                                             | NO   | —                 | ID buku di sumber eksternal                   |
| `title`       | `VARCHAR(255)`                                             | NO   | —                 | Judul buku                                    |
| `author`      | `VARCHAR(200)`                                             | YES  | NULL              | Nama pengarang                                |
| `cover_url`   | `VARCHAR(500)`                                             | YES  | NULL              | URL gambar sampul dari API eksternal          |
| `read_url`    | `VARCHAR(500)`                                             | YES  | NULL              | URL untuk membaca buku online                 |
| `last_read`   | `TIMESTAMP`                                                | YES  | NULL              | Waktu terakhir anggota membuka buku ini       |
| `is_favorite` | `TINYINT(1)`                                               | NO   | `0`               | `1` = ditandai favorit oleh anggota           |
| `created_at`  | `TIMESTAMP`                                                | NO   | CURRENT_TIMESTAMP | Waktu pertama kali di-bookmark                |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `(member_id, source, external_id)` — 1 anggota tidak bisa bookmark buku yang sama 2x
- `FOREIGN KEY` → `member_id` references `members(id)` ON DELETE CASCADE

---

### 5.2 `ebook_reading_progress`

Menyimpan posisi/progres membaca terakhir anggota per e-book. Data progres disimpan sebagai JSON agar fleksibel untuk berbagai format reader (nomor halaman, persentase, scroll position, dll).

| Kolom           | Tipe                                                       | Null | Default                       | Keterangan                                          |
|-----------------|------------------------------------------------------------|------|-------------------------------|-----------------------------------------------------|
| `id`            | `BIGINT UNSIGNED`                                          | NO   | AUTO_INCREMENT                | Primary key                                         |
| `member_id`     | `BIGINT UNSIGNED`                                          | NO   | —                             | FK → `members.id` (cascade delete)                  |
| `source`        | `ENUM('openlibrary','googlebooks','gutenberg')`            | NO   | —                             | Sumber API buku digital                             |
| `external_id`   | `VARCHAR(100)`                                             | NO   | —                             | ID buku di sumber eksternal                         |
| `progress_data` | `JSON`                                                     | YES  | NULL                          | Progres: `page`, `percentage`, `last_position`, dll |
| `updated_at`    | `TIMESTAMP`                                                | NO   | CURRENT_TIMESTAMP ON UPDATE   | Otomatis diperbarui setiap kali progres berubah     |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `(member_id, source, external_id)` — 1 record progres per anggota per buku
- `FOREIGN KEY` → `member_id` references `members(id)` ON DELETE CASCADE

> **Contoh `progress_data`:**
> ```json
> { "page": 42, "percentage": 35.5, "last_position": "chapter-3-section-2" }
> ```

---

### 5.3 `ebook_cache`

Cache metadata buku dari API eksternal agar tidak perlu fetch berulang kali. Laravel Scheduler bisa membersihkan record yang sudah melewati `expires_at` secara periodik. Tabel ini **tidak memiliki FK** karena bersifat standalone cache.

| Kolom         | Tipe                                                       | Null | Default           | Keterangan                                       |
|---------------|------------------------------------------------------------|------|-------------------|--------------------------------------------------|
| `id`          | `BIGINT UNSIGNED`                                          | NO   | AUTO_INCREMENT    | Primary key                                      |
| `source`      | `ENUM('openlibrary','googlebooks','gutenberg')`            | NO   | —                 | Sumber API buku digital                          |
| `external_id` | `VARCHAR(100)`                                             | NO   | —                 | ID buku di sumber eksternal                      |
| `data`        | `JSON`                                                     | NO   | —                 | Raw response dari API (metadata lengkap)         |
| `cached_at`   | `TIMESTAMP`                                                | NO   | CURRENT_TIMESTAMP | Waktu data di-cache                              |
| `expires_at`  | `TIMESTAMP`                                                | NO   | —                 | Waktu cache kadaluarsa, diisi saat insert        |

**Keys & Index:**
- `PRIMARY KEY` → `id`
- `UNIQUE KEY` → `(source, external_id)` — 1 cache entry per buku per sumber

> **Catatan:** Tidak ada `updated_at` karena cache di-invalidate dengan cara menghapus record (`DELETE`) lalu insert ulang, bukan update.

---

## 6. Views

| View                    | Sumber Tabel                               | Kegunaan                                                   |
|-------------------------|--------------------------------------------|------------------------------------------------------------|
| `v_member_scan_info`    | members, classes, loans, loan_items, fines | Data lengkap anggota saat petugas scan QR profil           |
| `v_active_loans`        | loans, members, loan_items, book_copies, books, classes | Semua peminjaman aktif + info detail           |
| `v_due_tomorrow`        | v_active_loans                             | Peminjaman jatuh tempo besok (untuk cron notifikasi H-1)   |
| `v_overdue_loans`       | v_active_loans                             | Peminjaman yang sudah melewati due date                    |
| `v_popular_books`       | books, categories, book_copies             | Buku terpopuler berdasarkan total_loans dan avg_rating     |
| `v_member_unpaid_fines` | members, loans, loan_items, fines          | Rekap denda belum lunas per anggota (untuk blokir pinjam)  |
| `v_pending_members`     | members, users, classes                    | Anggota dengan status pending (untuk badge admin)          |
| `v_visit_summary`       | visits, members                            | Rekap kunjungan harian dengan breakdown kategori dan tipe  |

---

## 7. Stored Procedures

### `sp_validate_member(p_qr_token, OUT p_result_code, OUT p_member_id, OUT p_message)`

Dipanggil saat petugas scan QR profil anggota. Menjalankan validasi berlapis secara berurutan.

**Urutan validasi:**
1. Cek apakah `qr_token` terdaftar di database
2. Cek status anggota (`pending`, `nonaktif`, `suspended`, `ditolak`)
3. Cek denda belum lunas
4. Cek kuota peminjaman (baca dari `settings.max_pinjam`)

**Output `p_result_code`:**

| Kode                  | Artinya                                    | Tindakan                             |
|-----------------------|--------------------------------------------|--------------------------------------|
| `OK`                  | Semua validasi lolos                       | Lanjut scan barcode buku             |
| `QR_TIDAK_DITEMUKAN`  | QR token tidak ada di database             | Cek ulang kartu anggota              |
| `PENDING`             | Akun belum diverifikasi admin              | Admin perlu approve akun             |
| `NONAKTIF`            | Akun dinonaktifkan                         | Hubungi admin                        |
| `SUSPENDED`           | Akun di-suspend                            | Hubungi admin, lihat alasan          |
| `DITOLAK`             | Registrasi ditolak                         | Daftar ulang atau hubungi admin      |
| `ADA_DENDA`           | Ada denda belum lunas                      | Selesaikan pembayaran denda dulu     |
| `KUOTA_PENUH`         | Sudah pinjam maks buku                     | Kembalikan buku dulu                 |

---

### `sp_calculate_due_date(p_loan_date, p_loan_type, p_competition_end_date, OUT p_due_date)`

Menghitung tanggal pengembalian otomatis sesuai rules.

**Rules:**
- Jika `loan_type = lomba` → `due_date = competition_end_date`
- Jika `loan_type = pembaca` → tambah N hari kerja (dari `settings.lama_pinjam_hari`), skip Sabtu-Minggu
- Jika hasil jatuh **Rabu** dan rules aktif → geser ke **Senin +5 hari**
- Jika hasil jatuh **Kamis** dan rules aktif → geser ke **Senin +4 hari**
- Rules Rabu-Kamis diaktifkan via `settings.aktif_rules_rabu_kamis`

---

### `sp_process_return(p_item_id, p_condition, p_returned_by, OUT p_fine_total)`

Memproses pengembalian satu buku (satu `loan_item`) secara atomik dalam satu transaksi.

**Urutan proses:**
1. Update `loan_items` → isi `returned_at`, `condition_after`, `returned_by`
2. Update `book_copies` → ubah `status` dan `condition` sesuai kondisi kembali
3. Hitung denda keterlambatan (baca `denda_per_hari` dari `settings`)
4. Insert denda kerusakan atau kehilangan jika kondisi tidak baik
5. Update `books.total_loans` += 1
6. Cek apakah semua item dalam transaksi sudah kembali → update `loans.status = selesai`
7. Cek antrean reservasi → update status reservasi pertama menjadi `tersedia` dan isi `notified_at`

---

## 8. Triggers

| Nama Trigger               | Event                    | Tabel          | Fungsi                                            |
|----------------------------|--------------------------|----------------|---------------------------------------------------|
| `trg_after_rating_insert`  | AFTER INSERT             | `book_ratings` | Update `books.avg_rating` setelah rating baru     |
| `trg_after_rating_update`  | AFTER UPDATE             | `book_ratings` | Update `books.avg_rating` setelah rating diubah   |
| `trg_after_rating_delete`  | AFTER DELETE             | `book_ratings` | Update `books.avg_rating` setelah rating dihapus  |
| `trg_after_copy_insert`    | AFTER INSERT             | `book_copies`  | Update `books.total_copies` saat eksemplar ditambah |
| `trg_after_copy_update`    | AFTER UPDATE             | `book_copies`  | Update `books.total_copies` saat kondisi berubah  |

---

## 9. Enum Reference

### `members.status`
| Value      | Keterangan                                                   |
|------------|--------------------------------------------------------------|
| `pending`  | Baru daftar, menunggu verifikasi admin. QR belum bisa dipakai|
| `aktif`    | Akun aktif, bisa meminjam buku                               |
| `nonaktif` | Dinonaktifkan admin (sementara atau permanen)                |
| `suspended`| Di-suspend karena pelanggaran, ada alasan di `suspend_reason`|
| `ditolak`  | Registrasi ditolak admin, ada alasan di `rejection_reason`   |

### `members.type`
| Value   | Keterangan                              |
|---------|-----------------------------------------|
| `siswa` | Pelajar SMA, wajib pilih kelas          |
| `guru`  | Tenaga pengajar, opsional NIP           |
| `umum`  | Masyarakat umum di luar sivitas sekolah |

### `book_copies.condition`
| Value          | Keterangan                              |
|----------------|-----------------------------------------|
| `baik`         | Kondisi normal, layak dipinjam          |
| `rusak_ringan` | Ada kerusakan kecil, masih bisa dipinjam|
| `rusak_berat`  | Kerusakan signifikan, perlu perbaikan   |
| `hilang`       | Buku hilang, eksemplar tidak aktif      |

### `book_copies.status`
| Value          | Keterangan                                    |
|----------------|-----------------------------------------------|
| `tersedia`     | Bisa dipinjam                                 |
| `dipinjam`     | Sedang dipinjam anggota                       |
| `direservasi`  | Sudah ada yang antre, menunggu dikembalikan   |
| `tidak_aktif`  | Tidak bisa dipinjam (hilang atau rusak berat) |

### `loans.loan_type`
| Value     | Keterangan                                                         |
|-----------|--------------------------------------------------------------------|
| `pembaca` | Peminjaman biasa, due date dihitung otomatis +3 hari kerja         |
| `lomba`   | Untuk persiapan lomba, due date = `competition_end_date`           |

### `loans.status`
| Value         | Keterangan                                          |
|---------------|-----------------------------------------------------|
| `aktif`       | Dalam masa peminjaman normal                        |
| `diperpanjang`| Sudah pernah diperpanjang minimal 1x                |
| `terlambat`   | Sudah melewati due date, belum dikembalikan         |
| `selesai`     | Semua buku dalam transaksi sudah dikembalikan       |
| `hilang`      | Buku dinyatakan hilang                              |

### `fines.fine_type`
| Value            | Keterangan                                       |
|------------------|--------------------------------------------------|
| `keterlambatan`  | Terlambat kembalikan, dihitung per hari          |
| `kerusakan`      | Buku kembali dalam kondisi rusak                 |
| `kehilangan`     | Buku tidak dikembalikan / hilang                 |

### `notifications.type`
| Value                    | Kapan Dikirim                                    |
|--------------------------|--------------------------------------------------|
| `akun_diaktifkan`        | Admin menyetujui registrasi anggota              |
| `akun_ditolak`           | Admin menolak registrasi anggota                 |
| `reminder_pengembalian`  | H-1 sebelum due date (cron harian)               |
| `terlambat_pengembalian` | Hari H dan setelahnya jika belum dikembalikan    |
| `denda_baru`             | Denda baru muncul setelah pengembalian           |
| `denda_lunas`            | Pembayaran denda dikonfirmasi petugas            |
| `reservasi_tersedia`     | Buku yang diantrean sudah dikembalikan           |
| `reservasi_kadaluarsa`   | Batas waktu ambil reservasi terlewat             |
| `perpanjangan_berhasil`  | Perpanjangan disetujui petugas                   |
| `info`                   | Pengumuman umum dari admin                       |

### `visits.category`
| Value        | Keterangan                                         |
|--------------|----------------------------------------------------|
| `membaca`    | Datang untuk membaca buku di tempat                |
| `pengunjung` | Berkunjung tanpa tujuan spesifik                   |
| `peminjam`   | Datang untuk meminjam atau mengembalikan buku      |

### `ebook_bookmarks.source` / `ebook_reading_progress.source` / `ebook_cache.source`
| Value          | Keterangan                                    |
|----------------|-----------------------------------------------|
| `openlibrary`  | Open Library (Internet Archive)               |
| `googlebooks`  | Google Books API                              |
| `gutenberg`    | Project Gutenberg (buku domain publik gratis) |

---

## 10. Index & Performa

### Index Kritis untuk Query Harian

| Tabel        | Index               | Kolom                    | Alasan                                             |
|--------------|---------------------|--------------------------|----------------------------------------------------|
| `members`    | `status_idx`        | `status`                 | Filter anggota pending, aktif, suspended           |
| `members`    | `qr_token_unique`   | `qr_token`               | Lookup saat scan QR profil (sangat sering)         |
| `book_copies`| `barcode_unique`    | `barcode`                | Lookup saat scan barcode buku (sangat sering)      |
| `book_copies`| `status_idx`        | `status`                 | Filter buku tersedia                               |
| `loans`      | `due_date_idx`      | `due_date`               | Query cron H-1 dan laporan keterlambatan           |
| `loans`      | `status_idx`        | `status`                 | Filter peminjaman aktif/terlambat                  |
| `loan_items` | `returned_idx`      | `returned_at`            | Filter buku yang belum dikembalikan                |
| `fines`      | `status_idx`        | `status`                 | Cek denda belum lunas saat validasi anggota        |
| `visits`     | `date_idx`          | `visit_date`             | Laporan kunjungan harian                           |
| `books`      | `search_idx`        | `title`, `author`        | FULLTEXT search pencarian buku                     |

| `ebook_bookmarks`        | `member_source_book_unique` | `member_id`, `source`, `external_id` | Lookup bookmark per anggota per buku |
| `ebook_reading_progress` | `progress_member_book_unique`| `member_id`, `source`, `external_id` | Lookup progres baca per anggota     |
| `ebook_cache`            | `cache_source_book_unique`  | `source`, `external_id`              | Lookup cache sebelum hit API eksternal|

---

*Dokumen ini adalah referensi desain database Sistem Perpustakaan SMA.*  
*Terakhir diperbarui: 2026 — Schema v2.1 (+ E-Book Tables)*
