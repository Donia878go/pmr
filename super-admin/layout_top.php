<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';
$site = getSiteSettings($pdo);
$curr_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - Super Admin' : 'Super Admin Portal - Politeknik Mulia Rokan'; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body class="bg-slate-100 text-slate-800 flex min-h-screen">

  <!-- SIDEBAR NAVIGATION -->
  <aside class="w-72 bg-slate-950 text-slate-300 flex flex-col shrink-0 border-r border-slate-800 min-h-screen">
    
    <!-- Header Brand with Circular White Background for Both Logos -->
    <div class="p-4 border-b border-slate-800 flex items-center gap-3 bg-slate-900/60">
      <div class="flex items-center gap-1.5 shrink-0">
        <div class="w-8 h-8 rounded-full bg-white p-0.5 shadow-sm border border-slate-200 flex items-center justify-center overflow-hidden">
          <img src="../<?= htmlspecialchars($site['logo_yayasan_url'] ?? 'assets/images/logo-yayasan.png'); ?>" alt="Yayasan" class="w-full h-full object-contain" title="Yayasan Rokan Jaya Bermarwah">
        </div>
        <div class="w-8 h-8 rounded-full bg-white p-0.5 shadow-sm border border-slate-200 flex items-center justify-center overflow-hidden">
          <img src="../<?= htmlspecialchars($site['logo_url'] ?? 'assets/images/logo-politeknik.png'); ?>" alt="Politeknik" class="w-full h-full object-contain" title="Politeknik Mulia Rokan">
        </div>
      </div>
      <div class="overflow-hidden">
        <span class="text-[9px] font-black text-[#e9b35d] uppercase tracking-wider block truncate">YAYASAN ROKAN JAYA BERMARWAH</span>
        <h2 class="text-xs font-black text-white uppercase tracking-wider truncate">SUPER ADMIN PMR</h2>
      </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto text-xs font-semibold">
      
      <!-- Ringkasan -->
      <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition <?= $curr_page === 'index.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-chart-pie w-4 text-center text-teal-400"></i>
        <span>Dashboard Super Admin</span>
      </a>

      <!-- 1. Arsitektur Keamanan & RBAC -->
      <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 px-3 pt-3 pb-1 flex items-center justify-between">
        <span>1. Keamanan & RBAC</span>
        <span class="text-[9px] bg-red-950 text-red-300 px-1.5 py-0.2 rounded border border-red-800">Security</span>
      </div>
      <a href="kelola_user.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_user.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-users-gear w-4 text-center text-rose-400"></i>
        <span>User Central & Impersonate</span>
      </a>
      <a href="rbac_matrix.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'rbac_matrix.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-shield-halved w-4 text-center text-amber-400"></i>
        <span>Role & Permission Matrix</span>
      </a>

      <!-- 2. Frontend Controller -->
      <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 px-3 pt-3 pb-1 flex items-center justify-between">
        <span>2. Layout & Frontend</span>
        <span class="text-[9px] bg-teal-950 text-teal-300 px-1.5 py-0.2 rounded border border-teal-800">Controller</span>
      </div>
      <a href="section_manager.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'section_manager.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-layer-group w-4 text-center text-teal-400"></i>
        <span>Dynamic Section Manager</span>
      </a>
      <a href="pengaturan_web.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'pengaturan_web.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-palette w-4 text-center text-cyan-400"></i>
        <span>Logo, Brand & Foto Kampus</span>
      </a>
      <a href="editor_tampilan.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'editor_tampilan.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-pen-to-square w-4 text-center text-emerald-400"></i>
        <span>Editor Teks & Hero Banner</span>
      </a>
      <a href="menu_builder.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'menu_builder.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-bars-staggered w-4 text-center text-indigo-400"></i>
        <span>Menu & Multi Dropdown</span>
      </a>
      <a href="form_builder.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'form_builder.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-list-check w-4 text-center text-purple-400"></i>
        <span>Dynamic Form Builder</span>
      </a>
      <a href="form_responses.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'form_responses.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-inbox w-4 text-center text-purple-300"></i>
        <span>Respon Formulir Masuk</span>
      </a>
      <a href="kelola_popup.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_popup.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-bullhorn w-4 text-center text-amber-400"></i>
        <span>Pop-up Promo & Banner</span>
      </a>
      <a href="kelola_fitur.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_fitur.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-wand-magic-sparkles w-4 text-center text-yellow-400"></i>
        <span>Fitur Baru Web Utama</span>
      </a>
      <a href="kelola_video.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_video.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-play w-4 text-center text-red-500"></i>
        <span>Galeri Video & YouTube</span>
      </a>
      <a href="kelola_audio.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_audio.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-music w-4 text-center text-pink-400"></i>
        <span>Audio Player & Mars Kampus</span>
      </a>
      <a href="kelola_sosmed.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_sosmed.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-share-nodes w-4 text-center text-sky-400"></i>
        <span>Logo Sosial Media</span>
      </a>

      <!-- 3. CMS & Master Data -->
      <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 px-3 pt-3 pb-1 flex items-center justify-between">
        <span>3. CMS & Master Data</span>
        <span class="text-[9px] bg-blue-950 text-blue-300 px-1.5 py-0.2 rounded border border-blue-800">CMS</span>
      </div>
      <a href="master_akademik.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'master_akademik.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-graduation-cap w-4 text-center text-blue-400"></i>
        <span>Fakultas, Prodi & Dosen</span>
      </a>
      <a href="media_library.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'media_library.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-photo-film w-4 text-center text-emerald-400"></i>
        <span>Central Media Library</span>
      </a>
      <a href="kelola_pengumuman.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_pengumuman.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-newspaper w-4 text-center text-amber-400"></i>
        <span>Publikasi Berita & Pengumuman</span>
      </a>
      <a href="kelola_fasilitas.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_fasilitas.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-building-columns w-4 text-center text-teal-400"></i>
        <span>Fasilitas & Lab Kampus</span>
      </a>
      <a href="kelola_testimoni.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_testimoni.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-handshake-angle w-4 text-center text-yellow-500"></i>
        <span>Testimoni & Suara Industri</span>
      </a>
      <a href="kelola_jalur_prodi.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'kelola_jalur_prodi.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-route w-4 text-center text-cyan-400"></i>
        <span>Jalur Masuk & Kuota PMB</span>
      </a>

      <!-- 4. Konfigurasi Sistem & Server -->
      <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 px-3 pt-3 pb-1 flex items-center justify-between">
        <span>4. Backend & Server</span>
        <span class="text-[9px] bg-amber-950 text-amber-300 px-1.5 py-0.2 rounded border border-amber-800">System</span>
      </div>
      <a href="integrasi_sistem.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'integrasi_sistem.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-sliders w-4 text-center text-amber-400"></i>
        <span>Script, SEO & Integrasi API</span>
      </a>
      <a href="audit_logs.php" class="flex items-center gap-3 px-3 py-2 rounded-xl transition <?= $curr_page === 'audit_logs.php' ? 'bg-teal-800 text-white font-bold shadow' : 'text-slate-400 hover:text-white hover:bg-slate-900'; ?>">
        <i class="fa-solid fa-clock-rotate-left w-4 text-center text-teal-400"></i>
        <span>Audit Trail (Activity Log)</span>
      </a>

      <div class="text-[10px] uppercase font-bold tracking-wider text-slate-500 px-3 pt-3 pb-1">
        Tautan Akses
      </div>
      <a href="../admin/index.php" class="flex items-center gap-3 px-3 py-2 rounded-xl text-amber-300 hover:bg-slate-900 transition">
        <i class="fa-solid fa-clipboard-user w-4 text-center text-amber-400"></i>
        <span>Panel Panitia PMB &rarr;</span>
      </a>
      <a href="../index.php" target="_blank" class="flex items-center gap-3 px-3 py-2 rounded-xl text-teal-300 hover:bg-slate-900 transition">
        <i class="fa-solid fa-globe w-4 text-center text-teal-400"></i>
        <span>Pratinjau Web Depan &rarr;</span>
      </a>

    </nav>

    <!-- Bottom User Info -->
    <div class="p-4 border-t border-slate-800 bg-slate-900/60 flex items-center justify-between text-xs">
      <div>
        <span class="block font-bold text-white"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Super Admin'); ?></span>
        <span class="text-[10px] text-teal-400 uppercase font-black">Level Super Administrator</span>
      </div>
      <a href="logout.php" title="Keluar" class="p-2 bg-rose-600/20 text-rose-400 hover:bg-rose-600 hover:text-white rounded-lg transition">
        <i class="fa-solid fa-right-from-bracket"></i>
      </a>
    </div>

  </aside>

  <!-- MAIN CONTENT WRAPPER -->
  <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
    
    <!-- Top Bar Header -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <h1 class="text-lg font-black text-slate-900"><?= $page_title ?? 'Super Admin Dashboard'; ?></h1>
        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 uppercase">
          Enterprise Control
        </span>
      </div>

      <div class="flex items-center gap-3 text-xs">
        <a href="../index.php" target="_blank" class="px-3.5 py-1.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold transition flex items-center gap-1.5">
          <i class="fa-solid fa-arrow-up-right-from-square"></i>
          <span>Web Depan</span>
        </a>
        <a href="logout.php" class="px-3.5 py-1.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white font-bold transition">
          Keluar
        </a>
      </div>
    </header>

    <!-- Impersonation Banner if active -->
    <?php if (!empty($_SESSION['impersonate_mode'])): ?>
    <div class="bg-gradient-to-r from-amber-600 to-orange-600 text-white px-6 py-2.5 text-xs font-bold flex items-center justify-between shadow-md">
      <div class="flex items-center gap-2">
        <span class="p-1 bg-white/20 rounded-md">🎭</span>
        <span>Anda sedang menggunakan mode <strong>User Impersonation</strong> sebagai: <?= htmlspecialchars($_SESSION['admin_nama'] ?? '') ?> (<?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?>).</span>
      </div>
      <a href="stop_impersonate.php" class="px-3 py-1 bg-white text-slate-900 rounded-lg hover:bg-amber-100 transition shadow text-xs font-black">
        &larr; Akhiri Mode Impersonasi
      </a>
    </div>
    <?php endif; ?>

    <!-- Page Body -->
    <main class="p-6 sm:p-8 flex-1 space-y-6">
