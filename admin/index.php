<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Statistik PMB
$total_pendaftar = $pdo->query("SELECT COUNT(*) FROM pendaftar")->fetchColumn();
$total_menunggu = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_seleksi = 'menunggu'")->fetchColumn();
$total_terverifikasi = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_seleksi = 'terverifikasi'")->fetchColumn();
$total_lulus = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_seleksi = 'lulus'")->fetchColumn();

// Pendaftar Terbaru
$stmt_recent = $pdo->query("SELECT p.*, j.nama_jalur, p1.nama_prodi 
                            FROM pendaftar p 
                            LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
                            LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
                            ORDER BY p.id DESC LIMIT 5");
$recent_pendaftar = $stmt_recent->fetchAll();

// Pendaftar per Prodi
$stmt_prodi_stat = $pdo->query("SELECT p1.nama_prodi, p1.jenjang, COUNT(p.id) as total 
                                FROM program_studi p1 
                                LEFT JOIN pendaftar p ON p1.id = p.prodi_pilihan_1 
                                GROUP BY p1.id");
$prodi_stats = $stmt_prodi_stat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin PMB - Politeknik Mulia Rokan</title>
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
              <span>⚡ Panel Super Admin (CMS Depan)</span>
            </a>
          <?php endif; ?>
          <a href="../index.php" target="_blank" class="text-slate-300 hover:text-white flex items-center gap-1">
            <span>🌐 Lihat Web Depan</span>
          </a>
          <span class="text-slate-600">|</span>
          <span class="text-slate-300">Halo, <strong><?= htmlspecialchars($_SESSION['admin_nama']); ?></strong> (<?= htmlspecialchars($_SESSION['admin_role']); ?>)</span>
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
      <a href="index.php" class="text-primary border-b-2 border-primary pb-1">📊 Dashboard Ringkasan</a>
      <a href="pendaftar.php" class="text-slate-600 hover:text-primary transition">👥 Data Seluruh Pendaftar</a>
      <a href="tambah_pendaftar.php" class="text-slate-600 hover:text-primary transition">➕ Daftarkan Peserta Baru</a>
      <a href="export_excel.php" class="text-slate-600 hover:text-primary transition">📥 Export Data (CSV/Excel)</a>
    </div>
  </div>

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Top Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-slate-500 font-semibold block uppercase">Total Pendaftar</span>
          <span class="text-3xl font-black text-slate-900"><?= $total_pendaftar; ?></span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl font-bold">
          📁
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-slate-500 font-semibold block uppercase">Menunggu Verifikasi</span>
          <span class="text-3xl font-black text-amber-600"><?= $total_menunggu; ?></span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
          ⏳
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-slate-500 font-semibold block uppercase">Terverifikasi</span>
          <span class="text-3xl font-black text-blue-600"><?= $total_terverifikasi; ?></span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
          ✔️
        </div>
      </div>

      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
        <div>
          <span class="text-xs text-slate-500 font-semibold block uppercase">Lulus Seleksi</span>
          <span class="text-3xl font-black text-emerald-600"><?= $total_lulus; ?></span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
          🎉
        </div>
      </div>

    </div>

    <!-- Pendaftar per Prodi & Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      
      <!-- Distribusi Prodi -->
      <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Sebaran Pilihan Prodi Utama</h3>
        <div class="space-y-3">
          <?php foreach ($prodi_stats as $stat): 
            $pct = $total_pendaftar > 0 ? round(($stat['total'] / $total_pendaftar) * 100) : 0;
          ?>
          <div>
            <div class="flex justify-between text-xs mb-1">
              <span class="font-medium text-slate-700"><?= htmlspecialchars($stat['jenjang']); ?> - <?= htmlspecialchars($stat['nama_prodi']); ?></span>
              <span class="font-bold text-primary"><?= $stat['total']; ?> (<?= $pct; ?>%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
              <div class="bg-primary h-2 rounded-full" style="width: <?= $pct; ?>%"></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex justify-between items-center">
          <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Pendaftar Terbaru Masuk</h3>
          <a href="pendaftar.php" class="text-xs font-bold text-primary hover:underline">Kelola Semua Data &rarr;</a>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-xs text-left">
            <thead class="bg-slate-50 text-slate-500 uppercase border-y border-slate-200">
              <tr>
                <th class="py-2.5 px-3">No. Daftar</th>
                <th class="py-2.5 px-3">Nama Lengkap</th>
                <th class="py-2.5 px-3">Prodi Pilihan</th>
                <th class="py-2.5 px-3">Status</th>
                <th class="py-2.5 px-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              <?php foreach ($recent_pendaftar as $p): ?>
              <tr>
                <td class="py-2.5 px-3 font-mono font-bold text-primary"><?= htmlspecialchars($p['no_pendaftaran']); ?></td>
                <td class="py-2.5 px-3 font-semibold text-slate-900"><?= htmlspecialchars($p['nama_lengkap']); ?></td>
                <td class="py-2.5 px-3 text-slate-600"><?= htmlspecialchars($p['nama_prodi']); ?></td>
                <td class="py-2.5 px-3">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                    <?= $p['status_seleksi'] === 'lulus' ? 'bg-emerald-100 text-emerald-800' : 
                       ($p['status_seleksi'] === 'terverifikasi' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'); ?>">
                    <?= htmlspecialchars($p['status_seleksi']); ?>
                  </span>
                </td>
                <td class="py-2.5 px-3 text-right">
                  <a href="pendaftar.php?edit=<?= $p['id']; ?>" class="px-2.5 py-1 bg-slate-100 hover:bg-primary hover:text-white rounded text-[11px] font-bold transition">
                    Verifikasi
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>

  </main>

</body>
</html>
