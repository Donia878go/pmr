<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$message = '';
$error = '';

// Proses Update Status Seleksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = (int)$_POST['id'];
    $status_seleksi = $_POST['status_seleksi'];
    $catatan_panitia = trim($_POST['catatan_panitia'] ?? '');

    $stmt_up = $pdo->prepare("UPDATE pendaftar SET status_seleksi = ?, catatan_panitia = ? WHERE id = ?");
    if ($stmt_up->execute([$status_seleksi, $catatan_panitia, $id])) {
        $message = "Status pendaftar berhasil diperbarui!";
    } else {
        $error = "Gagal memperbarui status pendaftar.";
    }
}

// Proses Hapus Pendaftar
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt_del = $pdo->prepare("DELETE FROM pendaftar WHERE id = ?");
    if ($stmt_del->execute([$del_id])) {
        $message = "Data pendaftar berhasil dihapus.";
    }
}

// Filter & Pencarian
$q = trim($_GET['q'] ?? '');
$filter_status = trim($_GET['status'] ?? '');

$sql = "SELECT p.*, j.nama_jalur, p1.nama_prodi as prodi1_nama, p1.jenjang as prodi1_jenjang, p2.nama_prodi as prodi2_nama 
        FROM pendaftar p 
        LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
        LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
        LEFT JOIN program_studi p2 ON p.prodi_pilihan_2 = p2.id 
        WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (p.nama_lengkap LIKE ? OR p.no_pendaftaran LIKE ? OR p.nik LIKE ? OR p.asal_sekolah LIKE ?)";
    $like = "%{$q}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($filter_status)) {
    $sql .= " AND p.status_seleksi = ?";
    $params[] = $filter_status;
}

$sql .= " ORDER BY p.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$pendaftar_list = $stmt->fetchAll();

// Detail jika klik edit/verifikasi
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt_edit = $pdo->prepare("SELECT p.*, j.nama_jalur, p1.nama_prodi as prodi1_nama, p1.jenjang as prodi1_jenjang, p2.nama_prodi as prodi2_nama 
                                FROM pendaftar p 
                                LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
                                LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
                                LEFT JOIN program_studi p2 ON p.prodi_pilihan_2 = p2.id 
                                WHERE p.id = ?");
    $stmt_edit->execute([$edit_id]);
    $edit_data = $stmt_edit->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Data Pendaftar - PMB Politeknik Mulia Rokan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 font-['Inter'] text-slate-800">

  <?php if (!empty($_SESSION['impersonate_mode'])): ?>
  <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-amber-700 text-white px-4 py-2.5 text-xs font-bold flex items-center justify-between shadow-lg sticky top-0 z-50">
    <div class="flex items-center gap-2">
      <span class="p-1 bg-white/20 rounded-md">🎭</span>
      <span>Mode Impersonasi Aktif: Anda sedang mengakses sebagai <strong><?= htmlspecialchars($_SESSION['admin_nama'] ?? '') ?></strong> (<?= htmlspecialchars($_SESSION['admin_role'] ?? '') ?>).</span>
    </div>
    <a href="../super-admin/stop_impersonate.php" class="px-3 py-1.5 bg-white text-slate-900 rounded-lg hover:bg-amber-100 transition shadow text-xs font-black">
      &larr; Kembali ke Akun Super Admin
    </a>
  </div>
  <?php endif; ?>

  <!-- Navbar Admin -->
  <nav class="bg-slate-900 text-white border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex items-center gap-3">
          <div class="p-1 bg-white rounded-lg shadow-xs">
            <img src="../assets/images/logo-politeknik.png" alt="Logo" class="h-8 w-auto">
          </div>
          <div>
            <h1 class="text-sm font-black uppercase tracking-wider">PMB Mulia Rokan</h1>
            <span class="text-[10px] text-slate-400">Panel Manajemen Panitia</span>
          </div>
        </div>

        <div class="flex items-center gap-4 text-xs">
          <?php if ($_SESSION['admin_role'] === 'superadmin'): ?>
            <a href="../super-admin/index.php" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold rounded-lg transition flex items-center gap-1">
              <span>⚡ Panel Super Admin CMS</span>
            </a>
          <?php endif; ?>
          <a href="../index.php" target="_blank" class="text-slate-300 hover:text-white flex items-center gap-1">
            <span>🌐 Lihat Web Depan</span>
          </a>
          <span class="text-slate-600">|</span>
          <span class="text-slate-300">Halo, <strong><?= htmlspecialchars($_SESSION['admin_nama']); ?></strong></span>
          <a href="logout.php" class="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded font-bold transition">Keluar</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Subnav Menu -->
  <div class="bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex gap-6 text-xs font-semibold py-3 overflow-x-auto">
      <a href="index.php" class="text-slate-600 hover:text-primary transition">📊 Dashboard Ringkasan</a>
      <a href="pendaftar.php" class="text-primary border-b-2 border-primary pb-1">👥 Data Seluruh Pendaftar</a>
      <a href="tambah_pendaftar.php" class="text-slate-600 hover:text-primary transition">➕ Daftarkan Peserta Baru</a>
      <a href="export_excel.php" class="text-slate-600 hover:text-primary transition">📥 Export Data (CSV/Excel)</a>
    </div>
  </div>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    
    <?php if (!empty($message)): ?>
    <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs rounded-xl flex items-center justify-between">
      <span><?= htmlspecialchars($message); ?></span>
      <a href="pendaftar.php" class="font-bold underline">Tutup</a>
    </div>
    <?php endif; ?>

    <!-- Modal/Form Verifikasi jika Edit -->
    <?php if ($edit_data): ?>
    <div class="bg-white p-6 sm:p-8 rounded-2xl border-2 border-primary shadow-xl">
      <div class="flex justify-between items-center border-b border-slate-200 pb-4 mb-6">
        <div>
          <span class="text-xs font-bold text-primary uppercase">Verifikasi Dokumen & Hasil Seleksi</span>
          <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($edit_data['nama_lengkap']); ?> (<?= htmlspecialchars($edit_data['no_pendaftaran']); ?>)</h2>
        </div>
        <a href="pendaftar.php" class="text-xs text-slate-500 hover:text-slate-900 font-bold">&times; Tutup Verifikasi</a>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
        <!-- Rincian Biodata -->
        <div class="space-y-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
          <h4 class="font-bold text-slate-800 uppercase tracking-wider mb-2">Informasi Lengkap Pendaftar:</h4>
          <div><strong>NIK:</strong> <?= htmlspecialchars($edit_data['nik']); ?></div>
          <div><strong>WhatsApp:</strong> <a href="https://wa.me/<?= preg_replace('/^0/', '62', $edit_data['no_whatsapp']); ?>" target="_blank" class="text-emerald-600 font-bold hover:underline">💬 <?= htmlspecialchars($edit_data['no_whatsapp']); ?></a></div>
          <div><strong>Email:</strong> <?= htmlspecialchars($edit_data['email']); ?></div>
          <div><strong>Asal Sekolah:</strong> <?= htmlspecialchars($edit_data['asal_sekolah']); ?> (Jurusan: <?= htmlspecialchars($edit_data['jurusan_sekolah']); ?>, Lulus <?= $edit_data['tahun_lulus']; ?>)</div>
          <div><strong>Jalur Masuk:</strong> <?= htmlspecialchars($edit_data['nama_jalur']); ?></div>
          <div><strong>Prodi Pilihan 1:</strong> <span class="font-bold text-primary"><?= htmlspecialchars($edit_data['prodi1_jenjang']); ?> - <?= htmlspecialchars($edit_data['prodi1_nama']); ?></span></div>
          <div><strong>Prodi Pilihan 2:</strong> <?= !empty($edit_data['prodi2_nama']) ? htmlspecialchars($edit_data['prodi2_nama']) : '-'; ?></div>
          <div><strong>Alamat:</strong> <?= htmlspecialchars($edit_data['alamat']); ?></div>
        </div>

        <!-- Berkas Upload -->
        <div class="space-y-3 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
          <h4 class="font-bold text-slate-800 uppercase tracking-wider mb-2">Berkas Dokumen yang Diupload:</h4>
          <div class="flex items-center justify-between py-1 border-b border-slate-200">
            <span>Pas Foto:</span>
            <?php if (!empty($edit_data['foto'])): ?>
              <a href="../uploads/<?= htmlspecialchars($edit_data['foto']); ?>" target="_blank" class="text-primary font-bold hover:underline">Lihat Foto</a>
            <?php else: ?>
              <span class="text-slate-400">Tidak ada</span>
            <?php endif; ?>
          </div>
          <div class="flex items-center justify-between py-1 border-b border-slate-200">
            <span>Scan Rapor/Ijazah:</span>
            <?php if (!empty($edit_data['berkas_rapor'])): ?>
              <a href="../uploads/<?= htmlspecialchars($edit_data['berkas_rapor']); ?>" target="_blank" class="text-primary font-bold hover:underline">Lihat Rapor</a>
            <?php else: ?>
              <span class="text-slate-400">Tidak ada</span>
            <?php endif; ?>
          </div>
          <div class="flex items-center justify-between py-1 border-b border-slate-200">
            <span>Scan Kartu Keluarga (KK):</span>
            <?php if (!empty($edit_data['berkas_kk'])): ?>
              <a href="../uploads/<?= htmlspecialchars($edit_data['berkas_kk']); ?>" target="_blank" class="text-primary font-bold hover:underline">Lihat KK</a>
            <?php else: ?>
              <span class="text-slate-400">Tidak ada</span>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Form Update Status -->
      <form action="pendaftar.php" method="POST" class="bg-primary-light/50 p-5 rounded-xl border border-primary/30 space-y-4">
        <input type="hidden" name="action" value="update_status">
        <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keputusan Seleksi *</label>
            <select name="status_seleksi" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary bg-white">
              <option value="menunggu" <?= $edit_data['status_seleksi'] === 'menunggu' ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
              <option value="terverifikasi" <?= $edit_data['status_seleksi'] === 'terverifikasi' ? 'selected' : ''; ?>>Berkas Terverifikasi</option>
              <option value="lulus" <?= $edit_data['status_seleksi'] === 'lulus' ? 'selected' : ''; ?>>Lulus Seleksi</option>
              <option value="tidak_lulus" <?= $edit_data['status_seleksi'] === 'tidak_lulus' ? 'selected' : ''; ?>>Tidak Lulus</option>
            </select>
          </div>

          <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Panitia untuk Mahasiswa</label>
            <input type="text" name="catatan_panitia" value="<?= htmlspecialchars($edit_data['catatan_panitia'] ?? ''); ?>" placeholder="Contoh: Berkas lengkap, selamat dinyatakan lulus seleksi beasiswa." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary bg-white">
          </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
          <a href="pendaftar.php" class="px-4 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-300 rounded-lg">Batal</a>
          <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-primary hover:bg-primary-dark rounded-lg shadow">
            Simpan Perubahan Status
          </button>
        </div>
      </form>
    </div>
    <?php endif; ?>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <form action="pendaftar.php" method="GET" class="flex flex-wrap items-center gap-2">
          <input type="text" name="q" value="<?= htmlspecialchars($q); ?>" placeholder="Cari nama, NIK, no pendaftaran..." class="px-3 py-2 text-xs border border-slate-300 rounded-lg w-full sm:w-56 focus:ring-primary">
          
          <select name="status" class="px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-primary">
            <option value="">Semua Status</option>
            <option value="menunggu" <?= $filter_status === 'menunggu' ? 'selected' : ''; ?>>Menunggu</option>
            <option value="terverifikasi" <?= $filter_status === 'terverifikasi' ? 'selected' : ''; ?>>Terverifikasi</option>
            <option value="lulus" <?= $filter_status === 'lulus' ? 'selected' : ''; ?>>Lulus</option>
            <option value="tidak_lulus" <?= $filter_status === 'tidak_lulus' ? 'selected' : ''; ?>>Tidak Lulus</option>
          </select>

          <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg">
            Filter
          </button>
          <?php if (!empty($q) || !empty($filter_status)): ?>
            <a href="pendaftar.php" class="text-xs text-rose-600 hover:underline">Reset</a>
          <?php endif; ?>
        </form>

        <a href="tambah_pendaftar.php" class="px-4 py-2 bg-teal-800 hover:bg-teal-900 text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
          <span>➕ Daftarkan Calon Baru</span>
        </a>
      </div>

      <div class="text-xs text-slate-500 w-full sm:w-auto text-right">
        Total: <strong><?= count($pendaftar_list); ?></strong> pendaftar
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-xs text-left">
          <thead class="bg-slate-50 text-slate-600 uppercase border-b border-slate-200 font-bold">
            <tr>
              <th class="py-3 px-4">No. Registrasi</th>
              <th class="py-3 px-4">Nama Lengkap & NIK</th>
              <th class="py-3 px-4">Kontak (WA)</th>
              <th class="py-3 px-4">Jalur & Prodi</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4">Tgl Daftar</th>
              <th class="py-3 px-4 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if (empty($pendaftar_list)): ?>
            <tr>
              <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data pendaftar yang sesuai kriteria.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($pendaftar_list as $row): ?>
            <tr class="hover:bg-slate-50/70 transition">
              <td class="py-3 px-4 font-mono font-bold text-primary">
                <?= htmlspecialchars($row['no_pendaftaran']); ?>
              </td>
              <td class="py-3 px-4">
                <div class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                <div class="text-[11px] text-slate-500">NIK: <?= htmlspecialchars($row['nik']); ?> (<?= $row['jenis_kelamin'] === 'L' ? 'L' : 'P'; ?>)</div>
                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($row['asal_sekolah']); ?></div>
              </td>
              <td class="py-3 px-4">
                <a href="https://wa.me/<?= preg_replace('/^0/', '62', $row['no_whatsapp']); ?>" target="_blank" class="inline-flex items-center gap-1 text-emerald-600 font-semibold hover:underline">
                  <span>📱 <?= htmlspecialchars($row['no_whatsapp']); ?></span>
                </a>
              </td>
              <td class="py-3 px-4">
                <span class="text-[10px] font-bold text-slate-500 uppercase block"><?= htmlspecialchars($row['nama_jalur']); ?></span>
                <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['prodi1_nama']); ?></span>
              </td>
              <td class="py-3 px-4">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                  <?= $row['status_seleksi'] === 'lulus' ? 'bg-emerald-100 text-emerald-800' : 
                     ($row['status_seleksi'] === 'terverifikasi' ? 'bg-blue-100 text-blue-800' : 
                     ($row['status_seleksi'] === 'tidak_lulus' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')); ?>">
                  <?= htmlspecialchars($row['status_seleksi']); ?>
                </span>
              </td>
              <td class="py-3 px-4 text-slate-500">
                <?= substr($row['created_at'], 0, 10); ?>
              </td>
              <td class="py-3 px-4 text-center">
                <div class="inline-flex items-center gap-1">
                  <a href="pendaftar.php?edit=<?= $row['id']; ?>" class="px-2.5 py-1 bg-primary text-white rounded font-bold hover:bg-primary-dark transition text-[11px]">
                    Verifikasi
                  </a>
                  <a href="../cetak_kartu.php?no=<?= urlencode($row['no_pendaftaran']); ?>" target="_blank" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded transition text-[11px]" title="Cetak Kartu">
                    🖨️
                  </a>
                  <a href="pendaftar.php?delete=<?= $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data pendaftar ini?')" class="px-2 py-1 bg-red-50 hover:bg-red-100 text-red-600 rounded transition text-[11px]" title="Hapus">
                    🗑️
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

</body>
</html>
