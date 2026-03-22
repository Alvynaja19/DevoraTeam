-- ============================================================
--  SISTEM PERPUSTAKAAN SMA
--  MySQL Schema v2 — Updated sesuai alur kerja final
--  Laravel 11 + Inertia.js + Vue 3 + Flutter
--
--  Perubahan dari v1:
--  - members: tambah status pending, qr_token, verified_at,
--             verified_by, rejection_reason
--  - loans: tambah extended_count, extended_due_date
--  - tambah tabel loan_extensions (riwayat perpanjangan)
--  - sp_process_return: baca nominal denda dari tabel settings
--  - sp_validate_member: validasi saat scan QR profil
--  - sp_create_loan: buat transaksi peminjaman lengkap
--  - view v_member_scan_info: data lengkap saat scan QR profil
--  - view v_due_tomorrow: untuk cron notifikasi H-1
--  - notifikasi: tambah tipe akun_diaktifkan, akun_ditolak,
--                reservasi_tersedia, perpanjangan_berhasil
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';


-- ============================================================
-- 1. USERS
--    Akun login sistem. Anggota register sendiri dengan
--    role default 'anggota'. Admin & petugas dibuat manual.
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`             BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`           VARCHAR(100)     NOT NULL,
    `email`          VARCHAR(150)     NOT NULL,
    `password`       VARCHAR(255)     NOT NULL,
    `role`           ENUM('admin','petugas','anggota') NOT NULL DEFAULT 'anggota',
    `remember_token` VARCHAR(100)     NULL,
    `created_at`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    KEY `users_role_idx` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. CLASSES
--    Data kelas untuk anggota siswa.
--    Tampil sebagai dropdown saat register (jika tipe = siswa)
--    dan di halaman manajemen anggota admin.
-- ============================================================
CREATE TABLE IF NOT EXISTS `classes` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(30)      NOT NULL COMMENT 'cth: XI IPA 1',
    `grade`      TINYINT UNSIGNED NOT NULL COMMENT '10, 11, atau 12',
    `major`      VARCHAR(50)      NULL     COMMENT 'IPA, IPS, Bahasa, dll',
    `is_active`  TINYINT(1)       NOT NULL DEFAULT 1 COMMENT '0 = kelas sudah tidak aktif (lulus)',
    `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `classes_grade_idx` (`grade`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. MEMBERS
--    Data anggota perpustakaan.
--
--    Alur register mandiri:
--    1. Pengunjung isi form → insert ke users + members
--    2. Sistem auto-generate member_code & qr_token
--    3. Status awal = 'pending'
--    4. QR sudah bisa dilihat di profil tapi belum bisa pinjam
--    5. Admin approve → status = 'aktif', isi verified_at & verified_by
--    6. Admin tolak  → status = 'ditolak', isi rejection_reason
--
--    qr_token = string unik isi QR profil anggota.
--    Format: UUID v4, cth: 550e8400-e29b-41d4-a716-446655440000
--    Dipakai untuk scan presensi, peminjaman, dan pengembalian.
--    BUKAN barcode buku — keduanya sistem berbeda.
-- ============================================================
CREATE TABLE IF NOT EXISTS `members` (
    `id`               BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`          BIGINT UNSIGNED  NOT NULL COMMENT 'FK ke users, wajib ada akun login',
    `class_id`         BIGINT UNSIGNED  NULL     COMMENT 'FK ke classes, wajib untuk siswa',
    `member_code`      VARCHAR(20)      NOT NULL COMMENT 'Auto-generate saat register. cth: SIS-2025-001',
    `qr_token`         VARCHAR(100)     NOT NULL COMMENT 'Isi QR profil. Auto-generate UUID saat register',
    `name`             VARCHAR(100)     NOT NULL,
    `type`             ENUM('siswa','guru','umum') NOT NULL,
    `nis_nip`          VARCHAR(30)      NULL     COMMENT 'NIS untuk siswa, NIP untuk guru',
    `phone`            VARCHAR(20)      NULL,
    `address`          TEXT             NULL,
    `photo`            VARCHAR(255)     NULL,
    `status`           ENUM('pending','aktif','nonaktif','suspended','ditolak') NOT NULL DEFAULT 'pending',
    `expired_at`       DATE             NULL     COMMENT 'Masa berlaku kartu. NULL = tidak ada batas',
    `verified_at`      TIMESTAMP        NULL     DEFAULT NULL COMMENT 'Waktu admin approve',
    `verified_by`      BIGINT UNSIGNED  NULL     COMMENT 'FK ke users (admin yang approve)',
    `rejection_reason` TEXT             NULL     COMMENT 'Alasan penolakan jika status = ditolak',
    `suspend_reason`   TEXT             NULL     COMMENT 'Alasan suspend jika status = suspended',
    `notes`            TEXT             NULL,
    `created_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `members_member_code_unique` (`member_code`),
    UNIQUE KEY `members_qr_token_unique`    (`qr_token`),
    UNIQUE KEY `members_user_id_unique`     (`user_id`) COMMENT '1 user hanya punya 1 data anggota',
    KEY `members_status_idx`  (`status`),
    KEY `members_type_idx`    (`type`),
    KEY `members_class_idx`   (`class_id`),
    CONSTRAINT `fk_members_user`        FOREIGN KEY (`user_id`)     REFERENCES `users`   (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_members_class`       FOREIGN KEY (`class_id`)    REFERENCES `classes` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_members_verified_by` FOREIGN KEY (`verified_by`) REFERENCES `users`   (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. CATEGORIES
--    Kategori / klasifikasi buku.
-- ============================================================
CREATE TABLE IF NOT EXISTS `categories` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(80)     NOT NULL,
    `code`        VARCHAR(10)     NOT NULL COMMENT 'cth: FIK, SCI, HIS, BIS',
    `description` TEXT            NULL,
    `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `categories_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. BOOKS
--    Master data judul buku. 1 judul = banyak eksemplar fisik.
--    total_copies & avg_rating di-update via trigger atau
--    Eloquent observer di Laravel.
-- ============================================================
CREATE TABLE IF NOT EXISTS `books` (
    `id`           BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `category_id`  BIGINT UNSIGNED   NOT NULL,
    `isbn`         VARCHAR(20)       NULL,
    `title`        VARCHAR(255)      NOT NULL,
    `author`       VARCHAR(200)      NOT NULL,
    `publisher`    VARCHAR(150)      NULL,
    `year`         YEAR              NULL,
    `edition`      VARCHAR(20)       NULL,
    `language`     VARCHAR(30)       NOT NULL DEFAULT 'Indonesia',
    `pages`        SMALLINT UNSIGNED NULL,
    `description`  TEXT              NULL,
    `cover_image`  VARCHAR(255)      NULL,
    `rack_number`  VARCHAR(20)       NULL     COMMENT 'Nomor rak lokasi fisik buku',
    `total_copies` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    `avg_rating`   DECIMAL(3,2)      NOT NULL DEFAULT 0.00,
    `total_loans`  INT UNSIGNED      NOT NULL DEFAULT 0 COMMENT 'Counter popularitas',
    `created_at`   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY  `books_isbn_unique`  (`isbn`),
    KEY         `books_category_idx` (`category_id`),
    FULLTEXT KEY `books_search_idx`  (`title`, `author`) COMMENT 'Full-text search pencarian buku',
    CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. BOOK_COPIES
--    Eksemplar fisik per buku. Setiap eksemplar punya barcode
--    unik yang ditempel di buku fisik.
--    Saat peminjaman: petugas scan barcode eksemplar ini.
--    Saat pengembalian: tidak perlu scan, cukup scan QR profil
--    anggota → sistem tampilkan daftar buku yang dipinjam.
-- ============================================================
CREATE TABLE IF NOT EXISTS `book_copies` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `book_id`    BIGINT UNSIGNED NOT NULL,
    `copy_code`  VARCHAR(30)     NOT NULL COMMENT 'cth: BK-001-E1 (buku ke-1, eksemplar ke-1)',
    `barcode`    VARCHAR(50)     NOT NULL COMMENT 'Barcode unik ditempel di buku fisik',
    `condition`  ENUM('baik','rusak_ringan','rusak_berat','hilang') NOT NULL DEFAULT 'baik',
    `status`     ENUM('tersedia','dipinjam','direservasi','tidak_aktif') NOT NULL DEFAULT 'tersedia',
    `notes`      TEXT            NULL,
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `book_copies_copy_code_unique` (`copy_code`),
    UNIQUE KEY `book_copies_barcode_unique`   (`barcode`),
    KEY         `book_copies_book_idx`        (`book_id`),
    KEY         `book_copies_status_idx`      (`status`),
    CONSTRAINT `fk_copies_book` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 7. LOANS
--    Header transaksi peminjaman.
--
--    Alur peminjaman:
--    1. Petugas scan QR profil anggota → validasi via sp_validate_member
--    2. Petugas scan barcode buku (maks 2 buku)
--    3. Pilih loan_type: 'pembaca' atau 'lomba'
--    4. Jika lomba → isi competition_end_date (wajib)
--    5. due_date dihitung otomatis via sp_calculate_due_date
--    6. Status default 'aktif'
--
--    extended_count: berapa kali sudah diperpanjang (maks dari settings)
--    extended_due_date: due_date baru setelah perpanjangan
-- ============================================================
CREATE TABLE IF NOT EXISTS `loans` (
    `id`                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `loan_code`            VARCHAR(30)     NOT NULL COMMENT 'cth: TRX-20250901-001',
    `member_id`            BIGINT UNSIGNED NOT NULL,
    `loan_date`            DATE            NOT NULL,
    `due_date`             DATE            NOT NULL COMMENT 'Dihitung otomatis via sp_calculate_due_date',
    `loan_type`            ENUM('pembaca','lomba') NOT NULL DEFAULT 'pembaca',
    `competition_end_date` DATE            NULL     COMMENT 'Wajib diisi jika loan_type = lomba',
    `extended_count`       TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Jumlah perpanjangan yang sudah dilakukan',
    `extended_due_date`    DATE            NULL     COMMENT 'Due date terbaru setelah perpanjangan',
    `status`               ENUM('aktif','diperpanjang','terlambat','selesai','hilang') NOT NULL DEFAULT 'aktif',
    `created_by`           BIGINT UNSIGNED NOT NULL COMMENT 'Petugas/admin yang input peminjaman',
    `notes`                TEXT            NULL,
    `created_at`           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `loans_loan_code_unique` (`loan_code`),
    KEY `loans_member_idx`     (`member_id`),
    KEY `loans_due_date_idx`   (`due_date`),
    KEY `loans_status_idx`     (`status`),
    KEY `loans_created_at_idx` (`created_at`),
    CONSTRAINT `fk_loans_member`     FOREIGN KEY (`member_id`)  REFERENCES `members` (`id`),
    CONSTRAINT `fk_loans_created_by` FOREIGN KEY (`created_by`) REFERENCES `users`   (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 8. LOAN_ITEMS
--    Detail tiap eksemplar buku dalam satu transaksi.
--    1 loans bisa punya 1-2 loan_items (sesuai max_pinjam).
--
--    Alur pengembalian:
--    1. Petugas scan QR profil anggota
--    2. Sistem tampilkan daftar loan_items yang belum kembali
--    3. Petugas pilih buku yang dikembalikan
--    4. Input condition_after
--    5. Sistem jalankan sp_process_return
-- ============================================================
CREATE TABLE IF NOT EXISTS `loan_items` (
    `id`              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `loan_id`         BIGINT UNSIGNED NOT NULL,
    `copy_id`         BIGINT UNSIGNED NOT NULL,
    `returned_at`     TIMESTAMP       NULL DEFAULT NULL COMMENT 'NULL = belum dikembalikan',
    `condition_after` ENUM('baik','rusak_ringan','rusak_berat','hilang') NULL COMMENT 'Kondisi buku saat kembali',
    `returned_by`     BIGINT UNSIGNED NULL COMMENT 'Petugas penerima pengembalian',
    `notes`           TEXT            NULL,
    `created_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `loan_items_loan_idx`      (`loan_id`),
    KEY `loan_items_copy_idx`      (`copy_id`),
    KEY `loan_items_returned_idx`  (`returned_at`),
    CONSTRAINT `fk_items_loan`        FOREIGN KEY (`loan_id`)     REFERENCES `loans`       (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_items_copy`        FOREIGN KEY (`copy_id`)     REFERENCES `book_copies` (`id`),
    CONSTRAINT `fk_items_returned_by` FOREIGN KEY (`returned_by`) REFERENCES `users`       (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 9. LOAN_EXTENSIONS
--    Riwayat setiap perpanjangan peminjaman.
--    Maks perpanjangan dikonfigurasi di tabel settings.
-- ============================================================
CREATE TABLE IF NOT EXISTS `loan_extensions` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `loan_id`      BIGINT UNSIGNED NOT NULL,
    `old_due_date` DATE            NOT NULL,
    `new_due_date` DATE            NOT NULL,
    `extended_by`  BIGINT UNSIGNED NOT NULL COMMENT 'Petugas/admin yang approve perpanjangan',
    `notes`        TEXT            NULL,
    `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `loan_extensions_loan_idx` (`loan_id`),
    CONSTRAINT `fk_extensions_loan`        FOREIGN KEY (`loan_id`)     REFERENCES `loans` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_extensions_extended_by` FOREIGN KEY (`extended_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 10. FINES
--     Denda per loan_item. Satu item bisa punya lebih dari
--     satu denda (contoh: terlambat + rusak sekaligus).
--     Nominal denda dibaca dari tabel settings saat insert.
-- ============================================================
CREATE TABLE IF NOT EXISTS `fines` (
    `id`           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `loan_item_id` BIGINT UNSIGNED  NOT NULL,
    `fine_type`    ENUM('keterlambatan','kerusakan','kehilangan') NOT NULL,
    `days_late`    SMALLINT UNSIGNED NULL COMMENT 'Hari terlambat (khusus tipe keterlambatan)',
    `amount`       INT UNSIGNED     NOT NULL COMMENT 'Nominal denda (Rp)',
    `status`       ENUM('belum_lunas','lunas','dibebaskan') NOT NULL DEFAULT 'belum_lunas',
    `paid_at`      TIMESTAMP        NULL DEFAULT NULL,
    `confirmed_by` BIGINT UNSIGNED  NULL COMMENT 'Petugas/admin yang konfirmasi lunas',
    `freed_by`     BIGINT UNSIGNED  NULL COMMENT 'Admin yang bebaskan denda',
    `freed_reason` TEXT             NULL COMMENT 'Alasan pembebasan denda',
    `notes`        TEXT             NULL,
    `created_at`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fines_loan_item_idx` (`loan_item_id`),
    KEY `fines_status_idx`    (`status`),
    KEY `fines_type_idx`      (`fine_type`),
    CONSTRAINT `fk_fines_loan_item`    FOREIGN KEY (`loan_item_id`) REFERENCES `loan_items` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_fines_confirmed_by` FOREIGN KEY (`confirmed_by`) REFERENCES `users`      (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_fines_freed_by`     FOREIGN KEY (`freed_by`)     REFERENCES `users`      (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 11. FINE_PAYMENTS
--     Riwayat pembayaran denda. Mendukung pembayaran cicil:
--     satu denda bisa dilunasi lewat beberapa pembayaran.
-- ============================================================
CREATE TABLE IF NOT EXISTS `fine_payments` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `fine_id`        BIGINT UNSIGNED NOT NULL,
    `amount_paid`    INT UNSIGNED    NOT NULL,
    `payment_date`   DATE            NOT NULL,
    `receipt_number` VARCHAR(50)     NULL COMMENT 'Nomor kwitansi pembayaran',
    `received_by`    BIGINT UNSIGNED NOT NULL COMMENT 'Petugas penerima pembayaran',
    `notes`          TEXT            NULL,
    `created_at`     TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fine_payments_fine_idx` (`fine_id`),
    CONSTRAINT `fk_payments_fine`        FOREIGN KEY (`fine_id`)     REFERENCES `fines` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_payments_received_by` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 12. RESERVATIONS
--     Antrean buku yang sedang dipinjam orang lain.
--     Dipicu saat: scan barcode buku → status 'dipinjam'
--     → sistem tawarkan opsi reservasi.
--     Notifikasi otomatis dikirim saat buku dikembalikan.
-- ============================================================
CREATE TABLE IF NOT EXISTS `reservations` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`   BIGINT UNSIGNED NOT NULL,
    `book_id`     BIGINT UNSIGNED NOT NULL,
    `queue_number` TINYINT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Urutan antrean untuk judul yang sama',
    `status`      ENUM('menunggu','tersedia','diambil','dibatalkan','kadaluarsa') NOT NULL DEFAULT 'menunggu',
    `notified_at` TIMESTAMP       NULL DEFAULT NULL COMMENT 'Waktu notifikasi buku tersedia dikirim',
    `expires_at`  TIMESTAMP       NULL DEFAULT NULL COMMENT 'Batas waktu ambil setelah notif',
    `notes`       TEXT            NULL,
    `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `reservations_member_idx` (`member_id`),
    KEY `reservations_book_idx`   (`book_id`),
    KEY `reservations_status_idx` (`status`),
    CONSTRAINT `fk_res_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
    CONSTRAINT `fk_res_book`   FOREIGN KEY (`book_id`)   REFERENCES `books`   (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 13. VISITS
--     Presensi kunjungan harian via scan QR profil anggota.
--     Satu anggota bisa dicatat lebih dari sekali per hari
--     jika kategori kunjungannya berbeda.
-- ============================================================
CREATE TABLE IF NOT EXISTS `visits` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`  BIGINT UNSIGNED NOT NULL,
    `visit_date` DATE            NOT NULL,
    `visit_time` TIME            NOT NULL,
    `category`   ENUM('membaca','pengunjung','peminjam') NOT NULL DEFAULT 'pengunjung',
    `scanned_by` BIGINT UNSIGNED NULL COMMENT 'Petugas yang melakukan scan',
    `notes`      VARCHAR(255)    NULL,
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `visits_member_idx`  (`member_id`),
    KEY `visits_date_idx`    (`visit_date`),
    KEY `visits_category_idx`(`category`),
    CONSTRAINT `fk_visits_member`     FOREIGN KEY (`member_id`)  REFERENCES `members` (`id`),
    CONSTRAINT `fk_visits_scanned_by` FOREIGN KEY (`scanned_by`) REFERENCES `users`   (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 14. BOOK_RATINGS
--     Rating & ulasan buku. Satu anggota hanya bisa rating
--     satu kali per judul buku.
--     avg_rating di tabel books di-update via trigger.
-- ============================================================
CREATE TABLE IF NOT EXISTS `book_ratings` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `book_id`    BIGINT UNSIGNED  NOT NULL,
    `member_id`  BIGINT UNSIGNED  NOT NULL,
    `rating`     TINYINT UNSIGNED NOT NULL COMMENT '1 sampai 5 bintang',
    `review`     TEXT             NULL,
    `created_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `book_ratings_book_member_unique` (`book_id`, `member_id`),
    CONSTRAINT `fk_ratings_book`   FOREIGN KEY (`book_id`)   REFERENCES `books`   (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ratings_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 15. SETTINGS
--     Konfigurasi sistem yang bisa diedit admin kapan saja
--     tanpa menyentuh kode program.
-- ============================================================
CREATE TABLE IF NOT EXISTS `settings` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key`         VARCHAR(80)     NOT NULL,
    `value`       TEXT            NOT NULL,
    `type`        ENUM('string','integer','boolean','json') NOT NULL DEFAULT 'string',
    `group`       VARCHAR(40)     NOT NULL DEFAULT 'general',
    `description` VARCHAR(255)    NULL,
    `updated_by`  BIGINT UNSIGNED NULL,
    `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `settings_key_unique` (`key`),
    CONSTRAINT `fk_settings_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 16. NOTIFICATIONS
--     Notifikasi in-app dan push notification ke mobile.
--     Dikirim via Firebase Cloud Messaging (FCM).
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`  BIGINT UNSIGNED NOT NULL,
    `type`       ENUM(
                     'akun_diaktifkan',
                     'akun_ditolak',
                     'reminder_pengembalian',
                     'terlambat_pengembalian',
                     'denda_baru',
                     'denda_lunas',
                     'reservasi_tersedia',
                     'reservasi_kadaluarsa',
                     'perpanjangan_berhasil',
                     'info'
                 ) NOT NULL,
    `title`      VARCHAR(150)    NOT NULL,
    `body`       TEXT            NOT NULL,
    `data`       JSON            NULL COMMENT 'Payload: loan_id, fine_id, book_id, dll',
    `is_read`    TINYINT(1)      NOT NULL DEFAULT 0,
    `sent_at`    TIMESTAMP       NULL DEFAULT NULL,
    `read_at`    TIMESTAMP       NULL DEFAULT NULL,
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `notifications_member_idx` (`member_id`),
    KEY `notifications_type_idx`   (`type`),
    KEY `notifications_read_idx`   (`is_read`),
    CONSTRAINT `fk_notif_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 17. FCM_TOKENS
--     Token Firebase Cloud Messaging per perangkat mobile.
--     Satu user bisa punya lebih dari satu token (multi-device).
-- ============================================================
CREATE TABLE IF NOT EXISTS `fcm_tokens` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `token`      TEXT            NOT NULL,
    `device`     VARCHAR(100)    NULL COMMENT 'Info perangkat, cth: Android 13 / Samsung A52',
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `fcm_tokens_user_idx` (`user_id`),
    CONSTRAINT `fk_fcm_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 18. ACTIVITY_LOGS
--     Audit trail semua aksi admin & petugas.
--     Dicatat oleh Laravel Observer atau Service class.
-- ============================================================
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NULL,
    `action`     VARCHAR(80)     NOT NULL COMMENT 'cth: approve_member, create_loan, process_return',
    `model_type` VARCHAR(80)     NULL COMMENT 'cth: App\\Models\\Member',
    `model_id`   BIGINT UNSIGNED NULL,
    `old_values` JSON            NULL,
    `new_values` JSON            NULL,
    `ip_address` VARCHAR(45)     NULL,
    `user_agent` VARCHAR(255)    NULL,
    `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `activity_logs_user_idx`  (`user_id`),
    KEY `activity_logs_action_idx`(`action`),
    KEY `activity_logs_model_idx` (`model_type`, `model_id`),
    CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Buku digital yang pernah diakses / disimpan anggota
CREATE TABLE ebook_bookmarks (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id   BIGINT UNSIGNED NOT NULL,
    source      ENUM('openlibrary','googlebooks','gutenberg') NOT NULL,
    external_id VARCHAR(100)    NOT NULL COMMENT 'ID buku di sumber eksternal',
    title       VARCHAR(255)    NOT NULL,
    author      VARCHAR(200)    NULL,
    cover_url   VARCHAR(500)    NULL,
    read_url    VARCHAR(500)    NULL COMMENT 'URL baca online',
    last_read   TIMESTAMP       NULL,
    is_favorite TINYINT(1)      NOT NULL DEFAULT 0,
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY ebook_bookmarks_member_source_book (member_id, source, external_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Riwayat bacaan per buku (halaman terakhir dibaca)
CREATE TABLE ebook_reading_progress (
    id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id     BIGINT UNSIGNED NOT NULL,
    source        ENUM('openlibrary','googlebooks','gutenberg') NOT NULL,
    external_id   VARCHAR(100)    NOT NULL,
    progress_data JSON            NULL COMMENT 'page, percentage, last position',
    updated_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY ebook_progress_member_book (member_id, source, external_id),
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);

-- Cache metadata buku dari API luar (supaya tidak fetch berulang)
CREATE TABLE ebook_cache (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    source      ENUM('openlibrary','googlebooks','gutenberg') NOT NULL,
    external_id VARCHAR(100)    NOT NULL,
    data        JSON            NOT NULL COMMENT 'Raw data dari API',
    cached_at   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expires_at  TIMESTAMP       NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY ebook_cache_source_book (source, external_id)
);


SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================
-- SEED: Settings default sistem
-- ============================================================
INSERT INTO `settings` (`key`, `value`, `type`, `group`, `description`) VALUES
-- Denda
('denda_per_hari',          '1000',  'integer', 'denda',      'Nominal denda keterlambatan per hari (Rp)'),
('denda_rusak_ringan',      '10000', 'integer', 'denda',      'Denda buku rusak ringan (Rp)'),
('denda_rusak_berat',       '30000', 'integer', 'denda',      'Denda buku rusak berat (Rp)'),
('denda_hilang',            '50000', 'integer', 'denda',      'Denda buku hilang (Rp)'),
-- Peminjaman
('max_pinjam',              '2',     'integer', 'peminjaman', 'Maksimum buku dipinjam per anggota'),
('lama_pinjam_hari',        '3',     'integer', 'peminjaman', 'Lama peminjaman default dalam hari kerja'),
('max_perpanjangan',        '1',     'integer', 'peminjaman', 'Maksimum perpanjangan per transaksi'),
('aktif_rules_rabu_kamis',  'true',  'boolean', 'peminjaman', 'Aktifkan rules: pinjam Rabu/Kamis → kembali Senin'),
-- Reservasi
('batas_ambil_reservasi',   '2',     'integer', 'reservasi',  'Hari batas ambil buku reservasi setelah notifikasi'),
-- Umum
('nama_perpustakaan',       'Perpustakaan SMA', 'string', 'general', 'Nama perpustakaan'),
('nama_sekolah',            'SMA Negeri 1',     'string', 'general', 'Nama sekolah'),
('alamat_sekolah',          '-',                'string', 'general', 'Alamat lengkap sekolah'),
('logo_sekolah',            '',                 'string', 'general', 'Path logo sekolah untuk header laporan');


-- ============================================================
-- TRIGGER: update avg_rating di tabel books
--          setelah insert atau update rating
-- ============================================================
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS `trg_after_rating_insert`
AFTER INSERT ON `book_ratings`
FOR EACH ROW
BEGIN
    UPDATE books
    SET avg_rating = (
        SELECT ROUND(AVG(rating), 2)
        FROM book_ratings
        WHERE book_id = NEW.book_id
    )
    WHERE id = NEW.book_id;
END$$

CREATE TRIGGER IF NOT EXISTS `trg_after_rating_update`
AFTER UPDATE ON `book_ratings`
FOR EACH ROW
BEGIN
    UPDATE books
    SET avg_rating = (
        SELECT ROUND(AVG(rating), 2)
        FROM book_ratings
        WHERE book_id = NEW.book_id
    )
    WHERE id = NEW.book_id;
END$$

CREATE TRIGGER IF NOT EXISTS `trg_after_rating_delete`
AFTER DELETE ON `book_ratings`
FOR EACH ROW
BEGIN
    UPDATE books
    SET avg_rating = IFNULL((
        SELECT ROUND(AVG(rating), 2)
        FROM book_ratings
        WHERE book_id = OLD.book_id
    ), 0.00)
    WHERE id = OLD.book_id;
END$$

-- ============================================================
-- TRIGGER: update total_copies di tabel books
--          setelah insert atau delete book_copies
-- ============================================================

CREATE TRIGGER IF NOT EXISTS `trg_after_copy_insert`
AFTER INSERT ON `book_copies`
FOR EACH ROW
BEGIN
    UPDATE books
    SET total_copies = (
        SELECT COUNT(*) FROM book_copies
        WHERE book_id = NEW.book_id
          AND condition != 'hilang'
    )
    WHERE id = NEW.book_id;
END$$

CREATE TRIGGER IF NOT EXISTS `trg_after_copy_update`
AFTER UPDATE ON `book_copies`
FOR EACH ROW
BEGIN
    UPDATE books
    SET total_copies = (
        SELECT COUNT(*) FROM book_copies
        WHERE book_id = NEW.book_id
          AND condition != 'hilang'
    )
    WHERE id = NEW.book_id;
END$$

DELIMITER ;


-- ============================================================
-- STORED PROCEDURE: sp_calculate_due_date
--   Input : tanggal pinjam, loan_type, competition_end_date
--   Output: due_date sesuai rules
--
--   Rules:
--   - Jika lomba → due_date = competition_end_date
--   - Jika pembaca → tambah 3 hari kerja (skip Sabtu-Minggu)
--   - Jika due_date jatuh Rabu → geser ke Senin (+5 hari)
--   - Jika due_date jatuh Kamis → geser ke Senin (+4 hari)
--   - Rules Rabu-Kamis hanya aktif jika setting = true
-- ============================================================
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS `sp_calculate_due_date` (
    IN  p_loan_date           DATE,
    IN  p_loan_type           VARCHAR(20),
    IN  p_competition_end_date DATE,
    OUT p_due_date            DATE
)
BEGIN
    DECLARE v_due       DATE;
    DECLARE v_dow       TINYINT;
    DECLARE v_count     INT DEFAULT 0;
    DECLARE v_lama      INT DEFAULT 3;
    DECLARE v_rules_aktif VARCHAR(10) DEFAULT 'true';

    -- Jika lomba, gunakan tanggal akhir lomba langsung
    IF p_loan_type = 'lomba' AND p_competition_end_date IS NOT NULL THEN
        SET p_due_date = p_competition_end_date;
        LEAVE sp_calculate_due_date;  -- exit early
    END IF;

    -- Ambil konfigurasi lama pinjam dari settings
    SELECT value INTO v_lama
    FROM settings WHERE `key` = 'lama_pinjam_hari' LIMIT 1;

    SELECT value INTO v_rules_aktif
    FROM settings WHERE `key` = 'aktif_rules_rabu_kamis' LIMIT 1;

    SET v_due = p_loan_date;

    -- Hitung hari kerja (skip Sabtu=7, Minggu=1 dalam DAYOFWEEK MySQL)
    WHILE v_count < v_lama DO
        SET v_due = DATE_ADD(v_due, INTERVAL 1 DAY);
        SET v_dow = DAYOFWEEK(v_due);
        IF v_dow NOT IN (1, 7) THEN
            SET v_count = v_count + 1;
        END IF;
    END WHILE;

    -- Terapkan rules Rabu-Kamis jika aktif
    IF v_rules_aktif = 'true' THEN
        SET v_dow = DAYOFWEEK(v_due);
        IF v_dow = 4 THEN
            -- Rabu → Senin berikutnya (+5)
            SET v_due = DATE_ADD(v_due, INTERVAL 5 DAY);
        ELSEIF v_dow = 5 THEN
            -- Kamis → Senin berikutnya (+4)
            SET v_due = DATE_ADD(v_due, INTERVAL 4 DAY);
        END IF;
    END IF;

    SET p_due_date = v_due;
END$$

DELIMITER ;


-- ============================================================
-- STORED PROCEDURE: sp_validate_member
--   Dipanggil saat petugas scan QR profil anggota.
--   Mengecek semua kondisi sebelum boleh meminjam buku.
--
--   Output result_code:
--   'OK'               → lolos, lanjut scan buku
--   'PENDING'          → akun belum diverifikasi admin
--   'NONAKTIF'         → akun dinonaktifkan
--   'SUSPENDED'        → akun di-suspend
--   'DITOLAK'          → registrasi ditolak admin
--   'KUOTA_PENUH'      → sudah pinjam 2 buku (atau sesuai settings)
--   'ADA_DENDA'        → punya denda belum lunas
--   'QR_TIDAK_DITEMUKAN' → qr_token tidak ada di database
-- ============================================================
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS `sp_validate_member` (
    IN  p_qr_token    VARCHAR(100),
    OUT p_result_code VARCHAR(30),
    OUT p_member_id   BIGINT,
    OUT p_message     VARCHAR(255)
)
BEGIN
    DECLARE v_status      VARCHAR(20);
    DECLARE v_type        VARCHAR(10);
    DECLARE v_name        VARCHAR(100);
    DECLARE v_active_loans INT DEFAULT 0;
    DECLARE v_unpaid_fines INT DEFAULT 0;
    DECLARE v_max_pinjam   INT DEFAULT 2;

    -- Cek apakah QR token terdaftar
    SELECT id, status, type, name
    INTO p_member_id, v_status, v_type, v_name
    FROM members WHERE qr_token = p_qr_token LIMIT 1;

    IF p_member_id IS NULL THEN
        SET p_result_code = 'QR_TIDAK_DITEMUKAN';
        SET p_message     = 'QR code tidak dikenali sistem.';
        LEAVE sp_validate_member;
    END IF;

    -- Cek status anggota
    IF v_status = 'pending' THEN
        SET p_result_code = 'PENDING';
        SET p_message = CONCAT('Akun ', v_name, ' belum diverifikasi admin.');
        LEAVE sp_validate_member;
    END IF;

    IF v_status = 'nonaktif' THEN
        SET p_result_code = 'NONAKTIF';
        SET p_message = CONCAT('Akun ', v_name, ' dinonaktifkan. Hubungi admin.');
        LEAVE sp_validate_member;
    END IF;

    IF v_status = 'suspended' THEN
        SET p_result_code = 'SUSPENDED';
        SET p_message = CONCAT('Akun ', v_name, ' di-suspend. Hubungi admin.');
        LEAVE sp_validate_member;
    END IF;

    IF v_status = 'ditolak' THEN
        SET p_result_code = 'DITOLAK';
        SET p_message = CONCAT('Pendaftaran ', v_name, ' ditolak admin.');
        LEAVE sp_validate_member;
    END IF;

    -- Cek denda belum lunas
    SELECT COUNT(*) INTO v_unpaid_fines
    FROM fines f
    JOIN loan_items li ON f.loan_item_id = li.id
    JOIN loans l ON li.loan_id = l.id
    WHERE l.member_id = p_member_id
      AND f.status = 'belum_lunas';

    IF v_unpaid_fines > 0 THEN
        SET p_result_code = 'ADA_DENDA';
        SET p_message = CONCAT(v_name, ' memiliki ', v_unpaid_fines, ' denda belum lunas.');
        LEAVE sp_validate_member;
    END IF;

    -- Ambil max pinjam dari settings
    SELECT CAST(value AS UNSIGNED) INTO v_max_pinjam
    FROM settings WHERE `key` = 'max_pinjam' LIMIT 1;

    -- Cek kuota pinjam
    SELECT COUNT(*) INTO v_active_loans
    FROM loan_items li
    JOIN loans l ON li.loan_id = l.id
    WHERE l.member_id = p_member_id
      AND l.status IN ('aktif', 'diperpanjang', 'terlambat')
      AND li.returned_at IS NULL;

    IF v_active_loans >= v_max_pinjam THEN
        SET p_result_code = 'KUOTA_PENUH';
        SET p_message = CONCAT(v_name, ' sudah meminjam ', v_active_loans, ' buku. Kembalikan dulu sebelum pinjam lagi.');
        LEAVE sp_validate_member;
    END IF;

    -- Semua lolos
    SET p_result_code = 'OK';
    SET p_message = CONCAT('Anggota ', v_name, ' valid. Silakan scan barcode buku.');
END$$

DELIMITER ;


-- ============================================================
-- STORED PROCEDURE: sp_process_return
--   Proses pengembalian satu buku (satu loan_item).
--   Nominal denda dibaca dari tabel settings secara dinamis.
--
--   Yang dilakukan:
--   1. Tandai loan_item sebagai kembali
--   2. Update kondisi & status book_copies
--   3. Hitung + insert denda keterlambatan (jika ada)
--   4. Insert denda kerusakan/kehilangan (jika ada)
--   5. Update status loans jika semua item sudah kembali
--   6. Cek antrean reservasi → notifikasi anggota berikutnya
-- ============================================================
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS `sp_process_return` (
    IN  p_item_id     BIGINT,
    IN  p_condition   VARCHAR(20),
    IN  p_returned_by BIGINT,
    OUT p_fine_total  INT
)
BEGIN
    DECLARE v_copy_id        BIGINT;
    DECLARE v_loan_id        BIGINT;
    DECLARE v_book_id        BIGINT;
    DECLARE v_member_id      BIGINT;
    DECLARE v_due_date       DATE;
    DECLARE v_days_late      INT DEFAULT 0;
    DECLARE v_denda_hari     INT DEFAULT 1000;
    DECLARE v_denda_ringan   INT DEFAULT 10000;
    DECLARE v_denda_berat    INT DEFAULT 30000;
    DECLARE v_denda_hilang   INT DEFAULT 50000;
    DECLARE v_fine_amt       INT DEFAULT 0;

    SET p_fine_total = 0;

    -- Ambil data item, loan, dan member
    SELECT li.copy_id, li.loan_id, l.due_date, l.member_id
    INTO v_copy_id, v_loan_id, v_due_date, v_member_id
    FROM loan_items li
    JOIN loans l ON li.loan_id = l.id
    WHERE li.id = p_item_id;

    -- Ambil book_id dari copy
    SELECT book_id INTO v_book_id
    FROM book_copies WHERE id = v_copy_id;

    -- Baca nominal denda dari settings
    SELECT CAST(value AS UNSIGNED) INTO v_denda_hari    FROM settings WHERE `key` = 'denda_per_hari'     LIMIT 1;
    SELECT CAST(value AS UNSIGNED) INTO v_denda_ringan  FROM settings WHERE `key` = 'denda_rusak_ringan' LIMIT 1;
    SELECT CAST(value AS UNSIGNED) INTO v_denda_berat   FROM settings WHERE `key` = 'denda_rusak_berat'  LIMIT 1;
    SELECT CAST(value AS UNSIGNED) INTO v_denda_hilang  FROM settings WHERE `key` = 'denda_hilang'       LIMIT 1;

    -- 1. Tandai item kembali
    UPDATE loan_items
    SET returned_at     = NOW(),
        condition_after = p_condition,
        returned_by     = p_returned_by
    WHERE id = p_item_id;

    -- 2. Update status dan kondisi eksemplar
    IF p_condition = 'hilang' THEN
        UPDATE book_copies
        SET status = 'tidak_aktif', `condition` = 'hilang'
        WHERE id = v_copy_id;
    ELSE
        UPDATE book_copies
        SET status = 'tersedia', `condition` = p_condition
        WHERE id = v_copy_id;
    END IF;

    -- 3. Hitung denda keterlambatan
    SET v_days_late = DATEDIFF(CURDATE(), v_due_date);
    IF v_days_late > 0 THEN
        SET v_fine_amt = v_days_late * v_denda_hari;
        INSERT INTO fines (loan_item_id, fine_type, days_late, amount, status)
        VALUES (p_item_id, 'keterlambatan', v_days_late, v_fine_amt, 'belum_lunas');
        SET p_fine_total = p_fine_total + v_fine_amt;
    END IF;

    -- 4. Denda kerusakan atau kehilangan
    IF p_condition = 'rusak_ringan' THEN
        INSERT INTO fines (loan_item_id, fine_type, amount, status)
        VALUES (p_item_id, 'kerusakan', v_denda_ringan, 'belum_lunas');
        SET p_fine_total = p_fine_total + v_denda_ringan;

    ELSEIF p_condition = 'rusak_berat' THEN
        INSERT INTO fines (loan_item_id, fine_type, amount, status)
        VALUES (p_item_id, 'kerusakan', v_denda_berat, 'belum_lunas');
        SET p_fine_total = p_fine_total + v_denda_berat;

    ELSEIF p_condition = 'hilang' THEN
        INSERT INTO fines (loan_item_id, fine_type, amount, status)
        VALUES (p_item_id, 'kehilangan', v_denda_hilang, 'belum_lunas');
        SET p_fine_total = p_fine_total + v_denda_hilang;
    END IF;

    -- 5. Update total_loans buku
    UPDATE books SET total_loans = total_loans + 1 WHERE id = v_book_id;

    -- 6. Cek apakah semua item di loan sudah kembali
    IF NOT EXISTS (
        SELECT 1 FROM loan_items
        WHERE loan_id = v_loan_id AND returned_at IS NULL
    ) THEN
        UPDATE loans SET status = 'selesai' WHERE id = v_loan_id;
    END IF;

    -- 7. Cek reservasi antrean untuk buku ini
    --    Jika buku tersedia kembali dan ada yang antre, set status → 'tersedia'
    IF p_condition != 'hilang' THEN
        UPDATE reservations
        SET status       = 'tersedia',
            notified_at  = NOW(),
            expires_at   = DATE_ADD(NOW(), INTERVAL (
                SELECT CAST(value AS UNSIGNED) FROM settings WHERE `key` = 'batas_ambil_reservasi' LIMIT 1
            ) DAY)
        WHERE book_id = v_book_id
          AND status  = 'menunggu'
        ORDER BY created_at ASC
        LIMIT 1;
    END IF;
END$$

DELIMITER ;


-- ============================================================
-- VIEWS
-- ============================================================

-- View: data lengkap anggota saat scan QR profil
CREATE OR REPLACE VIEW `v_member_scan_info` AS
SELECT
    m.id              AS member_id,
    m.member_code,
    m.qr_token,
    m.name            AS member_name,
    m.type            AS member_type,
    m.status,
    m.phone,
    m.expired_at,
    c.name            AS class_name,
    c.grade,
    -- Jumlah buku yang sedang dipinjam
    COUNT(DISTINCT CASE WHEN l.status IN ('aktif','diperpanjang','terlambat') AND li.returned_at IS NULL THEN li.id END) AS active_loans_count,
    -- Total denda belum lunas
    IFNULL(SUM(CASE WHEN f.status = 'belum_lunas' THEN f.amount ELSE 0 END), 0) AS unpaid_fines_total
FROM members m
LEFT JOIN classes    c  ON m.class_id    = c.id
LEFT JOIN loans      l  ON l.member_id   = m.id
LEFT JOIN loan_items li ON li.loan_id    = l.id
LEFT JOIN fines      f  ON f.loan_item_id = li.id
GROUP BY m.id;


-- View: peminjaman aktif lengkap untuk halaman petugas
CREATE OR REPLACE VIEW `v_active_loans` AS
SELECT
    l.id                   AS loan_id,
    l.loan_code,
    l.loan_date,
    l.due_date,
    IFNULL(l.extended_due_date, l.due_date) AS effective_due_date,
    l.loan_type,
    l.competition_end_date,
    l.extended_count,
    l.status               AS loan_status,
    DATEDIFF(CURDATE(), IFNULL(l.extended_due_date, l.due_date)) AS days_overdue,
    m.id                   AS member_id,
    m.member_code,
    m.name                 AS member_name,
    m.type                 AS member_type,
    m.qr_token,
    cl.name                AS class_name,
    li.id                  AS item_id,
    li.returned_at,
    bc.barcode             AS copy_barcode,
    bc.copy_code,
    bc.condition           AS copy_condition,
    b.id                   AS book_id,
    b.title                AS book_title,
    b.author               AS book_author,
    b.cover_image,
    b.rack_number
FROM loans l
JOIN members     m   ON l.member_id   = m.id
JOIN loan_items  li  ON li.loan_id    = l.id
JOIN book_copies bc  ON li.copy_id    = bc.id
JOIN books       b   ON bc.book_id    = b.id
LEFT JOIN classes cl ON m.class_id    = cl.id
WHERE l.status IN ('aktif','diperpanjang','terlambat')
  AND li.returned_at IS NULL;


-- View: peminjaman yang jatuh tempo BESOK (untuk cron notifikasi H-1)
CREATE OR REPLACE VIEW `v_due_tomorrow` AS
SELECT
    loan_id,
    loan_code,
    effective_due_date AS due_date,
    member_id,
    member_code,
    member_name,
    member_type,
    class_name,
    book_title,
    book_author
FROM v_active_loans
WHERE effective_due_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY);


-- View: peminjaman yang sudah terlambat
CREATE OR REPLACE VIEW `v_overdue_loans` AS
SELECT
    loan_id,
    loan_code,
    effective_due_date AS due_date,
    days_overdue,
    member_id,
    member_code,
    member_name,
    member_type,
    class_name,
    copy_barcode,
    book_title,
    book_author
FROM v_active_loans
WHERE effective_due_date < CURDATE()
ORDER BY days_overdue DESC;


-- View: buku terpopuler
CREATE OR REPLACE VIEW `v_popular_books` AS
SELECT
    b.id,
    b.title,
    b.author,
    b.cover_image,
    b.rack_number,
    cat.name              AS category_name,
    b.total_copies,
    b.avg_rating,
    b.total_loans,
    COUNT(CASE WHEN bc.status = 'tersedia' AND bc.condition != 'hilang' THEN 1 END) AS available_copies
FROM books b
JOIN categories  cat ON b.category_id = cat.id
LEFT JOIN book_copies bc ON bc.book_id = b.id
GROUP BY b.id
ORDER BY b.total_loans DESC, b.avg_rating DESC;


-- View: rekap denda belum lunas per anggota (untuk cek blokir pinjam)
CREATE OR REPLACE VIEW `v_member_unpaid_fines` AS
SELECT
    m.id                                                              AS member_id,
    m.name                                                            AS member_name,
    m.member_code,
    m.type                                                            AS member_type,
    COUNT(f.id)                                                       AS total_fines,
    SUM(f.amount)                                                     AS total_amount,
    SUM(CASE WHEN f.status = 'belum_lunas' THEN f.amount ELSE 0 END) AS unpaid_amount
FROM members m
JOIN loans      l  ON l.member_id    = m.id
JOIN loan_items li ON li.loan_id     = l.id
JOIN fines      f  ON f.loan_item_id = li.id
WHERE f.status = 'belum_lunas'
GROUP BY m.id;


-- View: daftar anggota pending (untuk notif badge di dashboard admin)
CREATE OR REPLACE VIEW `v_pending_members` AS
SELECT
    m.id,
    m.member_code,
    m.name,
    m.type,
    m.phone,
    m.nis_nip,
    cl.name  AS class_name,
    u.email,
    m.created_at AS registered_at
FROM members m
JOIN users      u  ON m.user_id  = u.id
LEFT JOIN classes cl ON m.class_id = cl.id
WHERE m.status = 'pending'
ORDER BY m.created_at ASC;


-- View: laporan kunjungan harian (untuk dashboard & laporan)
CREATE OR REPLACE VIEW `v_visit_summary` AS
SELECT
    v.visit_date,
    COUNT(*)                                                                  AS total_visits,
    COUNT(CASE WHEN v.category = 'membaca'    THEN 1 END)                    AS membaca,
    COUNT(CASE WHEN v.category = 'pengunjung' THEN 1 END)                    AS pengunjung,
    COUNT(CASE WHEN v.category = 'peminjam'   THEN 1 END)                    AS peminjam,
    COUNT(CASE WHEN m.type = 'siswa'          THEN 1 END)                    AS siswa,
    COUNT(CASE WHEN m.type = 'guru'           THEN 1 END)                    AS guru,
    COUNT(CASE WHEN m.type = 'umum'           THEN 1 END)                    AS umum
FROM visits v
JOIN members m ON v.member_id = m.id
GROUP BY v.visit_date
ORDER BY v.visit_date DESC;


-- ============================================================
-- E-BOOK — Integrasi buku digital dari sumber eksternal
-- Sumber yang didukung: Open Library, Google Books, Gutenberg
-- ============================================================

-- 19. EBOOK_BOOKMARKS
--     Buku digital yang pernah diakses / disimpan anggota.
--     Setiap anggota bisa bookmark buku dari sumber eksternal
--     tanpa perlu menyimpan file-nya di server.
CREATE TABLE IF NOT EXISTS `ebook_bookmarks` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`   BIGINT UNSIGNED NOT NULL,
    `source`      ENUM('openlibrary','googlebooks','gutenberg') NOT NULL,
    `external_id` VARCHAR(100)    NOT NULL COMMENT 'ID buku di sumber eksternal',
    `title`       VARCHAR(255)    NOT NULL,
    `author`      VARCHAR(200)    NULL,
    `cover_url`   VARCHAR(500)    NULL,
    `read_url`    VARCHAR(500)    NULL COMMENT 'URL baca online',
    `last_read`   TIMESTAMP       NULL DEFAULT NULL,
    `is_favorite` TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ebook_bookmarks_member_source_book` (`member_id`, `source`, `external_id`),
    CONSTRAINT `fk_ebookmarks_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 20. EBOOK_READING_PROGRESS
--     Riwayat bacaan per buku (halaman / posisi terakhir dibaca).
--     Data progres disimpan sebagai JSON agar fleksibel untuk
--     berbagai format (page number, percentage, scroll position, dll).
CREATE TABLE IF NOT EXISTS `ebook_reading_progress` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`     BIGINT UNSIGNED NOT NULL,
    `source`        ENUM('openlibrary','googlebooks','gutenberg') NOT NULL,
    `external_id`   VARCHAR(100)    NOT NULL,
    `progress_data` JSON            NULL COMMENT 'page, percentage, last position',
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ebook_progress_member_book` (`member_id`, `source`, `external_id`),
    CONSTRAINT `fk_eprogress_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 21. EBOOK_CACHE
--     Cache metadata buku dari API luar supaya tidak fetch berulang.
--     expires_at diisi saat insert; laravel scheduler bisa
--     membersihkan record yang sudah kadaluarsa secara periodik.
CREATE TABLE IF NOT EXISTS `ebook_cache` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `source`      ENUM('openlibrary','googlebooks','gutenberg') NOT NULL,
    `external_id` VARCHAR(100)    NOT NULL,
    `data`        JSON            NOT NULL COMMENT 'Raw data dari API',
    `cached_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at`  TIMESTAMP       NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `ebook_cache_source_book` (`source`, `external_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
