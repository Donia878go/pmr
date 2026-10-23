<?php
$page_title = "Master Data Akademik (Fakultas, Prodi & Dosen)";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';
$active_tab = $_GET['tab'] ?? 'prodi';

// ---------------- 1. KELOLA FAKULTAS ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_fakultas') {
    $f_id = (int)($_POST['id'] ?? 0);
    $kode = trim($_POST['kode_fakultas'] ?? '');
    $nama = trim($_POST['nama_fakultas'] ?? '');
    $dekan = trim($_POST['dekan'] ?? '');

    if (empty($kode) || empty($nama)) {
        $error = 'Kode dan nama fakultas wajib diisi.';
    } else {
        if ($f_id > 0) {
            $stmt = $pdo->prepare("UPDATE fakultas SET kode_fakultas = ?, nama_fakultas = ?, dekan = ? WHERE id = ?");
            $stmt->execute([$kode, $nama, $dekan, $f_id]);
            logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_FAKULTAS', 'Academic Master', $f_id, "Mengubah fakultas: $nama");
            $message = "Fakultas '$nama' berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO fakultas (kode_fakultas, nama_fakultas, dekan) VALUES (?, ?, ?)");
            $stmt->execute([$kode, $nama, $dekan]);
            $new_id = $pdo->lastInsertId();
            logActivity($pdo, $_SESSION['admin_id'], 'CREATE_FAKULTAS', 'Academic Master', $new_id, "Menambahkan fakultas: $nama");
            $message = "Fakultas '$nama' berhasil ditambahkan.";
        }
    }
}
if (isset($_GET['del_fakultas'])) {
    $del_id = (int)$_GET['del_fakultas'];
    $pdo->prepare("DELETE FROM fakultas WHERE id = ?")->execute([$del_id]);
    logActivity($pdo, $_SESSION['admin_id'], 'DELETE_FAKULTAS', 'Academic Master', $del_id, "Menghapus fakultas ID $del_id");
    $message = "Fakultas berhasil dihapus.";
}

// ---------------- 2. KELOLA PROGRAM STUDI ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_prodi') {
    $p_id = (int)($_POST['id'] ?? 0);
    $kode = trim($_POST['kode_prodi'] ?? '');
    $nama = trim($_POST['nama_prodi'] ?? '');
    $jenjang = in_array($_POST['jenjang'] ?? '', ['D3', 'D4']) ? $_POST['jenjang'] : 'D4';
    $akreditasi = trim($_POST['akreditasi'] ?? 'Baik Sekali');
    $kuota = (int)($_POST['kuota'] ?? 60);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $prospek = trim($_POST['prospek_karir'] ?? '');

    if (empty($kode) || empty($nama)) {
        $error = 'Kode dan nama program studi wajib diisi.';
    } else {
        if ($p_id > 0) {
            $stmt = $pdo->prepare("UPDATE program_studi SET kode_prodi = ?, nama_prodi = ?, jenjang = ?, akreditasi = ?, kuota = ?, deskripsi = ?, prospek_karir = ? WHERE id = ?");
            $stmt->execute([$kode, $nama, $jenjang, $akreditasi, $kuota, $deskripsi, $prospek, $p_id]);
            logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_PRODI', 'Academic Master', $p_id, "Mengubah prodi: $nama");
            $message = "Program Studi '$nama' berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO program_studi (kode_prodi, nama_prodi, jenjang, akreditasi, kuota, deskripsi, prospek_karir) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$kode, $nama, $jenjang, $akreditasi, $kuota, $deskripsi, $prospek]);
            $new_id = $pdo->lastInsertId();
            logActivity($pdo, $_SESSION['admin_id'], 'CREATE_PRODI', 'Academic Master', $new_id, "Menambahkan prodi: $nama");
            $message = "Program Studi '$nama' berhasil ditambahkan.";
        }
    }
}
if (isset($_GET['del_prodi'])) {
    $del_id = (int)$_GET['del_prodi'];
    $pdo->prepare("DELETE FROM program_studi WHERE id = ?")->execute([$del_id]);
    logActivity($pdo, $_SESSION['admin_id'], 'DELETE_PRODI', 'Academic Master', $del_id, "Menghapus prodi ID $del_id");
    $message = "Program Studi berhasil dihapus.";
}

// ---------------- 3. KELOLA DOSEN & STAF ----------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_dosen') {
    $d_id = (int)($_POST['id'] ?? 0);
    $nama = trim($_POST['nama_lengkap'] ?? '');
    $nidn = trim($_POST['nidn'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? 'Dosen Tetap');
    $fakultas_id = (int)($_POST['fakultas_id'] ?? 0) ?: null;
    $prodi_id = (int)($_POST['prodi_id'] ?? 0) ?: null;
    $keahlian = trim($_POST['bidang_keahlian'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['aktif', 'tugas_belajar', 'nonaktif']) ? $_POST['status'] : 'aktif';

    // Handle upload foto
    $foto_path = $_POST['existing_foto'] ?? 'assets/images/direktur.jpg';
    if (!empty($_FILES['foto']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $fname = 'dosen_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/' . $fname;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $dest)) {
                $foto_path = 'assets/images/' . $fname;
            }
        }
    }

    if (empty($nama)) {
        $error = 'Nama lengkap dosen wajib diisi.';
    } else {
        if ($d_id > 0) {
            $stmt = $pdo->prepare("UPDATE dosen_staf SET nama_lengkap = ?, nidn = ?, jabatan = ?, fakultas_id = ?, prodi_id = ?, bidang_keahlian = ?, foto = ?, status = ? WHERE id = ?");
            $stmt->execute([$nama, $nidn, $jabatan, $fakultas_id, $prodi_id, $keahlian, $foto_path, $status, $d_id]);
            logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_DOSEN', 'Academic Master', $d_id, "Mengubah data dosen: $nama");
            $message = "Data Dosen/Staf '$nama' berhasil diperbarui.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO dosen_staf (nama_lengkap, nidn, jabatan, fakultas_id, prodi_id, bidang_keahlian, foto, status, urutan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->execute([$nama, $nidn, $jabatan, $fakultas_id, $prodi_id, $keahlian, $foto_path, $status]);
            $new_id = $pdo->lastInsertId();
            logActivity($pdo, $_SESSION['admin_id'], 'CREATE_DOSEN', 'Academic Master', $new_id, "Menambahkan data dosen: $nama");
            $message = "Dosen/Staf '$nama' berhasil ditambahkan ke direktori.";
        }
    }
}
if (isset($_GET['del_dosen'])) {
    $del_id = (int)$_GET['del_dosen'];
    $pdo->prepare("DELETE FROM dosen_staf WHERE id = ?")->execute([$del_id]);
    logActivity($pdo, $_SESSION['admin_id'], 'DELETE_DOSEN', 'Academic Master', $del_id, "Menghapus dosen ID $del_id");
    $message = "Data Dosen/Staf berhasil dihapus.";
}

// Data Queries
$fakultas_list = $pdo->query("SELECT f.*, (SELECT COUNT(*) FROM dosen_staf WHERE fakultas_id = f.id) as total_dosen FROM fakultas f ORDER BY f.id ASC")->fetchAll();
$prodi_list = $pdo->query("SELECT p.*, (SELECT COUNT(*) FROM pendaftar WHERE prodi_pilihan_1 = p.id) as total_pendaftar FROM program_studi p ORDER BY p.id ASC")->fetchAll();
$dosen_list = $pdo->query("SELECT d.*, f.nama_fakultas, p.nama_prodi 
                           FROM dosen_staf d 
                           LEFT JOIN fakultas f ON d.fakultas_id = f.id 
                           LEFT JOIN program_studi p ON d.prodi_id = p.id 
                           ORDER BY d.id DESC")->fetchAll();
?>

<div class="space-y-6">

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
      <span><?= htmlspecialchars($message); ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
      <span><?= htmlspecialchars($error); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Hero Card -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-graduation-cap"></i>
        <span>Academic Entities & Lecturer Directory</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">Master Data Akademik & Direktori Dosen</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Kelola entitas pendidikan institusi: Daftar Fakultas/Departemen, Program Studi Sarjana Terapan (D4) beserta kuota penerimaan mahasiswa baru, dan Direktori Profil Dosen & Staf Pengajar.
      </p>
    </div>
    <i class="fa-solid fa-building-columns text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <!-- Tabs Navigation -->
  <div class="flex items-center gap-2 border-b border-slate-200 pb-3">
    <a href="master_akademik.php?tab=prodi" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 <?= $active_tab === 'prodi' ? 'bg-teal-800 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'; ?>">
      <i class="fa-solid fa-award"></i>
      <span>Program Studi & Kuota (<?= count($prodi_list); ?>)</span>
    </a>
    <a href="master_akademik.php?tab=fakultas" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 <?= $active_tab === 'fakultas' ? 'bg-teal-800 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'; ?>">
      <i class="fa-solid fa-landmark"></i>
      <span>Fakultas / Departemen (<?= count($fakultas_list); ?>)</span>
    </a>
    <a href="master_akademik.php?tab=dosen" class="px-5 py-2.5 rounded-2xl text-xs font-bold transition flex items-center gap-2 <?= $active_tab === 'dosen' ? 'bg-teal-800 text-white shadow' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'; ?>">
      <i class="fa-solid fa-user-tie"></i>
      <span>Direktori Dosen & Staf (<?= count($dosen_list); ?>)</span>
    </a>
  </div>

  <!-- TAB 1: PROGRAM STUDI -->
  <?php if ($active_tab === 'prodi'): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Form Prodi -->
      <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-plus-circle text-teal-600"></i>
          <span>Tambah / Simpan Prodi</span>
        </h3>
        <form action="master_akademik.php?tab=prodi" method="POST" class="space-y-3">
          <input type="hidden" name="action" value="save_prodi">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kode Prodi *</label>
            <input type="text" name="kode_prodi" required placeholder="Contoh: D4-TRPS" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Program Studi *</label>
            <input type="text" name="nama_prodi" required placeholder="Contoh: Rekayasa Pengolahan Sawit" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 font-semibold">
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Jenjang</label>
              <select name="jenjang" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white font-bold">
                <option value="D4">D4 (Sarjana Terapan)</option>
                <option value="D3">D3 (Ahli Madya)</option>
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Akreditasi</label>
              <input type="text" name="akreditasi" value="Baik Sekali" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
            </div>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kuota Mahasiswa Baru *</label>
            <input type="number" name="kuota" value="60" min="10" max="500" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Prospek Karir Lulusan</label>
            <textarea name="prospek_karir" rows="2" placeholder="Plantation Mill Manager, Automation Engineer..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
          </div>
          <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition">
            Simpan Program Studi
          </button>
        </form>
      </div>

      <!-- List Prodi -->
      <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
          Daftar Program Studi Sarjana Terapan Politeknik Mulia Rokan
        </h3>
        <div class="space-y-3">
          <?php foreach ($prodi_list as $p): ?>
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded bg-amber-100 text-amber-900 text-[10px] font-black uppercase"><?= $p['jenjang']; ?></span>
                  <h4 class="text-xs font-black text-slate-900"><?= htmlspecialchars($p['nama_prodi']); ?></h4>
                  <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Akreditasi <?= htmlspecialchars($p['akreditasi']); ?></span>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                  <span>Kode: <code class="font-bold text-teal-800"><?= htmlspecialchars($p['kode_prodi']); ?></code></span>
                  <span>&bull;</span>
                  <span>Kuota PMB: <strong><?= $p['kuota']; ?> Mahasiswa</strong></span>
                  <span>&bull;</span>
                  <span>Pendaftar Masuk: <strong><?= $p['total_pendaftar']; ?> Orang</strong></span>
                </div>
                <?php if (!empty($p['deskripsi'])): ?>
                  <p class="text-[11px] text-slate-600 mt-1.5"><?= htmlspecialchars($p['deskripsi']); ?></p>
                <?php endif; ?>
              </div>
              <a href="master_akademik.php?tab=prodi&del_prodi=<?= $p['id']; ?>" onclick="return confirm('Hapus program studi ini?')" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition self-end sm:self-center" title="Hapus Prodi">
                <i class="fa-solid fa-trash-can text-xs"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- TAB 2: FAKULTAS -->
  <?php if ($active_tab === 'fakultas'): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-plus-circle text-teal-600"></i>
          <span>Tambah Fakultas Baru</span>
        </h3>
        <form action="master_akademik.php?tab=fakultas" method="POST" class="space-y-3">
          <input type="hidden" name="action" value="save_fakultas">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Kode Fakultas *</label>
            <input type="text" name="kode_fakultas" required placeholder="Contoh: FV-INDUSTRI" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Fakultas *</label>
            <input type="text" name="nama_fakultas" required placeholder="Fakultas Vokasi Rekayasa..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 font-semibold">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Dekan / Pimpinan Fakultas</label>
            <input type="text" name="dekan" placeholder="Dr. Ir. ..., M.T." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition">
            Simpan Fakultas
          </button>
        </form>
      </div>

      <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
          Daftar Fakultas / Departemen
        </h3>
        <div class="space-y-3">
          <?php foreach ($fakultas_list as $f): ?>
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 flex items-center justify-between gap-3">
              <div>
                <div class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded bg-teal-100 text-teal-800 text-[10px] font-mono font-bold"><?= htmlspecialchars($f['kode_fakultas']); ?></span>
                  <h4 class="text-xs font-black text-slate-900"><?= htmlspecialchars($f['nama_fakultas']); ?></h4>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1">
                  <span>Dekan: <strong><?= htmlspecialchars($f['dekan'] ?: '-'); ?></strong></span>
                  <span>&bull;</span>
                  <span>Total Dosen: <?= $f['total_dosen']; ?> Pengajar</span>
                </div>
              </div>
              <a href="master_akademik.php?tab=fakultas&del_fakultas=<?= $f['id']; ?>" onclick="return confirm('Hapus fakultas ini?')" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl transition" title="Hapus Fakultas">
                <i class="fa-solid fa-trash-can text-xs"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- TAB 3: DOSEN & STAF -->
  <?php if ($active_tab === 'dosen'): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
          <i class="fa-solid fa-user-plus text-teal-600"></i>
          <span>Tambah Dosen / Tenaga Pengajar</span>
        </h3>
        <form action="master_akademik.php?tab=dosen" method="POST" enctype="multipart/form-data" class="space-y-3">
          <input type="hidden" name="action" value="save_dosen">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Nama Lengkap & Gelar *</label>
            <input type="text" name="nama_lengkap" required placeholder="Contoh: Dr. Ir. Rudi Hartono, M.T." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 font-semibold">
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">NIDN</label>
              <input type="text" name="nidn" placeholder="1029384756" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Status</label>
              <select name="status" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
                <option value="aktif">Aktif Mengajar</option>
                <option value="tugas_belajar">Tugas Belajar</option>
                <option value="nonaktif">Non-Aktif</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Jabatan Fungsional / Posisi</label>
            <input type="text" name="jabatan" value="Dosen Tetap Vokasi" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Fakultas</label>
            <select name="fakultas_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <option value="">-- Pilih Fakultas --</option>
              <?php foreach ($fakultas_list as $f): ?>
                <option value="<?= $f['id']; ?>"><?= htmlspecialchars($f['nama_fakultas']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Program Studi</label>
            <select name="prodi_id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <option value="">-- Pilih Program Studi --</option>
              <?php foreach ($prodi_list as $p): ?>
                <option value="<?= $p['id']; ?>"><?= htmlspecialchars($p['nama_prodi']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Bidang Keahlian / Riset</label>
            <input type="text" name="bidang_keahlian" placeholder="Otomasi Pabrik Sawit, PLC & SCADA..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Foto Formal Dosen (JPG/PNG)</label>
            <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-800">
          </div>
          <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition">
            Simpan Data Dosen
          </button>
        </form>
      </div>

      <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100">
          Direktori Dosen & Tenaga Pengajar (<?= count($dosen_list); ?>)
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <?php foreach ($dosen_list as $d): ?>
            <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 flex gap-3.5 items-start">
              <img src="../<?= htmlspecialchars($d['foto'] ?: 'assets/images/direktur.jpg'); ?>" alt="Foto" class="w-12 h-12 rounded-xl object-cover shrink-0 border border-slate-300">
              <div class="flex-1 min-w-0">
                <h4 class="text-xs font-black text-slate-900 truncate"><?= htmlspecialchars($d['nama_lengkap']); ?></h4>
                <div class="text-[11px] text-teal-800 font-semibold"><?= htmlspecialchars($d['jabatan']); ?></div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">NIDN: <?= htmlspecialchars($d['nidn'] ?: '-'); ?></div>
                <div class="text-[11px] text-slate-600 mt-1">
                  Prodi: <?= htmlspecialchars($d['nama_prodi'] ?: 'Umum'); ?>
                </div>
                <?php if (!empty($d['bidang_keahlian'])): ?>
                  <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded bg-teal-50 text-teal-800 border border-teal-200 truncate max-w-full">
                    Keahlian: <?= htmlspecialchars($d['bidang_keahlian']); ?>
                  </span>
                <?php endif; ?>
              </div>
              <a href="master_akademik.php?tab=dosen&del_dosen=<?= $d['id']; ?>" onclick="return confirm('Hapus profil dosen ini?')" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                <i class="fa-solid fa-trash-can text-xs"></i>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
