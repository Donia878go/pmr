-- ========================================================
-- Database: pmb_mulia_rokan
-- Dibuat untuk: XAMPP (Apache + MySQL / MariaDB + PHP)
-- Politeknik Mulia Rokan - PMB TA 2026/2027
-- ========================================================

CREATE DATABASE IF NOT EXISTS `pmb_mulia_rokan` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pmb_mulia_rokan`;

-- --------------------------------------------------------
-- Table structure for table `admin_users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('superadmin','panitia') NOT NULL DEFAULT 'panitia',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password default: admin123
INSERT INTO `admin_users` (`id`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(1, 'admin', '$2y$10$4n93U7iWdD8V2WsmJtA.eeWlP2dI9u64nCclD70uFkJ7r1wL5sEce', 'Administrator PMB Mulia Rokan', 'superadmin');

-- --------------------------------------------------------
-- Table structure for table `jalur_pendaftaran`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `jalur_pendaftaran`;
CREATE TABLE `jalur_pendaftaran` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_jalur` varchar(20) NOT NULL UNIQUE,
  `nama_jalur` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `biaya_daftar` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tgl_buka` date NOT NULL,
  `tgl_tutup` date NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `jalur_pendaftaran` (`id`, `kode_jalur`, `nama_jalur`, `deskripsi`, `biaya_daftar`, `tgl_buka`, `tgl_tutup`, `status`) VALUES
(1, 'PRESTASI', 'Jalur Prestasi & KIP-Kuliah', 'Bebas uang pendaftaran, seleksi nilai rapor semester 1-5 atau sertifikat prestasi kejuaraan akademik/non-akademik.', 0.00, '2026-01-01', '2026-04-30', 'aktif'),
(2, 'REGULER-1', 'Jalur Reguler Gelombang 1', 'Jalur umum untuk lulusan SMA/SMK/MA sederajat dengan potongan biaya UKT awal 25% dan beasiswa yayasan.', 150000.00, '2026-02-01', '2026-05-31', 'aktif'),
(3, 'INDUSTRI', 'Jalur Kerjasama Industri Sawit', 'Program ikatan kerja & magang terpadu dengan konsorsium pabrik kelapa sawit & perkebunan di Riau.', 200000.00, '2026-03-01', '2026-06-30', 'aktif');

-- --------------------------------------------------------
-- Table structure for table `program_studi`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `program_studi`;
CREATE TABLE `program_studi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_prodi` varchar(20) NOT NULL UNIQUE,
  `nama_prodi` varchar(100) NOT NULL,
  `jenjang` enum('D3','D4') NOT NULL,
  `akreditasi` varchar(10) NOT NULL DEFAULT 'Baik',
  `kuota` int(11) NOT NULL DEFAULT 60,
  `deskripsi` text DEFAULT NULL,
  `prospek_karir` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `program_studi` (`id`, `kode_prodi`, `nama_prodi`, `jenjang`, `akreditasi`, `kuota`, `deskripsi`, `prospek_karir`) VALUES
(1, 'TRPKS', 'Teknologi Rekayasa Pengolahan Kelapa Sawit', 'D4', 'Baik', 75, 'Fokus keahlian otomatisasi pabrik kelapa sawit (PKS), kendali mutu refinery CPO, dan konversi energi biomassa.', 'Asisten Mill Manager, Quality Control PKS, Sustainability Officer Perkebunan, Process Engineer CPO.'),
(2, 'TRMM', 'Teknologi Rekayasa Mesin & Manufaktur', 'D4', 'Baik', 60, 'Fokus rancang bangun mekanik presisi, PLC otomasi industri modern, fabrikasi, dan perawatan alat berat perkebunan.', 'Maintenance Engineer Pabrik, CNC & CAD Specialist, Heavy Equipment Supervisor, Fabrikasi Industri.'),
(3, 'TRPL', 'Teknologi Rekayasa Perangkat Lunak', 'D4', 'Baik', 80, 'Fokus pengembangan software aplikasi web & mobile enterprise, integrasi sensor IoT industri, dan database cerdas.', 'Fullstack Web Developer, Mobile App Engineer, IoT Specialist Perkebunan Cerdas, Database Administrator.');

-- --------------------------------------------------------
-- Table structure for table `pendaftar`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pendaftar`;
CREATE TABLE `pendaftar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_pendaftaran` varchar(30) NOT NULL UNIQUE,
  `nama_lengkap` varchar(150) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `no_whatsapp` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `asal_sekolah` varchar(150) NOT NULL,
  `jurusan_sekolah` varchar(100) DEFAULT NULL,
  `tahun_lulus` year(4) NOT NULL,
  `jalur_id` int(11) NOT NULL,
  `prodi_pilihan_1` int(11) NOT NULL,
  `prodi_pilihan_2` int(11) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `berkas_rapor` varchar(255) DEFAULT NULL,
  `berkas_kk` varchar(255) DEFAULT NULL,
  `status_seleksi` enum('menunggu','terverifikasi','lulus','tidak_lulus') NOT NULL DEFAULT 'menunggu',
  `catatan_panitia` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pendaftar_jalur` (`jalur_id`),
  KEY `fk_pendaftar_prodi1` (`prodi_pilihan_1`),
  KEY `fk_pendaftar_prodi2` (`prodi_pilihan_2`),
  CONSTRAINT `fk_pendaftar_jalur` FOREIGN KEY (`jalur_id`) REFERENCES `jalur_pendaftaran` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pendaftar_prodi1` FOREIGN KEY (`prodi_pilihan_1`) REFERENCES `program_studi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pendaftar_prodi2` FOREIGN KEY (`prodi_pilihan_2`) REFERENCES `program_studi` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pendaftar` (`id`, `no_pendaftaran`, `nama_lengkap`, `nik`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `no_whatsapp`, `email`, `alamat`, `asal_sekolah`, `jurusan_sekolah`, `tahun_lulus`, `jalur_id`, `prodi_pilihan_1`, `prodi_pilihan_2`, `status_seleksi`, `catatan_panitia`) VALUES
(1, 'PMB-2026-0001', 'Ahmad Fadillah', '1407011205040001', 'L', 'Bagansiapiapi', '2007-05-12', '081234567890', 'ahmad.fadillah@gmail.com', 'Jl. Merdeka No. 45, Bagansiapiapi, Rokan Hilir', 'SMKN 1 Bangko', 'Teknik Pemesinan', 2025, 1, 1, 2, 'lulus', 'Selamat! Berkas dan nilai rapor memenuhi syarat beasiswa jalur prestasi. Silakan unduh bukti kelulusan.'),
(2, 'PMB-2026-0002', 'Siti Rahmawati', '1407024808050003', 'P', 'Ujung Tanjung', '2007-08-18', '082198765432', 'siti.rahma@yahoo.com', 'Jl. Lintas Riau-Sumut Km 12, Ujung Tanjung', 'SMAN 1 Tanah Putih', 'MIPA', 2026, 2, 3, 1, 'terverifikasi', 'Berkas telah diverifikasi lengkap oleh panitia seleksi. Menunggu jadwal tes wawancara online.'),
(3, 'PMB-2026-0003', 'Rian Kurniawan', '1407031502040002', 'L', 'Bagan Batu', '2006-02-15', '085277889900', 'rian.k@gmail.com', 'Jl. Jenderal Sudirman No. 88, Bagan Batu', 'SMK Swasta Pembangunan', 'Teknik Komputer & Jaringan', 2025, 3, 2, 3, 'menunggu', 'Pendaftaran berhasil dikirim. Menunggu verifikasi dokumen oleh panitia PMB.');

-- --------------------------------------------------------
-- Table structure for table `pengumuman`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengumuman`;
CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL UNIQUE,
  `kategori` enum('pengumuman','jadwal','beasiswa') NOT NULL DEFAULT 'pengumuman',
  `isi` text NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('publish','draft') NOT NULL DEFAULT 'publish',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pengumuman` (`id`, `judul`, `slug`, `kategori`, `isi`, `tanggal`, `status`) VALUES
(1, 'Pembukaan Pendaftaran Mahasiswa Baru Politeknik Mulia Rokan TA 2026/2027', 'pembukaan-pmb-2026-2027', 'jadwal', 'Politeknik Mulia Rokan secara resmi membuka Penerimaan Mahasiswa Baru Tahun Akademik 2026/2027 untuk jenjang Sarjana Terapan (D4). Tersedia kuota beasiswa bebas SPP untuk 100 pendaftar pertama dan fasilitas asrama.', '2026-01-10', 'publish'),
(2, 'Panduan Verifikasi Berkas Jalur Prestasi & KIP-Kuliah', 'panduan-verifikasi-jalur-prestasi', 'beasiswa', 'Calon mahasiswa baru yang mendaftar melalui Jalur Prestasi diwajibkan mengunggah scan rapor legalisir semester 1 s.d 5 serta sertifikat penghargaan akademik/non-akademik minimal tingkat kabupaten.', '2026-01-18', 'publish'),
(3, 'Informasi Kerjasama Penempatan Magang Industri Sawit Riau', 'kerjasama-industri-sawit-riau', 'pengumuman', 'Politeknik Mulia Rokan telah menandatangani MoU dengan 12 konsorsium pabrik kelapa sawit di Riau untuk program magang bersertifikat dan prioritas rekrutmen lulusan.', '2026-02-05', 'publish');
