<?php
$page_title = "Formulir Pendaftaran Online PMB 2026";
require_once __DIR__ . '/config/database.php';

$error = '';
$success = '';

// Ambil data jalur & prodi
$jalur_list = $pdo->query("SELECT * FROM jalur_pendaftaran WHERE status = 'aktif' ORDER BY id ASC")->fetchAll();
$prodi_list = $pdo->query("SELECT * FROM program_studi ORDER BY id ASC")->fetchAll();

$selected_jalur = isset($_GET['jalur']) ? (int)$_GET['jalur'] : (count($jalur_list) > 0 ? $jalur_list[0]['id'] : 1);
$selected_prodi = isset($_GET['prodi']) ? (int)$_GET['prodi'] : '';

// Proses Submit Form
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
    $jalur_id        = (int)($_POST['jalur_id'] ?? 1);
    $prodi_pilihan_1 = (int)($_POST['prodi_pilihan_1'] ?? 0);
    $prodi_pilihan_2 = !empty($_POST['prodi_pilihan_2']) ? (int)$_POST['prodi_pilihan_2'] : null;

    // Validasi Sederhana
    if (empty($nama_lengkap) || empty($nik) || empty($tempat_lahir) || empty($tanggal_lahir) || empty($no_whatsapp) || empty($email) || empty($asal_sekolah) || empty($prodi_pilihan_1)) {
        $error = 'Harap lengkapi semua kolom biodata wajib bertanda bintang (*).';
    } elseif (strlen($nik) < 16) {
        $error = 'Nomor Induk Kependudukan (NIK) harus terdiri dari 16 digit angka.';
    } else {
        // Cek apakah NIK sudah pernah mendaftar
        $stmt_cek = $pdo->prepare("SELECT id, no_pendaftaran FROM pendaftar WHERE nik = ?");
        $stmt_cek->execute([$nik]);
        $existing = $stmt_cek->fetch();

        if ($existing) {
            $error = "NIK {$nik} sudah terdaftar sebelumnya dengan Nomor Pendaftaran: " . $existing['no_pendaftaran'] . ". Silakan cek status pendaftaran Anda.";
        } else {
            // Handle File Upload
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $foto_name = null;
            $rapor_name = null;
            $kk_name = null;

            // Upload Foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto_name = 'foto_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $foto_name);
            }

            // Upload Rapor
            if (isset($_FILES['berkas_rapor']) && $_FILES['berkas_rapor']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['berkas_rapor']['name'], PATHINFO_EXTENSION);
                $rapor_name = 'rapor_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['berkas_rapor']['tmp_name'], $upload_dir . $rapor_name);
            }

            // Upload KK
            if (isset($_FILES['berkas_kk']) && $_FILES['berkas_kk']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['berkas_kk']['name'], PATHINFO_EXTENSION);
                $kk_name = 'kk_' . time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['berkas_kk']['tmp_name'], $upload_dir . $kk_name);
            }

            // Generate No Pendaftaran
            $no_pendaftaran = generateNoPendaftaran($pdo);

            // Insert ke Database
            $sql = "INSERT INTO pendaftar (
                no_pendaftaran, nama_lengkap, nik, jenis_kelamin, tempat_lahir, tanggal_lahir,
                no_whatsapp, email, alamat, asal_sekolah, jurusan_sekolah, tahun_lulus,
                jalur_id, prodi_pilihan_1, prodi_pilihan_2, foto, berkas_rapor, berkas_kk,
                status_seleksi, catatan_panitia
            ) VALUES (
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?,
                'menunggu', 'Pendaftaran online berhasil diterima. Menunggu verifikasi dokumen oleh panitia.'
            )";

            $stmt_insert = $pdo->prepare($sql);
            $saved = $stmt_insert->execute([
                $no_pendaftaran, $nama_lengkap, $nik, $jenis_kelamin, $tempat_lahir, $tanggal_lahir,
                $no_whatsapp, $email, $alamat, $asal_sekolah, $jurusan_sekolah, $tahun_lulus,
                $jalur_id, $prodi_pilihan_1, $prodi_pilihan_2, $foto_name, $rapor_name, $kk_name
            ]);

            if ($saved) {
                header("Location: cek_status.php?no_pendaftaran=" . urlencode($no_pendaftaran) . "&success=1");
                exit;
            } else {
                $error = 'Terjadi kesalahan sistem saat menyimpan data. Silakan coba kembali.';
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="theme-bg-canvas py-12 flex-1">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Title Card -->
    <div class="bg-white/95 backdrop-blur-md p-6 sm:p-8 rounded-3xl border border-white/40 shadow-xl mb-8">
      <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
        <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
        <span>Formulir PMB TA 2026/2027</span>
      </span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-3">Pendaftaran Calon Mahasiswa Baru</h1>
      <p class="text-slate-600 text-sm mt-1">Lengkapi data diri, asal sekolah, pilihan program studi, serta berkas administrasi di bawah ini dengan benar.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="p-4 mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm flex items-start gap-3">
      <span class="text-lg">⚠️</span>
      <div><?= htmlspecialchars($error); ?></div>
    </div>
    <?php endif; ?>

    <!-- Formulir -->
    <form action="daftar.php" method="POST" enctype="multipart/form-data" class="space-y-8">
      
      <!-- Bagian 1: Jalur & Pilihan Program Studi -->
      <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">1</span>
          Pilihan Jalur Masuk & Program Studi
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Jalur Pendaftaran *</label>
            <select name="jalur_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
              <?php foreach ($jalur_list as $j): ?>
              <option value="<?= $j['id']; ?>" <?= ($selected_jalur == $j['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($j['nama_jalur']); ?> (Biaya: <?= $j['biaya_daftar'] == 0 ? 'GRATIS' : formatRupiah($j['biaya_daftar']); ?>)
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Program Studi Pilihan 1 *</label>
            <select name="prodi_pilihan_1" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
              <option value="">-- Pilih Program Studi Utama --</option>
              <?php foreach ($prodi_list as $p): ?>
              <option value="<?= $p['id']; ?>" <?= ($selected_prodi == $p['id']) ? 'selected' : ''; ?>>
                <?= htmlspecialchars($p['jenjang']); ?> - <?= htmlspecialchars($p['nama_prodi']); ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Program Studi Pilihan 2 (Opsional)</label>
            <select name="prodi_pilihan_2" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
              <option value="">-- Pilih Program Studi Cadangan --</option>
              <?php foreach ($prodi_list as $p): ?>
              <option value="<?= $p['id']; ?>">
                <?= htmlspecialchars($p['jenjang']); ?> - <?= htmlspecialchars($p['nama_prodi']); ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </div>

      <!-- Bagian 2: Biodata Diri -->
      <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">2</span>
          Biodata Calon Mahasiswa
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Lengkap (Sesuai Ijazah) *</label>
            <input type="text" name="nama_lengkap" required placeholder="Contoh: Muhammad Rian Pratama" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">NIK (Nomor Induk Kependudukan) *</label>
            <input type="text" name="nik" required maxlength="16" placeholder="16 digit NIK pada KTP/KK" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Jenis Kelamin *</label>
            <select name="jenis_kelamin" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tempat Lahir *</label>
            <input type="text" name="tempat_lahir" required placeholder="Contoh: Bagansiapiapi" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tanggal Lahir *</label>
            <input type="date" name="tanggal_lahir" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nomor WhatsApp Aktif *</label>
            <input type="tel" name="no_whatsapp" required placeholder="Contoh: 081234567890" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Alamat Email Aktif *</label>
            <input type="email" name="email" required placeholder="Contoh: emailanda@gmail.com" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Alamat Lengkap Tempat Tinggal *</label>
            <textarea name="alamat" rows="3" required placeholder="Nama jalan, RT/RW, Dusun, Desa/Kelurahan, Kecamatan, Kabupaten" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary"></textarea>
          </div>
        </div>
      </div>

      <!-- Bagian 3: Asal Sekolah -->
      <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">3</span>
          Pendidikan Asal Sekolah
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Nama Sekolah Asal (SMA/SMK/MA) *</label>
            <input type="text" name="asal_sekolah" required placeholder="Contoh: SMAN 1 Bangko / SMKN 1 Tanah Putih" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Tahun Lulus *</label>
            <input type="number" name="tahun_lulus" min="2020" max="2026" value="<?= date('Y'); ?>" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>

          <div class="sm:col-span-3">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Jurusan di Sekolah</label>
            <input type="text" name="jurusan_sekolah" placeholder="Contoh: IPA, IPS, Teknik Mesin, TKJ, Akuntansi, dll." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          </div>
        </div>
      </div>

      <!-- Bagian 4: Upload Berkas Dokumen -->
      <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center gap-2">
          <span class="w-7 h-7 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center">4</span>
          Upload Berkas Dokumen Persyaratan
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Pas Foto Formal (3x4)</label>
            <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary/20">
            <span class="text-[10px] text-slate-400 mt-1 block">Format: JPG/PNG, maks 2MB</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Scan Rapor / Ijazah</label>
            <input type="file" name="berkas_rapor" accept=".pdf,image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary/20">
            <span class="text-[10px] text-slate-400 mt-1 block">Rapor sem 1-5 atau SKL/Ijazah</span>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Scan Kartu Keluarga (KK)</label>
            <input type="file" name="berkas_kk" accept=".pdf,image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary/20">
            <span class="text-[10px] text-slate-400 mt-1 block">Format: PDF/JPG, maks 2MB</span>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
        <p class="text-xs text-slate-500">
          Dengan menekan tombol kirim, saya menyatakan bahwa data yang saya isikan adalah benar dan dapat dipertanggungjawabkan.
        </p>
        <button type="submit" class="brand-gear-btn w-full sm:w-auto px-8 py-3.5 font-black text-sm rounded-xl shadow-lg transition transform hover:-translate-y-0.5 whitespace-nowrap">
          Kirim Pendaftaran Sekarang &rarr;
        </button>
      </div>

    </form>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
