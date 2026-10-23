# Portal PMB & Website Resmi Politeknik Mulia Rokan TA 2026/2027
**Sistem Informasi Terpadu: Website Kampus Vokasi + Dynamic CMS Super Admin + Panel Panitia PMB**

Sistem ini dibangun khusus untuk **Politeknik Mulia Rokan**, Kabupaten Rokan Hilir, Riau dengan identitas visual resmi berbasis warna **Hijau Teal Gerigi Industri & Emas Matahari Sawit**.

---

## 🔐 Keamanan & Akses Tersembunyi (Hidden Login)

Sesuai instruksi khusus, **seluruh tombol dan tautan login admin maupun super admin sengaja TIDAK DITAMPILKAN pada halaman publik/web depan** agar keamanan portal terjaga dari pihak yang tidak berwenang.

Akses panel hanya dapat dibuka secara langsung melalui pengetikan URL berikut di bilah alamat browser:

| Portal Akses | Alamat URL Langsung di Browser | Kredensial Login Default |
| :--- | :--- | :--- |
| **Super Admin CMS Portal** *(Kendali Penuh Web Depan, Background, Video, Audio & Form)* | [http://localhost/super-admin](http://localhost/super-admin) | **Username:** `superadmin`<br>**Password:** `superadmin123` *(atau admin / admin123)* |
| **Panel Admin Panitia PMB** *(Pendaftaran, Verifikasi Berkas & Kelulusan)* | [http://localhost/admin](http://localhost/admin) | **Username:** `panitia`<br>**Password:** `panitia123` *(atau admin / admin123)* |
| **Website Utama (Publik / Mahasiswa)** | [http://localhost/](http://localhost/) | Akses Publik Bebas |

---

## 🌟 Fitur Unggulan Terbaru & Sangat Lengkap

### 1. Tampilan Web Depan & Background Identitas Kampus
- **Background Sesuai Tema Warna:** Latar belakang utama menggunakan *Teal Canvas* (`#042f2e` & `#021e1b`) berpadu gradien radial zamrud dan amber yang elegan.
- **Custom Foto Background Kampus:** Latar belakang Hero menampilkan foto gedung/kampus Politeknik Mulia Rokan yang dilapisi *teal overlay* berkelas. Foto ini dan kepekatan overlay gelapnya dapat diganti/diatur kapan saja melalui panel Super Admin.
- **Floating Audio / Mars Kampus Player:** Di pojok kiri bawah terdapat pemutar musik mengambang dengan tombol Play/Pause dan animasi equalizer yang dapat memutar lagu Mars / Hymne Politeknik Mulia Rokan.
- **Galeri Video Interaktif:** Seksi khusus pemutar video kampus (profil kampus, tur laboratorium otomasi PKS, liputan magang industri sawit).
- **Pop-up Modal Promo:** Pengumuman beasiswa 100% yang menyapa pengunjung secara otomatis saat membuka website.
- **Menu Navigasi Bertingkat (Dropdown):**
  - **Beranda**
  - **Profil Kampus ▾** (Tentang Politeknik, Visi Misi & Tujuan, Sambutan Direktur, Fasilitas & Lab, Kemitraan Industri Sawit)
  - **Program Studi ▾** (D4 TRPKS Pengolahan Sawit, D4 TRMM Mesin Manufaktur, D4 TRPL Software, Sertifikasi BNSP)
  - **Jalur PMB ▾** (Jalur Prestasi & KIP-K Bebas SPP, Jalur Reguler, Jalur Kerjasama Industri, Alur 5 Langkah, Rincian UKT)
  - **Informasi & Berita ▾** (Pengumuman Seleksi, Berita Kampus, FAQ / Tanya Jawab, Unduh Brosur)
  - **Layanan & Form ▾** (Pendaftaran Online, Tracking & Cek Status, Survei Minat & Bakat, Layanan Aspirasi & Pengaduan)
  - **Kontak**

---

### 2. Panel Super Admin (`/super-admin`) — Pusat Kendali Paling Lengkap
1. **Ganti Logo & Custom Foto Background Kampus (`pengaturan_web.php`):**
   - Upload file logo resmi baru (JPG, PNG, SVG).
   - Upload foto latar belakang gedung kampus baru.
   - Atur slider kepekatan warna overlay gelap (30% s.d. 95%).
   - Ubah warna tema primer teal dan aksen emas.
   - Ubah teks berjalan pengumuman (*Running Announcement Ticker*).
2. **Tambah Fitur Baru di Web Utama (`kelola_fitur.php`):**
   - Menambahkan kartu keunggulan/fitur baru (ikon emoji, judul fitur, subjudul, deskripsi, badge, tautan tombol).
   - Langsung otomatis tampil di halaman beranda web depan.
3. **Galeri Video & YouTube (`kelola_video.php`):**
   - Menambahkan video YouTube atau file MP4.
   - Mengatur video utama (*Featured Video*) dan playlist video pendukung.
4. **Musik & Audio Kampus / Mars (`kelola_audio.php`):**
   - Mengunggah file lagu MP3/WAV atau memasukkan URL streaming audio.
   - Mengatur judul lagu dan mengaktifkan/menonaktifkan pemutar musik mengambang.
5. **Kelola & Tambah Logo Sosial Media (`kelola_sosmed.php`):**
   - Menambahkan platform baru (Instagram, TikTok, YouTube, WhatsApp, Facebook, Telegram, Threads, Spotify, dll.).
   - Mengunggah file gambar logo sosial media kustom sendiri atau menggunakan ikon preset.
6. **Banner Pop-up Modal Promo (`kelola_popup.php`):**
   - Mengaktifkan/menonaktifkan modal pop-up promosi, mengatur judul, teks, dan tombol pendaftaran.
7. **Menu & Dropdown Builder (`menu_builder.php`):**
   - Menambah menu bar utama atau submenu dropdown bertingkat.
8. **Dynamic Form Builder Sangat Lengkap (`form_builder.php` & `form_responses.php`):**
   - Membuat formulir baru tanpa batasan (Text, Number, Email, WhatsApp, Date, Textarea, Dropdown, Radio, Checkbox, Upload Dokumen/Foto).
   - Melihat data respon submission yang masuk dari publik dan mengunduh file lampiran.
9. **Editor Tulisan & Banner Depan (`editor_tampilan.php`):**
   - Mengubah teks Headline Hero, subjudul, tombol CTA, sambutan Direktur, dan visi misi.
10. **Kelola Fasilitas, Testimoni Mitra Sawit, Berita, Kuota Prodi, dan Akun Petugas.**

---

### 3. Panel Admin Panitia PMB (`/admin`) — Operasional PMB
1. **Daftarkan Calon Peserta Baru Offline (`tambah_pendaftar.php`):** Input pendaftar yang datang langsung ke kampus secara manual dengan nomor pendaftaran otomatis.
2. **Data & Verifikasi Berkas (`pendaftar.php`):** Cek pas foto, rapor, KK, beri catatan panitia, dan putuskan status (Lulus / Terverifikasi / Tidak Lulus).
3. **Cetak Dokumen:** Cetak Kartu Tanda Peserta PMB & Surat Keterangan Lulus (SKL) resmi kampus bertanda tangan.
4. **Ekspor Data:** Unduh rekap calon mahasiswa ke Excel/CSV.

---

## 🚀 Panduan Akses di Browser

Pastikan modul **Apache** dan **MySQL** pada **XAMPP Control Panel** dalam status **Running**, kemudian buka:
- **Tampilan Web Depan:** [http://localhost/](http://localhost/)
- **Login Super Admin:** [http://localhost/super-admin](http://localhost/super-admin) *(User: `superadmin` | Pass: `superadmin123`)*
- **Login Panitia PMB:** [http://localhost/admin](http://localhost/admin) *(User: `panitia` | Pass: `panitia123`)*
