-- ========================================================
-- Upgrade Multimedia, Background Customization & Features
-- ========================================================
USE `pmb_mulia_rokan`;

-- Tambah kolom-kolom multimedia dan background ke site_settings jika belum ada
ALTER TABLE `site_settings`
ADD COLUMN IF NOT EXISTS `hero_bg_image` varchar(255) DEFAULT 'assets/images/gedung-kampus.svg',
ADD COLUMN IF NOT EXISTS `hero_bg_opacity` int(11) DEFAULT 80,
ADD COLUMN IF NOT EXISTS `audio_title` varchar(150) DEFAULT 'Mars Politeknik Mulia Rokan',
ADD COLUMN IF NOT EXISTS `audio_url` varchar(255) DEFAULT 'assets/audio/mars-politeknik.mp3',
ADD COLUMN IF NOT EXISTS `audio_is_active` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `video_hero_url` varchar(255) DEFAULT 'https://www.youtube.com/watch?v=ScMzIvxBSi4',
ADD COLUMN IF NOT EXISTS `video_hero_title` varchar(200) DEFAULT 'Profil Eksklusif Kampus Politeknik Mulia Rokan',
ADD COLUMN IF NOT EXISTS `popup_is_active` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `popup_title` varchar(200) DEFAULT 'Beasiswa Penuh 100% Dibuka!',
ADD COLUMN IF NOT EXISTS `popup_content` text DEFAULT 'Pendaftaran Mahasiswa Baru Politeknik Mulia Rokan TA 2026/2027 kini menyediakan kuota beasiswa bebas SPP 8 semester penuh untuk lulusan SMA/SMK berprestasi dan mitra industri sawit.',
ADD COLUMN IF NOT EXISTS `popup_cta_text` varchar(100) DEFAULT 'Ambil Kuota Beasiswa',
ADD COLUMN IF NOT EXISTS `popup_cta_link` varchar(200) DEFAULT 'daftar.php';

-- Table: fitur_unggulan
DROP TABLE IF EXISTS `fitur_unggulan`;
CREATE TABLE `fitur_unggulan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul_fitur` varchar(150) NOT NULL,
  `sub_judul` varchar(200) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `icon` varchar(50) DEFAULT '⚡',
  `badge` varchar(50) DEFAULT 'Keunggulan',
  `link_url` varchar(200) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fitur_unggulan` (`judul_fitur`, `sub_judul`, `deskripsi`, `icon`, `badge`, `link_url`, `urutan`) VALUES
('Teaching Factory Pabrik Sawit Mini', 'PKS Berbasis Digital SCADA', 'Mahasiswa berpraktek langsung pada stasiun sterilizer, thresher, digester, press, dan klarifikasi CPO dengan pemantauan sensor otomatis.', '🏭', 'Fasilitas Langka', 'index.php#fasilitas', 1),
('Jaminan Penempatan Magang Industri', 'Konsorsium 15+ Pabrik Kelapa Sawit', 'Program magang kerja bersertifikat 6 bulan di industri sawit dan manufaktur dengan uang saku dan peluang rekrutmen langsung.', '🤝', 'Link & Match', 'index.php#mitra', 2),
('Sertifikasi Kompetensi BNSP Resmi', 'Uji Kompetensi Terlisensi Nasional', 'Setiap lulusan tidak hanya meraih Ijazah D4 melainkan juga mengantongi Sertifikat Profesi BNSP bidang otomasi dan rekayasa kelapa sawit.', '📜', 'Standar BNSP', 'index.php#section-prodi', 3),
('Beasiswa Bebas SPP 8 Semester', 'Jalur Prestasi & KIP-Kuliah', 'Bebas uang gedung (SPI Rp 0) dan kesempatan kuliah gratis 100% hingga lulus bagi siswa berprestasi ranking 1-5.', '🎓', 'Bebas Biaya', 'index.php#biaya', 4),
('Laboratorium Rekayasa Software & IoT', 'Pusat Inovasi Agroteknologi Digital', 'Pengembangan perangkat cerdas berbasis sensor IoT untuk pemantauan perkebunan sawit cerdas (smart palm plantation).', '💻', 'Era Industri 4.0', 'index.php#fasilitas', 5),
('Asrama Mahasiswa & Pembinaan Karakter', 'Fasilitas Hunian Terpadu', 'Lingkungan kampus asri dan aman dengan fasilitas asrama, internet gigabit, sarana olahraga, dan pembinaan kedisiplinan mental.', '🏡', 'Kampus Asri', 'index.php#fasilitas', 6);

-- Table: video_kampus
DROP TABLE IF EXISTS `video_kampus`;
CREATE TABLE `video_kampus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `video_type` enum('youtube','mp4') NOT NULL DEFAULT 'youtube',
  `video_url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `urutan` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `video_kampus` (`judul`, `deskripsi`, `video_type`, `video_url`, `thumbnail`, `is_featured`, `urutan`) VALUES
('Profil Kampus Politeknik Mulia Rokan: Mewujudkan Generasi Vokasi Terdepan', 'Tur virtual lingkungan kampus utama, teaching factory mini plant PKS, dan laboratorium otomasi industri presisi.', 'youtube', 'https://www.youtube.com/embed/ScMzIvxBSi4', 'assets/images/gedung-kampus.svg', 1, 1),
('Aktivitas Praktik Mahasiswa di Teaching Factory Pengolahan Kelapa Sawit', 'Dokumentasi praktikum pengoperasian stasiun press dan pengujian mutu refinery minyak kelapa sawit mentah (CPO).', 'youtube', 'https://www.youtube.com/embed/ScMzIvxBSi4', 'assets/images/gedung-kampus.svg', 0, 2),
('Testimoni Industri Konsorsium Pabrik Kelapa Sawit Riau', 'Tanggapan para mill manager mengenai etos kerja dan kompetensi lulusan Politeknik Mulia Rokan di pabrik perkebunan.', 'youtube', 'https://www.youtube.com/embed/ScMzIvxBSi4', 'assets/images/gedung-kampus.svg', 0, 3);
