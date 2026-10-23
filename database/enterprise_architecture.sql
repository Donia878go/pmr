-- ========================================================
-- Enterprise Super-Admin Architecture Migration
-- Politeknik Mulia Rokan
-- ========================================================
USE `pmb_mulia_rokan`;

-- 1. RBAC (Role-Based Access Control)
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) NOT NULL UNIQUE,
  `nama_role` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `roles` (`id`, `slug`, `nama_role`, `deskripsi`, `is_system`) VALUES
(1, 'superadmin', 'Super Administrator', 'Akses penuh tanpa batasan ke seluruh sistem, konfigurasi server, dan audit log.', 1),
(2, 'admin_pmb', 'Panitia PMB', 'Hak akses manajemen pendaftaran, verifikasi berkas, dan keputusan kelulusan.', 1),
(3, 'jurnalis', 'Humas & Jurnalis Kampus', 'Hak akses modul publikasi, berita, pengumuman, dan galeri media.', 0),
(4, 'keuangan', 'Bagian Keuangan & UKT', 'Hak akses verifikasi pembayaran pendaftaran, tarif jalur, dan skema beasiswa.', 0);

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `nama_permission` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_module_action` (`module`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `permissions` (`module`, `action`, `nama_permission`, `deskripsi`) VALUES
('pmb', 'read', 'Lihat Data Pendaftar', 'Melihat daftar calon mahasiswa baru'),
('pmb', 'create', 'Daftarkan Calon Peserta', 'Menambah pendaftar manual offline'),
('pmb', 'verify', 'Verifikasi Dokumen PMB', 'Memvalidasi kelengkapan berkas fisik/digital'),
('pmb', 'approve', 'Keputusan Kelulusan PMB', 'Menetapkan status lulus / tidak lulus'),
('pmb', 'export', 'Ekspor Data PMB', 'Mengunduh rekap pendaftar ke Excel/CSV'),
('cms', 'sections', 'Kelola Tata Letak Homepage', 'Mengatur urutan dan on/off seksi beranda'),
('cms', 'brand', 'Kelola Identitas & Logo', 'Mengubah logo, warna, dan foto background'),
('cms', 'menus', 'Kelola Struktur Menu Navigasi', 'Mengatur menu bar dan dropdown bertingkat'),
('cms', 'forms', 'Kelola Dynamic Form Builder', 'Membuat formulir kustom dan melihat respon'),
('cms', 'multimedia', 'Kelola Video & Audio', 'Mengatur galeri video YouTube dan mars audio'),
('publish', 'create', 'Tulis Berita & Pengumuman', 'Membuat draft artikel berita baru'),
('publish', 'publish', 'Publikasikan Konten', 'Menyetujui dan menayangkan artikel berita'),
('academic', 'manage', 'Kelola Master Data Akademik', 'Mengatur prodi, fakultas, dan direktori dosen'),
('system', 'audit', 'Akses Audit Log', 'Memantau rekaman aktivitas seluruh pengguna'),
('system', 'integrations', 'Kelola SMTP & API Scripts', 'Mengatur email server, tracker code, dan keys'),
('system', 'users', 'Manajemen Pengguna & RBAC', 'Membuat user, reset password, suspend, dan impersonate');

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assign permissions
-- Superadmin (semua permission)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM `permissions`;

-- Admin PMB (Role ID 2)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 2, id FROM `permissions` WHERE `module` = 'pmb' OR `module` = 'academic';

-- Jurnalis (Role ID 3)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 3, id FROM `permissions` WHERE `module` = 'publish' OR `action` IN ('multimedia', 'sections');

-- Keuangan (Role ID 4)
INSERT INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 4, id FROM `permissions` WHERE `action` IN ('read', 'export');

-- Tambah kolom status, role_id, email, last_login ke admin_users
ALTER TABLE `admin_users`
ADD COLUMN IF NOT EXISTS `role_id` int(11) DEFAULT 2,
ADD COLUMN IF NOT EXISTS `email` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `status` enum('aktif','suspended') NOT NULL DEFAULT 'aktif',
ADD COLUMN IF NOT EXISTS `last_login` datetime DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `last_ip` varchar(50) DEFAULT NULL;

UPDATE `admin_users` SET `role_id` = 1 WHERE `role` = 'superadmin';
UPDATE `admin_users` SET `role_id` = 2 WHERE `role` = 'panitia';

-- 2. Dynamic Section Manager (Frontend Controller)
DROP TABLE IF EXISTS `homepage_sections`;
CREATE TABLE `homepage_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section_key` varchar(50) NOT NULL UNIQUE,
  `nama_seksi` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `container_style` varchar(50) DEFAULT 'default',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `homepage_sections` (`section_key`, `nama_seksi`, `deskripsi`, `urutan`, `is_active`) VALUES
('hero', 'Hero Banner & Visual Kampus', 'Headline utama, badge pembukaan PMB, tombol aksi, dan emblem logo', 1, 1),
('fitur', 'Keunggulan & Terobosan Kampus', 'Kartu fitur strategis, sertifikasi BNSP, dan teaching factory', 2, 1),
('video', 'Galeri Video Profil & YouTube', 'Pemutar video profil kampus, tur gedung, dan liputan magang', 3, 1),
('sambutan', 'Sambutan Direktur Kampus', 'Pidato dan foto resmi pimpinan Politeknik Mulia Rokan', 4, 1),
('visi_misi', 'Visi & Misi Perguruan Tinggi', 'Landasan filosofis, visi 2035, dan 4 butir misi institusi', 5, 1),
('jalur', 'Jalur Masuk PMB TA 2026/2027', 'Pilihan jalur pendaftaran (Prestasi, Reguler, Industri) dan biaya', 6, 1),
('prodi', 'Program Studi Sarjana Terapan (D4)', 'Katalog 3 prodi vokasi unggulan, akreditasi, dan prospek karir', 7, 1),
('fasilitas', 'Fasilitas & Laboratorium Terpadu', 'Sarana mini plant PKS, workstation PLC, dan software cloud hub', 8, 1),
('alur_biaya', 'Alur Pendaftaran & Skema UKT', 'Panduan 5 langkah seleksi online dan transparansi biaya kuliah', 9, 1),
('testimoni', 'Suara Mitra Industri & Mahasiswa', 'Ulasan kepuasan konsorsium pabrik sawit, alumni, dan awardee beasiswa', 10, 1),
('pengumuman', 'Pengumuman & Berita Terkini', 'Kabar terbaru hasil seleksi, agenda, dan informasi penting', 11, 1),
('faq', 'Tanya Jawab Populer (FAQ)', 'Akordeon interaktif pertanyaan umum calon mahasiswa baru', 12, 1),
('cta', 'Banner Ajakan Mendaftar (CTA)', 'Bilah penutup ajakan registrasi dan tombol konsultasi WhatsApp', 13, 1);

-- 3. Audit Trail (Activity Logs)
DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `user_role` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `detail` text NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_user` (`user_id`),
  KEY `idx_log_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `audit_logs` (`username`, `user_role`, `module`, `action`, `detail`, `ip_address`) VALUES
('superadmin', 'superadmin', 'system', 'migration', 'Inisialisasi Arsitektur Enterprise Super-Admin Antigravity', '127.0.0.1');

-- 4. Central Media Library
DROP TABLE IF EXISTS `media_library`;
CREATE TABLE `media_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `uploaded_by` varchar(50) DEFAULT 'superadmin',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `media_library` (`file_name`, `file_path`, `file_type`, `file_size`, `caption`, `uploaded_by`) VALUES
('logo-resmi.jpg', 'assets/images/logo-resmi.jpg', 'image/jpeg', 101386, 'Logo Resmi Politeknik Mulia Rokan', 'superadmin'),
('gedung-kampus.svg', 'assets/images/gedung-kampus.svg', 'image/svg+xml', 2364, 'Ilustrasi Gedung Kampus Utama Bagansiapiapi', 'superadmin');

-- 5. Master Data Akademik (Fakultas & Direktori Dosen/Staf)
DROP TABLE IF EXISTS `dosen_staf`;
DROP TABLE IF EXISTS `fakultas`;

CREATE TABLE `fakultas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_fakultas` varchar(20) NOT NULL UNIQUE,
  `nama_fakultas` varchar(150) NOT NULL,
  `dekan` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fakultas` (`id`, `kode_fakultas`, `nama_fakultas`, `dekan`) VALUES
(1, 'FV-INDUSTRI', 'Fakultas Vokasi Rekayasa & Agro-Industri', 'Dr. Ir. Hendra Saputra, M.T.'),
(2, 'FV-TIK', 'Fakultas Vokasi Teknologi Informasi & Digital', 'Ahmad Ridwan, S.Kom., M.Cs.');

CREATE TABLE `dosen_staf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(150) NOT NULL,
  `nidn` varchar(20) DEFAULT NULL,
  `jabatan` varchar(100) NOT NULL,
  `fakultas_id` int(11) DEFAULT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `bidang_keahlian` varchar(200) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status` enum('aktif','tugas_belajar','nonaktif') NOT NULL DEFAULT 'aktif',
  `urutan` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_dosen_fakultas` (`fakultas_id`),
  CONSTRAINT `fk_dosen_fakultas` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `dosen_staf` (`nama_lengkap`, `nidn`, `jabatan`, `fakultas_id`, `bidang_keahlian`, `status`, `urutan`) VALUES
('Ir. H. Syamsul Bahri, M.T.', '1012057501', 'Direktur / Lektor Kepala', 1, 'Refinery Minyak Sawit & Manajemen Industri PKS', 'aktif', 1),
('Dr. Ir. Hendra Saputra, M.T.', '1018088002', 'Wakil Direktur I / Dosen Senior', 1, 'Otomasi Industri, PLC & Fabrikasi Mekanik', 'aktif', 2),
('Ahmad Ridwan, S.Kom., M.Cs.', '1025118501', 'Ketua Jurusan TRPL / Dosen', 2, 'Software Architecture, Cloud Computing & IoT', 'aktif', 3),
('Nurhasanah, S.T., M.Eng.', '1004079001', 'Kepala Laboratorium Teaching Factory', 1, 'Teknologi Pengolahan Biomassa Kelapa Sawit', 'aktif', 4);

-- 6. Konfigurasi Sistem Lanjutan (Maintenance, SEO, Script Injector, SMTP, Payment API)
ALTER TABLE `site_settings`
ADD COLUMN IF NOT EXISTS `is_maintenance` tinyint(1) NOT NULL DEFAULT 0,
ADD COLUMN IF NOT EXISTS `maintenance_message` text DEFAULT 'Website Kami Sedang Dalam Pemeliharaan Berkala untuk Peningkatan Layanan. Silakan Hubungi Helpdesk WhatsApp PMB: 0812-3456-7890.',
ADD COLUMN IF NOT EXISTS `seo_meta_title` varchar(255) DEFAULT 'Politeknik Mulia Rokan - PMB TA 2026/2027',
ADD COLUMN IF NOT EXISTS `seo_meta_keywords` text DEFAULT 'pmb politeknik mulia rokan, kuliah vokasi sawit riau, pmb vokasi d4, beasiswa kip kuliah rokan hilir',
ADD COLUMN IF NOT EXISTS `seo_meta_description` text DEFAULT 'Penerimaan Mahasiswa Baru Politeknik Mulia Rokan TA 2026/2027. Program Sarjana Terapan (D4) Teknologi Rekayasa Pengolahan Sawit, Mesin & Perangkat Lunak.',
ADD COLUMN IF NOT EXISTS `custom_head_scripts` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `custom_body_scripts` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `smtp_host` varchar(150) DEFAULT 'smtp.gmail.com',
ADD COLUMN IF NOT EXISTS `smtp_port` int(11) DEFAULT 587,
ADD COLUMN IF NOT EXISTS `smtp_user` varchar(150) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `smtp_pass` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `smtp_encryption` varchar(10) DEFAULT 'tls',
ADD COLUMN IF NOT EXISTS `payment_gateway_mode` enum('sandbox','production') DEFAULT 'sandbox',
ADD COLUMN IF NOT EXISTS `payment_server_key` varchar(255) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `payment_client_key` varchar(255) DEFAULT NULL;
