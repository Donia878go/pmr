-- ========================================================
-- Upgrade CMS & Super Admin Tables for Politeknik Mulia Rokan
-- ========================================================
USE `pmb_mulia_rokan`;

-- 1. Table: site_settings
DROP TABLE IF EXISTS `site_settings`;
CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL PRIMARY KEY DEFAULT 1,
  `nama_kampus` varchar(150) NOT NULL DEFAULT 'POLITEKNIK MULIA ROKAN',
  `sub_nama` varchar(255) NOT NULL DEFAULT 'Kampus Vokasi Industri Kelapa Sawit, Rekayasa Manufaktur & Teknologi Digital',
  `tagline` varchar(255) NOT NULL DEFAULT 'Penerimaan Mahasiswa Baru TA 2026/2027',
  `logo_url` varchar(255) NOT NULL DEFAULT 'assets/images/logo-resmi.jpg',
  `logo_yayasan_url` varchar(255) NOT NULL DEFAULT 'assets/images/logo-yayasan.svg',
  `color_primary` varchar(20) NOT NULL DEFAULT '#0f766e',
  `color_primary_dark` varchar(20) NOT NULL DEFAULT '#115e59',
  `color_accent` varchar(20) NOT NULL DEFAULT '#f59e0b',
  `color_accent_dark` varchar(20) NOT NULL DEFAULT '#d97706',
  `running_text` text DEFAULT NULL,
  `hero_badge` varchar(150) DEFAULT '✨ Penerimaan Mahasiswa Baru TA 2026/2027 Resmi Dibuka',
  `hero_title` varchar(255) DEFAULT 'Wujudkan Karir Gemilang Bersama Politeknik Mulia Rokan',
  `hero_subtitle` text DEFAULT NULL,
  `hero_cta_text` varchar(100) DEFAULT 'Daftar PMB Sekarang',
  `hero_cta_link` varchar(200) DEFAULT 'daftar.php',
  `sambutan_direktur_nama` varchar(150) DEFAULT 'Ir. H. Syamsul Bahri, M.T.',
  `sambutan_direktur_jabatan` varchar(150) DEFAULT 'Direktur Politeknik Mulia Rokan',
  `sambutan_direktur_teks` text DEFAULT NULL,
  `sambutan_direktur_foto` varchar(255) DEFAULT 'assets/images/direktur.jpg',
  `visi_teks` text DEFAULT NULL,
  `misi_teks` text DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `email` varchar(100) DEFAULT 'pmb@politeknikmuliarokan.ac.id',
  `telepon` varchar(50) DEFAULT '(0767) 21998',
  `whatsapp_helpdesk` varchar(50) DEFAULT '0812-3456-7890',
  `jam_operasional` varchar(150) DEFAULT 'Senin - Jumat: 08.00 - 16.00 WIB | Sabtu: 08.00 - 13.00 WIB',
  `google_maps_iframe` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `site_settings` (
  `id`, `nama_kampus`, `sub_nama`, `tagline`, `logo_url`, `logo_yayasan_url`,
  `color_primary`, `color_primary_dark`, `color_accent`, `color_accent_dark`,
  `running_text`, `hero_badge`, `hero_title`, `hero_subtitle`, `hero_cta_text`, `hero_cta_link`,
  `sambutan_direktur_nama`, `sambutan_direktur_jabatan`, `sambutan_direktur_teks`,
  `visi_teks`, `misi_teks`,
  `alamat`, `email`, `telepon`, `whatsapp_helpdesk`, `jam_operasional`, `google_maps_iframe`
) VALUES (
  1,
  'POLITEKNIK MULIA ROKAN',
  'Kampus Vokasi Unggulan Sawit, Rekayasa Mesin & Rekayasa Perangkat Lunak Terapan',
  'Penerimaan Mahasiswa Baru TA 2026/2027',
  'assets/images/logo-resmi.jpg',
  'assets/images/logo-yayasan.svg',
  '#0f766e',
  '#115e59',
  '#f59e0b',
  '#d97706',
  '📢 PENERIMAAN MAHASISWA BARU TA 2026/2027: Dapatkan Beasiswa Penuh 100% Jalur Prestasi & KIP-Kuliah serta Penempatan Magang Bersertifikat di Konsorsium Industri Sawit Terkemuka Riau!',
  '✨ Pendaftaran Mahasiswa Baru TA 2026/2027 Resmi Dibuka',
  'Membangun Generasi Vokasi Unggul & Siap Kerja di Era Industri Modern',
  'Politeknik Mulia Rokan menyelenggarakan program Sarjana Terapan (D4) yang mengintegrasikan keahlian agro-industri kelapa sawit, rekayasa mesin modern, serta kecerdasan perangkat lunak digital dengan dukungan fasilitas teaching factory terdepan.',
  'Daftar PMB Sekarang',
  'daftar.php',
  'Ir. H. Syamsul Bahri, M.T.',
  'Direktur Politeknik Mulia Rokan',
  'Selamat datang di Kampus Politeknik Mulia Rokan. Kami berkomitmen menyelenggarakan pendidikan vokasi berkualitas internasional yang berakar kuat pada potensi lokal Rokan Hilir dan Riau. Melalui kurikulum berbasis industri dan kemitraan strategis, kami membekali setiap mahasiswa dengan sertifikasi kompetensi keahlian nyata agar langsung terserap di dunia kerja.',
  'Menjadi perguruan tinggi vokasi terkemuka di tingkat nasional yang menghasilkan lulusan unggul, berakhlak mulia, berjiwa technopreneur, dan berdaya saing tinggi di bidang industri perkebunan kelapa sawit, manufaktur, dan teknologi rekayasa digital pada tahun 2035.',
  '1. Menyelenggarakan pendidikan vokasi Sarjana Terapan (D4) berbasis kurikulum industri dan sertifikasi profesi BNSP.\n2. Mengembangkan riset terapan inovatif yang memberikan solusi praktis bagi industri perkebunan dan masyarakat luas.\n3. Melaksanakan program pengabdian masyarakat untuk memajukan pembangunan wilayah Rokan Hilir dan Riau.\n4. Membangun kerjasama strategis berkelanjutan dengan konsorsium industri kelapa sawit dan BUMN nasional.',
  'Jl. Lintas Bagansiapiapi - Ujung Tanjung Km. 4,5, Bagansiapiapi, Kabupaten Rokan Hilir, Riau 28912',
  'pmb@politeknikmuliarokan.ac.id',
  '(0767) 21998 / 0812-3456-7890',
  '0812-3456-7890',
  'Senin - Jumat: 08.00 - 16.00 WIB | Sabtu: 08.00 - 13.00 WIB',
  '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127599.4589254359!2d100.751336440625!3d2.1581454500000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3032d8479e9a4f47%3A0x6b4999c0172f88cf!2sBagansiapiapi%2C%20Bangko%2C%20Rokan%20Hilir%20Regency%2C%20Riau!5e0!3m2!1sen!2sid!4v1700000000000!5m2!1sen!2sid" width="100%" height="280" style="border:0; border-radius: 12px;" allowfullscreen="" loading="lazy"></iframe>'
);

-- 2. Table: social_media
DROP TABLE IF EXISTS `social_media`;
CREATE TABLE `social_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `nama_label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `social_media` (`platform`, `nama_label`, `url`, `icon`, `is_active`, `urutan`) VALUES
('instagram', 'Instagram', 'https://instagram.com/politeknikmuliarokan', 'instagram', 1, 1),
('facebook', 'Facebook', 'https://facebook.com/politeknikmuliarokan', 'facebook', 1, 2),
('youtube', 'YouTube Official', 'https://youtube.com/@politeknikmuliarokan', 'youtube', 1, 3),
('tiktok', 'TikTok', 'https://tiktok.com/@politeknikmuliarokan', 'tiktok', 1, 4),
('whatsapp', 'WhatsApp PMB', 'https://wa.me/6281234567890', 'whatsapp', 1, 5);

-- 3. Table: custom_menus
DROP TABLE IF EXISTS `custom_menus`;
CREATE TABLE `custom_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `judul` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` enum('_self','_blank') NOT NULL DEFAULT '_self',
  `urutan` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_menu_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Menus Utama & Dropdowns
INSERT INTO `custom_menus` (`id`, `parent_id`, `judul`, `url`, `target`, `urutan`, `is_active`) VALUES
(1, NULL, 'Beranda', 'index.php', '_self', 1, 1),
(2, NULL, 'Profil Kampus', '#', '_self', 2, 1),
(3, NULL, 'Program Studi', '#', '_self', 3, 1),
(4, NULL, 'Jalur PMB', '#', '_self', 4, 1),
(5, NULL, 'Informasi & Berita', '#', '_self', 5, 1),
(6, NULL, 'Layanan & Form', '#', '_self', 6, 1),
(7, NULL, 'Kontak', 'index.php#kontak', '_self', 7, 1);

-- Submenu Profil Kampus
INSERT INTO `custom_menus` (`parent_id`, `judul`, `url`, `target`, `urutan`, `is_active`) VALUES
(2, 'Tentang Politeknik Mulia Rokan', 'index.php#profil', '_self', 1, 1),
(2, 'Visi, Misi & Tujuan', 'index.php#visi-misi', '_self', 2, 1),
(2, 'Sambutan Direktur Kampus', 'index.php#sambutan', '_self', 3, 1),
(2, 'Fasilitas Kampus & Laboratorium', 'index.php#fasilitas', '_self', 4, 1),
(2, 'Kemitraan Industri Sawit', 'index.php#mitra', '_self', 5, 1);

-- Submenu Program Studi
INSERT INTO `custom_menus` (`parent_id`, `judul`, `url`, `target`, `urutan`, `is_active`) VALUES
(3, 'D4 Rekayasa Pengolahan Sawit (TRPKS)', 'index.php#section-prodi', '_self', 1, 1),
(3, 'D4 Rekayasa Mesin & Manufaktur (TRMM)', 'index.php#section-prodi', '_self', 2, 1),
(3, 'D4 Rekayasa Perangkat Lunak (TRPL)', 'index.php#section-prodi', '_self', 3, 1),
(3, 'Prospek Karir & Sertifikasi Profesi', 'index.php#section-prodi', '_self', 4, 1);

-- Submenu Jalur PMB
INSERT INTO `custom_menus` (`parent_id`, `judul`, `url`, `target`, `urutan`, `is_active`) VALUES
(4, 'Jalur Prestasi & KIP-Kuliah', 'index.php#section-jalur', '_self', 1, 1),
(4, 'Jalur Reguler Gelombang 1-3', 'index.php#section-jalur', '_self', 2, 1),
(4, 'Jalur Kerjasama Khusus Industri', 'index.php#section-jalur', '_self', 3, 1),
(4, 'Alur 5 Langkah Pendaftaran', 'index.php#section-alur', '_self', 4, 1),
(4, 'Biaya Kuliah (UKT) & Beasiswa', 'index.php#biaya', '_self', 5, 1);

-- Submenu Informasi & Berita
INSERT INTO `custom_menus` (`parent_id`, `judul`, `url`, `target`, `urutan`, `is_active`) VALUES
(5, 'Pengumuman Seleksi PMB', 'index.php#section-pengumuman', '_self', 1, 1),
(5, 'Berita & Kegiatan Kampus', 'index.php#berita', '_self', 2, 1),
(5, 'FAQ / Tanya Jawab PMB', 'index.php#faq', '_self', 3, 1),
(5, 'Unduh Brosur Lengkap PMB', 'index.php#brosur', '_self', 4, 1);

-- Submenu Layanan & Form
INSERT INTO `custom_menus` (`parent_id`, `judul`, `url`, `target`, `urutan`, `is_active`) VALUES
(6, 'Formulir Pendaftaran Mahasiswa Baru', 'daftar.php', '_self', 1, 1),
(6, 'Tracking & Cek Status Pendaftaran', 'cek_status.php', '_self', 2, 1),
(6, 'Survei Minat & Bakat Calon Maba', 'form.php?slug=survei-minat-bakat', '_self', 3, 1),
(6, 'Layanan Aspirasi & Pertanyaan', 'form.php?slug=layanan-aspirasi', '_self', 4, 1);

-- 4. Table: dynamic_forms
DROP TABLE IF EXISTS `dynamic_form_submissions`;
DROP TABLE IF EXISTS `dynamic_form_fields`;
DROP TABLE IF EXISTS `dynamic_forms`;

CREATE TABLE `dynamic_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul_form` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL UNIQUE,
  `deskripsi` text DEFAULT NULL,
  `pesan_sukses` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dynamic_form_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `field_name` varchar(100) NOT NULL,
  `label` varchar(200) NOT NULL,
  `tipe` enum('text','number','email','phone','date','textarea','select','radio','checkbox','file') NOT NULL DEFAULT 'text',
  `pilihan` text DEFAULT NULL,
  `placeholder` varchar(200) DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_field_form` (`form_id`),
  CONSTRAINT `fk_field_form` FOREIGN KEY (`form_id`) REFERENCES `dynamic_forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `dynamic_form_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `data_json` longtext NOT NULL,
  `file_attachment` varchar(255) DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `status` enum('baru','diproses','selesai') NOT NULL DEFAULT 'baru',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sub_form` (`form_id`),
  CONSTRAINT `fk_sub_form` FOREIGN KEY (`form_id`) REFERENCES `dynamic_forms` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Form 1: Survei Minat & Bakat Calon Maba
INSERT INTO `dynamic_forms` (`id`, `judul_form`, `slug`, `deskripsi`, `pesan_sukses`, `is_active`) VALUES
(1, 'Survei Minat & Bakat Calon Mahasiswa Baru 2026', 'survei-minat-bakat', 'Bantu kami mengenal Anda lebih dekat untuk program penyesuaian beasiswa, mentoring bakat, dan proyek industri di Politeknik Mulia Rokan.', 'Terima kasih telah mengisi survei minat dan bakat! Tim PMB kami akan mengonfirmasi rekomendasi jalur beasiswa terbaik untuk Anda.', 1),
(2, 'Layanan Aspirasi, Konsultasi & Pengaduan PMB', 'layanan-aspirasi', 'Sampaikan pertanyaan, masukan, kendala pendaftaran, atau usulan langsung kepada Direksi dan Panitia PMB Politeknik Mulia Rokan.', 'Terima kasih, aspirasi atau pertanyaan Anda telah kami terima dan akan segera dibalas oleh tim helpdesk melalui WhatsApp/Email.', 1);

-- Fields Form 1
INSERT INTO `dynamic_form_fields` (`form_id`, `field_name`, `label`, `tipe`, `pilihan`, `placeholder`, `is_required`, `urutan`) VALUES
(1, 'nama_lengkap', 'Nama Lengkap Calon Mahasiswa', 'text', NULL, 'Contoh: Ahmad Rizki Maulana', 1, 1),
(1, 'nomor_wa', 'Nomor WhatsApp Aktif', 'phone', NULL, '0812xxxxxxxx', 1, 2),
(1, 'asal_sekolah', 'Asal Sekolah (SMA / SMK / MA)', 'text', NULL, 'Nama SMA/SMK dan Kota/Kabupaten', 1, 3),
(1, 'bidang_minat', 'Bidang Keahlian yang Paling Anda Minati', 'select', 'Otomatisasi Pabrik Kelapa Sawit (CPO),Desain Mekanik Mesin & Robotika,Pemrograman Web & Mobile App Enterprise,Kecerdasan Buatan (AI) & IoT Pertanian,Manajemen Perkebunan Berkelanjutan', NULL, 1, 4),
(1, 'motivasi_kuliah', 'Apa Motivasi Utama Anda Melanjutkan Kuliah Vokasi?', 'textarea', NULL, 'Ceritakan cita-cita karir impian Anda setelah lulus nanti...', 1, 5),
(1, 'minat_ekskul', 'Minat Kegiatan Organisasi / Ekstrakurikuler', 'radio', 'Robotika & Coding,Olahraga & Seni,Pramuka & Pecinta Alam,Kewirausahaan / Bisnis Sawit', NULL, 1, 6);

-- Fields Form 2
INSERT INTO `dynamic_form_fields` (`form_id`, `field_name`, `label`, `tipe`, `pilihan`, `placeholder`, `is_required`, `urutan`) VALUES
(2, 'nama_pengirim', 'Nama Pengirim / Orang Tua / Pendaftar', 'text', NULL, 'Nama lengkap Anda', 1, 1),
(2, 'kontak_wa', 'Nomor WhatsApp / HP', 'phone', NULL, '08xxxxxxxxxx', 1, 2),
(2, 'email_pengirim', 'Alamat Email', 'email', NULL, 'email@contoh.com', 0, 3),
(2, 'kategori_layanan', 'Kategori Aspirasi / Pertanyaan', 'select', 'Konsultasi Beasiswa KIP-K / Sawit,Kendala Upload Dokumen PMB,Informasi Asrama & Fasilitas,Kerjasama Sekolah / Kunjungan Kampus,Lainnya', NULL, 1, 4),
(2, 'isi_pesan', 'Detail Pertanyaan / Aspirasi', 'textarea', NULL, 'Tuliskan secara jelas rincian pertanyaan atau kendala Anda...', 1, 5),
(2, 'lampiran_file', 'Lampiran Dokumen / Bukti (Opsional)', 'file', NULL, NULL, 0, 6);

-- 5. Table: fasilitas_kampus
DROP TABLE IF EXISTS `fasilitas_kampus`;
CREATE TABLE `fasilitas_kampus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_fasilitas` varchar(150) NOT NULL,
  `kategori` varchar(50) DEFAULT 'Akademik',
  `deskripsi` text NOT NULL,
  `icon` varchar(50) DEFAULT 'building',
  `gambar` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `fasilitas_kampus` (`nama_fasilitas`, `kategori`, `deskripsi`, `icon`, `urutan`) VALUES
('Teaching Factory Mini Pabrik Kelapa Sawit (PKS)', 'Laboratorium Khusus', 'Fasilitas simulasi proses pengolahan TBS, boiler uap, press station, clarification hingga refinery CPO dengan sensor digital.', 'factory', 1),
('Laboratorium Otomasi Mesin & PLC Industri', 'Laboratorium Teknik', 'Dilengkapi workstation PLC Siemens, pneumatic control system, sensor industri, dan trainer mekanika manufaktur presisi.', 'cpu', 2),
('Software Engineering & Cloud Computing Hub', 'Laboratorium Komputer', 'Laboratorium komputer spesifikasi tinggi dengan jaringan gigabit, server cloud lokal, dan software enterprise developer.', 'code', 3),
('Bengkel Manufaktur Mesin CNC & Fabrikasi', 'Workshop Praktik', 'Mesin bubut, frais, CNC milling, las berstandar industri serta perkakas presisi untuk pembuatan suku cadang mesin pabrik.', 'wrench', 4),
('Perpustakaan Digital & Modern Co-Working Space', 'Pusat Belajar', 'Ribuan koleksi buku ilmiah, akses jurnal internasional bereputasi, area diskusi ber-AC, dan koneksi internet cepat 1 Gbps.', 'book-open', 5),
('Asrama Mahasiswa & Kompleks Olahraga Terpadu', 'Fasilitas Mahasiswa', 'Kamar asrama nyaman dengan pengawasan 24 jam, kantin sehat, lapangan futsal, basket, voli, dan masjid kampus.', 'home', 6);

-- 6. Table: testimoni_mitra
DROP TABLE IF EXISTS `testimoni_mitra`;
CREATE TABLE `testimoni_mitra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `role_instansi` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `tipe` enum('mitra_industri','alumni','mahasiswa') NOT NULL DEFAULT 'mitra_industri',
  `urutan` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `testimoni_mitra` (`nama`, `role_instansi`, `pesan`, `tipe`, `urutan`) VALUES
('PT. Rokan Palm Industri', 'Konsorsium Pabrik Kelapa Sawit', 'Kurikulum Politeknik Mulia Rokan sangat selaras dengan kebutuhan nyata pabrik sawit modern. Mahasiswa magang memiliki etos kerja tinggi dan menguasai kendali otomatisasi.', 'mitra_industri', 1),
('Fajar Pratama, S.Tr.T', 'Alumni D4 TRPKS - Mill Assistant', 'Kuliah di Politeknik Mulia Rokan memberikan pengalaman praktik langsung 70% di teaching factory. Belum wisuda saya sudah diterima kerja di pabrik kelapa sawit ternama.', 'alumni', 2),
('Nurul Aini', 'Mahasiswi D4 TRPL - Penerima Beasiswa', 'Fasilitas kampus sangat lengkap dan dosen-dosennya praktisi berpengalaman. Saya mendapat beasiswa penuh dan kesempatan mengerjakan proyek software industri nyata.', 'mahasiswa', 3);

-- 7. Table: berita_artikel
DROP TABLE IF EXISTS `berita_artikel`;
CREATE TABLE `berita_artikel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `ringkasan` text NOT NULL,
  `isi` longtext NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT 'Berita',
  `penulis` varchar(100) DEFAULT 'Humas PMR',
  `tanggal` date NOT NULL,
  `status` enum('publish','draft') NOT NULL DEFAULT 'publish',
  `views` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `berita_artikel` (`judul`, `slug`, `ringkasan`, `isi`, `kategori`, `tanggal`, `views`) VALUES
('Politeknik Mulia Rokan Jalin Kerjasama Strategis dengan 15 Perusahaan Industri Sawit Terkemuka', 'kerjasama-strategis-industri-sawit-2026', 'MoU strategis disepakati untuk penyerapan lulusan, program magang bersertifikat industri 6 bulan, dan beasiswa pendidikan penuh bagi mahasiswa berprestasi.', '<p>Bagansiapiapi — Politeknik Mulia Rokan secara resmi menandatangani nota kesepahaman (MoU) kemitraan industri terpadu dengan 15 konsorsium pabrik kelapa sawit dan industri manufaktur di wilayah Riau dan Sumatera. Kerjasama ini bertujuan untuk mengoptimalkan link-and-match pendidikan vokasi dengan dunia usaha dan industri kerja nyata.</p><p>Direktur Politeknik Mulia Rokan menyampaikan bahwa program ini menjamin seluruh mahasiswa tingkat akhir mendapatkan penempatan magang bergaji dan peluang langsung diangkat sebagai staf ahli teknik di industri terkait.</p>', 'Kemitraan', '2026-02-10', 342),
('Kemenristekdikti Apresiasi Laboratorium Teaching Factory Otomasi Sawit Modern Politeknik Mulia Rokan', 'apresiasi-teaching-factory-sawit', 'Fasilitas mini plant pengolahan kelapa sawit berbasis sensor IoT dinilai sebagai salah satu percontohan teaching factory terbaik di wilayah Sumatera.', '<p>Politeknik Mulia Rokan terus membuktikan komitmennya dalam menghadirkan sarana prasarana vokasi mutakhir. Fasilitas Teaching Factory Mini PKS yang dilengkapi stasiun kendali terpusat SCADA dan PLC mendapat sambutan hangat dari para asesor dan pakar industri nasional.</p>', 'Akademik', '2026-01-25', 218),
('Sosialisasi Beasiswa KIP-Kuliah & Jalur Prestasi Bebas SPP Dibuka untuk Siswa se-Riau', 'sosialisasi-beasiswa-kip-kuliah-2026', 'Panitia PMB Politeknik Mulia Rokan menyambangi puluhan sekolah menengah atas di Rokan Hilir dan sekitarnya guna menginformasikan kemudahan akses beasiswa.', '<p>Guna memastikan pemerataan pendidikan tinggi berkualitas bagi putra-putri daerah, tim sosialisasi PMB Politeknik Mulia Rokan aktif mendatangi SMA/SMK/MA sederajat. Program beasiswa yang ditawarkan mencakup pembebasan biaya kuliah 100% hingga bantuan asrama dan uang saku pembinaan.</p>', 'PMB', '2026-02-01', 450);

-- 8. Pastikan user admin_users lengkap dengan superadmin dan panitia
INSERT INTO `admin_users` (`username`, `password`, `nama_lengkap`, `role`) VALUES
('superadmin', '$2y$10$4n93U7iWdD8V2WsmJtA.eeWlP2dI9u64nCclD70uFkJ7r1wL5sEce', 'Super Administrator PMR', 'superadmin'),
('panitia', '$2y$10$4n93U7iWdD8V2WsmJtA.eeWlP2dI9u64nCclD70uFkJ7r1wL5sEce', 'Panitia Seleksi PMB', 'panitia')
ON DUPLICATE KEY UPDATE `nama_lengkap` = VALUES(`nama_lengkap`), `role` = VALUES(`role`);
