<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Ambil Jalur dan Prodi
$jalur_list = $pdo->query("SELECT * FROM jalur_pendaftaran WHERE status = 'aktif' ORDER BY id ASC")->fetchAll();
$prodi_list = $pdo->query("SELECT * FROM program_studi ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap    = trim($_POST['nama_lengkap'] ?? '');
    $nik             = trim($_POST['nik'] ?? '');
    $jenis_kelamin   = $_POST['jenis_kelamin'] ?? 'L';
    $tempat_lahir    = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir   = $_POST['tanggal_lahir'] ?? '';
    $no_whatsapp     = trim($_POST['no_whatsapp'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $alamat          = trim($_POST['alamat'] ?? '');
    $asal_sekolah    = trim($_POST['asal_sekolah'] ?? '');
    $jurusan_sekolah = trim($_POST['jurusan_sekolah'] ?? '');
    $tahun_lulus     = (int)($_POST['tahun_lulus'] ?? date('Y'));
    $jalur_id        = (int)($_POST['jalur_id'] ?? 0);
    $prodi_1         = (int)($_POST['prodi_pilihan_1'] ?? 0);
    $prodi_2         = !empty($_POST['prodi_pilihan_2']) ? (int)$_POST['prodi_pilihan_2'] : null;
    $status_seleksi  = $_POST['status_seleksi'] ?? 'menunggu';
    $catatan_panitia = trim($_POST['catatan_panitia'] ?? 'Pendaftaran dilakukan langsung melalui loket panitia PMB.');

    if (empty($nama_lengkap) || empty($nik) || empty($no_whatsapp) || empty($jalur_id) || empty($prodi_1)) {
        $error = "Kolom Nama Lengkap, NIK, WhatsApp, Jalur, dan Pilihan Prodi 1 wajib diisi!";
    } else {
        // Cek NIK duplikat
        $cek = $pdo->prepare("SELECT id FROM pendaftar WHERE nik = ?");
        $cek->execute([$nik]);
        if ($cek->fetch()) {
            $error = "NIK tersebut sudah pernah terdaftar di sistem PMB.";
        } else {
            // Upload foto jika ada
            $foto_path = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $new_name = 'foto_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../uploads/' . $new_name)) {
                        $foto_path = $new_name;
                    }
                }
            }

            // Upload Rapor jika ada
            $rapor_path = null;
            if (isset($_FILES['berkas_rapor']) && $_FILES['berkas_rapor']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['berkas_rapor']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $new_name = 'rapor_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['berkas_rapor']['tmp_name'], __DIR__ . '/../uploads/' . $new_name)) {
                        $rapor_path = $new_name;
                    }
                }
            }

            // Upload KK jika ada
            $kk_path = null;
            if (isset($_FILES['berkas_kk']) && $_FILES['berkas_kk']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['berkas_kk']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
                    $new_name = 'kk_' . time() . '_' . rand(100, 999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['berkas_kk']['tmp_name'], __DIR__ . '/../uploads/' . $new_name)) {
                        $kk_path = $new_name;
                    }
                }
            }

            $no_pendaftaran = generateNoPendaftaran($pdo);

            $stmt_ins = $pdo->prepare("INSERT INTO pendaftar (
                no_pendaftaran, nama_lengkap, nik, jenis_kelamin, tempat_lahir, tanggal_lahir,
                no_whatsapp, email, alamat, asal_sekolah, jurusan_sekolah, tahun_lulus,
                jalur_id, prodi_pilihan_1, prodi_pilihan_2, foto, berkas_rapor, berkas_kk,
                status_seleksi, catatan_panitia
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $res = $stmt_ins->execute([
                $no_pendaftaran, $nama_lengkap, $nik, $jenis_kelamin, $tempat_lahir, $tanggal_lahir,
                $no_whatsapp, $email, $alamat, $asal_sekolah, $jurusan_sekolah, $tahun_lulus,
                $jalur_id, $prodi_1, $prodi_2, $foto_path, $rapor_path, $kk_path,
                $status_seleksi, $catatan_panitia
            ]);

            if ($res) {
                $message = "Calon mahasiswa baru berhasil didaftarkan! Nomor Pendaftaran: <strong>{$no_pendaftaran}</strong>";
            } else {
                $error = "Terjadi kegagalan saat menyimpan data pendaftar.";
            }
        }
    }
}

$site = getSiteSettings($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Pendaftaran Calon Mahasiswa Baru (Internal Panitia) - Politeknik Mulia Rokan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 font-['Inter'] text-slate-800 flex flex-col min-h-screen">

  <!-- Header Admin -->
  <nav class="bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center gap-3">
          <div class="p-1 bg-white rounded-lg shadow-xs">
            <img src="../<?= htmlspecialchars($site['logo_url'] ?? 'assets/images/logo-politeknik.png'); ?>" alt="Logo" class="h-8 w-auto object-contain">
          </div>
          <div>
            <h1 class="text-sm font-black uppercase tracking-wider">PMB Politeknik Mulia Rokan</h1>
            <span class="text-[10px] text-teal-400 font-semibold">Panel Panitia Seleksi PMB</span>
          </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
          <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
            <a href="../super-admin/index.php" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg transition flex items-center gap-1">
              <span>⚡ Panel Super Admin CMS</span>
            </a>
          <?php endif; ?>
          <a href="../index.php" target="_blank" class="text-slate-300 hover:text-white flex items-center gap-1">
            <span>🌐 Web Depan</span>
          </a>
          <span class="text-slate-600">|</span>
          <span class="text-slate-300">Panitia: <strong><?= htmlspecialchars($_SESSION['admin_nama']); ?></strong></span>
          <a href="logout.php" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-bold transition">
            Keluar
          </a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Subnav Menu -->
  <div class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex gap-6 text-xs font-semibold py-3 overflow-x-auto">
      <a href="index.php" class="text-slate-600 hover:text-teal-700 transition">📊 Dashboard Ringkasan</a>
      <a href="pendaftar.php" class="text-slate-600 hover:text-teal-700 transition">👥 Data Seluruh Pendaftar</a>
      <a href="tambah_pendaftar.php" class="text-teal-700 border-b-2 border-teal-700 pb-1">➕ Pendaftaran Peserta Baru</a>
      <a href="export_excel.php" class="text-slate-600 hover:text-teal-700 transition">📥 Export Data (CSV/Excel)</a>
    </div>
  </div>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 space-y-6 flex-1">
    
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-black text-slate-900">Formulir Pendaftaran Peserta (Offline / Manual Panitia)</h2>
        <p class="text-xs text-slate-500">Gunakan formulir ini jika calon mahasiswa mendaftar langsung di kampus atau melalui panitia PMB.</p>
      </div>
      <a href="pendaftar.php" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-xs font-bold transition">
        &larr; Kembali ke Data Pendaftar
      </a>
    </div>

    <?php if (!empty($message)): ?>
      <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
        <?= $message; ?>
        <div class="mt-2">
          <a href="pendaftar.php" class="underline font-bold text-emerald-900">Lihat di tabel pendaftar &rarr;</a>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
        <?= $error; ?>
      </div>
    <?php endif; ?>

    <form action="tambah_pendaftar.php" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 space-y-6">
      
      <!-- Bagian 1: Data Diri -->
      <div>
        <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
          1. Identitas Calon Mahasiswa
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap *</label>
            <input type="text" name="nama_lengkap" required class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">NIK (16 Digit KTP/KK) *</label>
            <input type="text" name="nik" maxlength="16" required class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Kelamin *</label>
            <select name="jenis_kelamin" required class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Aktif *</label>
            <input type="tel" name="no_whatsapp" required placeholder="08xxxxxxxxxx" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email</label>
            <input type="email" name="email" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div class="md:col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Tempat Tinggal Lengkap</label>
            <textarea name="alamat" rows="2" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
          </div>
        </div>
      </div>

      <!-- Bagian 2: Asal Sekolah -->
      <div>
        <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
          2. Asal Pendidikan Menengah
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Asal Sekolah *</label>
            <input type="text" name="asal_sekolah" required placeholder="Contoh: SMAN 1 Bangko" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Jurusan di Sekolah</label>
            <input type="text" name="jurusan_sekolah" placeholder="IPA / IPS / TKJ / Mesin" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tahun Kelulusan *</label>
            <input type="number" name="tahun_lulus" value="<?= date('Y'); ?>" min="2018" max="2027" required class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
        </div>
      </div>

      <!-- Bagian 3: Pilihan Jalur & Program Studi -->
      <div>
        <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
          3. Pilihan Jalur Seleksi & Program Studi
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Jalur Pendaftaran *</label>
            <select name="jalur_id" required class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <?php foreach ($jalur_list as $jl): ?>
                <option value="<?= $jl['id']; ?>"><?= htmlspecialchars($jl['nama_jalur']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Program Studi 1 *</label>
            <select name="prodi_pilihan_1" required class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <?php foreach ($prodi_list as $pr): ?>
                <option value="<?= $pr['id']; ?>"><?= htmlspecialchars($pr['jenjang'] . ' ' . $pr['nama_prodi']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Program Studi 2 (Opsional)</label>
            <select name="prodi_pilihan_2" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <option value="">-- Tidak Memilih Pilihan 2 --</option>
              <?php foreach ($prodi_list as $pr): ?>
                <option value="<?= $pr['id']; ?>"><?= htmlspecialchars($pr['jenjang'] . ' ' . $pr['nama_prodi']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- Bagian 4: Status Keputusan Awal Panitia -->
      <div>
        <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 mb-4">
          4. Verifikasi & Keputusan Panitia
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Status Seleksi Awal</label>
            <select name="status_seleksi" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white font-semibold">
              <option value="menunggu">Menunggu Verifikasi</option>
              <option value="terverifikasi" selected>Terverifikasi Lengkap</option>
              <option value="lulus">Langsung Dinyatakan Lulus</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Panitia untuk Calon Mahasiswa</label>
            <input type="text" name="catatan_panitia" value="Pendaftaran berkas fisik telah diverifikasi lengkap di sekretariat PMB." class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
        <a href="pendaftar.php" class="text-xs font-bold text-slate-500 hover:text-slate-800">
          &larr; Batal
        </a>
        <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow-lg transition">
          Simpan Pendaftar Baru &rarr;
        </button>
      </div>

    </form>

  </main>

</body>
</html>
