<?php
$page_title = "Pusat Kendali Super Administrator";
require_once __DIR__ . '/layout_top.php';

// Enterprise Metrics
$tot_pendaftar = $pdo->query("SELECT COUNT(*) FROM pendaftar")->fetchColumn();
$tot_forms     = $pdo->query("SELECT COUNT(*) FROM dynamic_forms")->fetchColumn();
$tot_responses = $pdo->query("SELECT COUNT(*) FROM dynamic_form_submissions")->fetchColumn();
$tot_sections  = $pdo->query("SELECT COUNT(*) FROM homepage_sections WHERE is_active = 1")->fetchColumn();
$tot_users     = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
$tot_roles     = $pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
$tot_media     = $pdo->query("SELECT COUNT(*) FROM media_library")->fetchColumn();
$tot_prodi     = $pdo->query("SELECT COUNT(*) FROM program_studi")->fetchColumn();
$tot_dosen     = $pdo->query("SELECT COUNT(*) FROM dosen_staf")->fetchColumn();

// Recent Audit Trail Logs
$stmt_audit = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 6");
$recent_logs = $stmt_audit->fetchAll();

// Pendaftar PMB Terbaru
$stmt_recent_pmb = $pdo->query("SELECT p.*, j.nama_jalur, p1.nama_prodi 
                                FROM pendaftar p 
                                LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
                                LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
                                ORDER BY p.id DESC LIMIT 5");
$recent_pmb = $stmt_recent_pmb->fetchAll();

// Maintenance status
$is_maint = !empty($site['is_maintenance']);
?>

<div class="space-y-8">

  <!-- Welcome Banner -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-teal-800/40 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
    <div class="space-y-2 relative z-10 max-w-2xl">
      <div class="flex items-center gap-2">
        <span class="inline-block px-3 py-0.5 bg-amber-400 text-slate-950 font-black text-[10px] uppercase tracking-wider rounded-full">
          Enterprise Control Suite
        </span>
        <?php if ($is_maint): ?>
          <span class="inline-flex items-center gap-1.5 px-3 py-0.5 bg-rose-600/30 text-rose-300 border border-rose-500/40 rounded-full text-[10px] font-black uppercase">
            <span class="w-2 h-2 rounded-full bg-rose-400 animate-ping"></span>
            Maintenance Mode ON
          </span>
        <?php endif; ?>
      </div>
      <h2 class="text-2xl sm:text-3xl font-black text-white">Selamat Datang, <?= htmlspecialchars($_SESSION['admin_nama']); ?>!</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Sistem kontrol institusi <strong>Politeknik Mulia Rokan</strong> berjalan dalam status optimal. Anda memiliki wewenang penuh atas arsitektur RBAC, tata letak homepage dinamis, media library, dan integrasi backend tanpa intervensi hardcode.
      </p>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-2.5 shrink-0 relative z-10">
      <a href="section_manager.php" class="px-4 py-2.5 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2 border border-teal-600/40">
        <i class="fa-solid fa-layer-group"></i>
        <span>Section Manager</span>
      </a>
      <a href="integrasi_sistem.php" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
        <i class="fa-solid fa-sliders"></i>
        <span>Integrasi & SEO</span>
      </a>
      <a href="../index.php" target="_blank" class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white font-bold text-xs rounded-xl border border-white/20 transition flex items-center justify-center gap-1.5">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        <span>Web Depan</span>
      </a>
    </div>
    <i class="fa-solid fa-building-shield text-9xl text-teal-600/5 absolute -right-6 -bottom-6"></i>
  </div>

  <!-- Key Metrics Row (4 Pillars Overview) -->
  <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    
    <!-- 1. RBAC & Users -->
    <a href="kelola_user.php" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm hover:border-rose-400 hover:shadow-md transition flex items-center justify-between group">
      <div>
        <span class="text-[11px] text-slate-500 font-bold uppercase block tracking-wider">1. Keamanan & RBAC</span>
        <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block"><?= $tot_users; ?> <span class="text-xs text-slate-400 font-normal">User</span></span>
        <span class="text-[11px] text-rose-600 font-bold block mt-1"><?= $tot_roles; ?> Role Dikonfigurasi</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        <i class="fa-solid fa-user-shield"></i>
      </div>
    </a>

    <!-- 2. Dynamic Sections -->
    <a href="section_manager.php" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm hover:border-teal-400 hover:shadow-md transition flex items-center justify-between group">
      <div>
        <span class="text-[11px] text-slate-500 font-bold uppercase block tracking-wider">2. Layout Homepage</span>
        <span class="text-2xl sm:text-3xl font-black text-teal-700 mt-1 block"><?= $tot_sections; ?> <span class="text-xs text-slate-400 font-normal">Seksi</span></span>
        <span class="text-[11px] text-teal-600 font-bold block mt-1">Blok Konten Aktif Tayang</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        <i class="fa-solid fa-cubes-stacked"></i>
      </div>
    </a>

    <!-- 3. Academic & CMS -->
    <a href="master_akademik.php" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm hover:border-blue-400 hover:shadow-md transition flex items-center justify-between group">
      <div>
        <span class="text-[11px] text-slate-500 font-bold uppercase block tracking-wider">3. Master Akademik</span>
        <span class="text-2xl sm:text-3xl font-black text-slate-900 mt-1 block"><?= $tot_prodi; ?> <span class="text-xs text-slate-400 font-normal">Prodi</span></span>
        <span class="text-[11px] text-blue-600 font-bold block mt-1"><?= $tot_dosen; ?> Dosen Terdaftar</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        <i class="fa-solid fa-graduation-cap"></i>
      </div>
    </a>

    <!-- 4. Media Library Assets -->
    <a href="media_library.php" class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200 shadow-sm hover:border-amber-400 hover:shadow-md transition flex items-center justify-between group">
      <div>
        <span class="text-[11px] text-slate-500 font-bold uppercase block tracking-wider">4. Media Library</span>
        <span class="text-2xl sm:text-3xl font-black text-amber-600 mt-1 block"><?= $tot_media; ?> <span class="text-xs text-slate-400 font-normal">Aset</span></span>
        <span class="text-[11px] text-slate-500 font-bold block mt-1">Gambar & Dokumen PDF</span>
      </div>
      <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold group-hover:scale-110 transition">
        <i class="fa-solid fa-photo-film"></i>
      </div>
    </a>

  </div>

  <!-- Four Architectural Pillars Hub (Grid Cards) -->
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <div>
        <h3 class="text-base font-black text-slate-900">4 Pilar Arsitektur Kontrol Institusi</h3>
        <p class="text-xs text-slate-500">Pusat kendali enterprise terpadu sesuai standar tata kelola portal modern.</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- Pilar 1 -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm hover:border-slate-300 transition">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-base">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <div>
            <h4 class="text-xs font-black text-slate-900 uppercase">1. Keamanan & RBAC</h4>
            <span class="text-[10px] text-slate-500">Access Control Matrix</span>
          </div>
        </div>
        <ul class="space-y-2 text-xs font-semibold">
          <li><a href="kelola_user.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-users text-slate-400 text-[10px]"></i> User Central & Suspend</a></li>
          <li><a href="rbac_matrix.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-matrix-org text-slate-400 text-[10px]"></i> Role & Permission Matrix</a></li>
          <li><a href="kelola_user.php" class="text-indigo-600 hover:underline flex items-center gap-2"><i class="fa-solid fa-masks-theater text-[10px]"></i> User Impersonation Mode</a></li>
        </ul>
      </div>

      <!-- Pilar 2 -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm hover:border-slate-300 transition">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center text-base">
            <i class="fa-solid fa-layer-group"></i>
          </div>
          <div>
            <h4 class="text-xs font-black text-slate-900 uppercase">2. Frontend Controller</h4>
            <span class="text-[10px] text-slate-500">Tata Letak Dinamis</span>
          </div>
        </div>
        <ul class="space-y-2 text-xs font-semibold">
          <li><a href="section_manager.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-cubes text-slate-400 text-[10px]"></i> Dynamic Section Manager</a></li>
          <li><a href="form_builder.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-list-check text-slate-400 text-[10px]"></i> Form Builder Generator</a></li>
          <li><a href="kelola_popup.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-bullhorn text-slate-400 text-[10px]"></i> Emergency Modal & Pop-up</a></li>
          <li><a href="menu_builder.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-bars text-slate-400 text-[10px]"></i> Multi Dropdown Navigasi</a></li>
        </ul>
      </div>

      <!-- Pilar 3 -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm hover:border-slate-300 transition">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center text-base">
            <i class="fa-solid fa-graduation-cap"></i>
          </div>
          <div>
            <h4 class="text-xs font-black text-slate-900 uppercase">3. CMS & Master Data</h4>
            <span class="text-[10px] text-slate-500">Entitas Pendidikan</span>
          </div>
        </div>
        <ul class="space-y-2 text-xs font-semibold">
          <li><a href="master_akademik.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-landmark text-slate-400 text-[10px]"></i> Fakultas & Kuota Prodi</a></li>
          <li><a href="master_akademik.php?tab=dosen" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-user-tie text-slate-400 text-[10px]"></i> Direktori Staf & Dosen</a></li>
          <li><a href="media_library.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-images text-slate-400 text-[10px]"></i> Central Media Library</a></li>
          <li><a href="kelola_pengumuman.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-newspaper text-slate-400 text-[10px]"></i> Publikasi & Pengumuman</a></li>
        </ul>
      </div>

      <!-- Pilar 4 -->
      <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm hover:border-slate-300 transition">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-base">
            <i class="fa-solid fa-gears"></i>
          </div>
          <div>
            <h4 class="text-xs font-black text-slate-900 uppercase">4. Server & Backend</h4>
            <span class="text-[10px] text-slate-500">Log & Integrasi API</span>
          </div>
        </div>
        <ul class="space-y-2 text-xs font-semibold">
          <li><a href="audit_logs.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-clock-rotate-left text-slate-400 text-[10px]"></i> Audit Trail Activity Log</a></li>
          <li><a href="integrasi_sistem.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-code text-slate-400 text-[10px]"></i> Head & Body Script Injector</a></li>
          <li><a href="integrasi_sistem.php" class="text-slate-700 hover:text-teal-700 flex items-center gap-2"><i class="fa-solid fa-magnifying-glass text-slate-400 text-[10px]"></i> Dynamic SEO & Meta Tags</a></li>
          <li><a href="integrasi_sistem.php" class="text-rose-600 hover:underline flex items-center gap-2"><i class="fa-solid fa-power-off text-[10px]"></i> Maintenance Mode Switch</a></li>
        </ul>
      </div>

    </div>
  </div>

  <!-- Realtime Activity Stream & Recent Applicants -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Audit Trail Recent Logs -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Audit Trail: Aktivitas Server Terbaru</h3>
          <p class="text-[11px] text-slate-500">Merekam setiap tindakan CRUD secara otomatis.</p>
        </div>
        <a href="audit_logs.php" class="text-xs font-bold text-teal-700 hover:underline flex items-center gap-1">
          <span>Semua Log</span>
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>

      <?php if (empty($recent_logs)): ?>
        <p class="text-xs text-slate-400 py-6 text-center">Belum ada riwayat aktivitas yang tercatat.</p>
      <?php else: ?>
        <div class="divide-y divide-slate-100">
          <?php foreach ($recent_logs as $log): 
            $action = strtoupper($log['action']);
            $badge = 'bg-slate-100 text-slate-700';
            if (str_contains($action, 'CREATE')) $badge = 'bg-emerald-100 text-emerald-800';
            elseif (str_contains($action, 'UPDATE') || str_contains($action, 'TOGGLE')) $badge = 'bg-amber-100 text-amber-800';
            elseif (str_contains($action, 'DELETE')) $badge = 'bg-rose-100 text-rose-800';
            elseif (str_contains($action, 'IMPERSONATE')) $badge = 'bg-indigo-100 text-indigo-800';
          ?>
            <div class="py-3 flex items-start justify-between gap-3 text-xs">
              <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                  <span class="font-bold text-slate-900"><?= htmlspecialchars($log['username'] ?: 'System'); ?></span>
                  <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase <?= $badge; ?>"><?= htmlspecialchars($log['action']); ?></span>
                  <span class="text-[10px] text-teal-700 font-semibold">[<?= htmlspecialchars($log['module']); ?>]</span>
                </div>
                <p class="text-slate-600 text-[11px] mt-0.5 truncate"><?= htmlspecialchars($log['detail']); ?></p>
              </div>
              <div class="text-right shrink-0 font-mono text-[10px] text-slate-400">
                <span><?= date('H:i:s', strtotime($log['created_at'])); ?></span>
                <span class="block text-[9px]"><?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?></span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Pendaftar PMB Terbaru -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div>
          <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Pendaftar PMB Terbaru</h3>
          <p class="text-[11px] text-slate-500">Total <?= $tot_pendaftar; ?> calon pendaftar.</p>
        </div>
        <a href="../admin/pendaftar.php" class="text-xs font-bold text-teal-700 hover:underline flex items-center gap-1">
          <span>Panel PMB</span>
          <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
      </div>

      <?php if (empty($recent_pmb)): ?>
        <p class="text-xs text-slate-400 py-6 text-center">Belum ada data pendaftar.</p>
      <?php else: ?>
        <div class="divide-y divide-slate-100">
          <?php foreach ($recent_pmb as $pmb): ?>
            <div class="py-3 flex justify-between items-center gap-3 text-xs">
              <div class="min-w-0">
                <div class="font-bold text-slate-900 truncate"><?= htmlspecialchars($pmb['nama_lengkap']); ?></div>
                <div class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($pmb['nama_prodi']); ?></div>
              </div>
              <div class="text-right shrink-0">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                  <?= $pmb['status_seleksi'] === 'lulus' ? 'bg-emerald-100 text-emerald-800' : 
                     ($pmb['status_seleksi'] === 'terverifikasi' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800'); ?>">
                  <?= htmlspecialchars($pmb['status_seleksi']); ?>
                </span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
