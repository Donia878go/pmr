<?php
require_once __DIR__ . '/config/database.php';
$site = getSiteSettings($pdo);
$page_title = $site['nama_kampus'] ?? "Politeknik Mulia Rokan";

// Ambil data dinamis dari database
$stmt_jalur = $pdo->query("SELECT * FROM jalur_pendaftaran WHERE status = 'aktif' ORDER BY id ASC");
$jalur_list = $stmt_jalur->fetchAll();

$stmt_prodi = $pdo->query("SELECT * FROM program_studi ORDER BY id ASC");
$prodi_list = $stmt_prodi->fetchAll();

$stmt_pengumuman = $pdo->query("SELECT * FROM pengumuman WHERE status = 'publish' ORDER BY tanggal DESC LIMIT 3");
$pengumuman_list = $stmt_pengumuman->fetchAll();

$stmt_fasilitas = $pdo->query("SELECT * FROM fasilitas_kampus ORDER BY urutan ASC LIMIT 6");
$fasilitas_list = $stmt_fasilitas->fetchAll();

$stmt_testimoni = $pdo->query("SELECT * FROM testimoni_mitra ORDER BY urutan ASC LIMIT 3");
$testimoni_list = $stmt_testimoni->fetchAll();

// Fitur Unggulan & Video Baru
$fitur_list = getFiturUnggulan($pdo);
$video_list = getVideoKampus($pdo);

// Ambil Status Seksi Homepage Dinamis (Frontend Controller)
$sec_rows = $pdo->query("SELECT section_key, is_active FROM homepage_sections")->fetchAll();
$sec_active = [];
foreach ($sec_rows as $sr) {
    $sec_active[$sr['section_key']] = (bool)$sr['is_active'];
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

$logo_img = !empty($site['logo_url']) ? $site['logo_url'] : 'assets/images/logo-politeknik.png';
$logo_yayasan_img = !empty($site['logo_yayasan_url']) ? $site['logo_yayasan_url'] : 'assets/images/logo-yayasan.png';
$hero_bg  = !empty($site['hero_bg_image']) ? $site['hero_bg_image'] : 'assets/images/gedung-kampus.svg';
$hero_bg_opacity = isset($site['hero_bg_opacity']) ? (int)$site['hero_bg_opacity'] : 85;
$overlay_decimal = $hero_bg_opacity / 100;
?>

<!-- MAIN WRAPPER WITH UNIFIED DARK NAVY SOLID BACKGROUND (#0b1120) -->
<div class="theme-bg-canvas text-slate-100 min-h-screen">

  <!-- ==========================================
       1. HERO SECTION (CLEAN DARK NAVY FADE + 2 COLUMNS)
       ========================================== -->
  <?php if ($sec_active['hero'] ?? true): ?>
  <section id="beranda" class="relative overflow-hidden py-20 lg:py-24 border-b border-white/10">
    
    <!-- Background Campus Image with Smooth Dark Navy Overlay -->
    <div class="absolute inset-0 z-0">
      <img src="<?= htmlspecialchars($hero_bg); ?>" alt="Gedung Kampus Politeknik Mulia Rokan" class="w-full h-full object-cover object-center filter brightness-40 contrast-110 scale-105 transition-transform duration-1000">
      <!-- Dark Navy Smooth Fade into Background #0b1120 -->
      <div class="absolute inset-0 bg-gradient-to-b from-[#0b1120]/75 via-[#0b1120]/90 to-[#0b1120]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
      <!-- Clean 2-Column Grid: Text on Left (7 cols), Emblem Card on Right (5 cols) -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
        
        <!-- Left Column: Copywriting & Primary CTAs -->
        <div class="lg:col-span-7 space-y-6">
          <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-[#131c31] text-[#1bb28f] border border-[#1bb28f]/40 text-xs sm:text-sm font-bold shadow-md">
            <span class="w-2.5 h-2.5 rounded-full bg-[#e9b35d] animate-ping"></span>
            <span><?= htmlspecialchars($site['hero_badge'] ?? '✨ Penerimaan Mahasiswa Baru TA 2026/2027 Resmi Dibuka'); ?></span>
          </div>

          <h1 class="text-3xl sm:text-5xl lg:text-5xl font-black text-white tracking-tight leading-[1.18] drop-shadow-md">
            <?= htmlspecialchars($site['hero_title'] ?? 'Membangun Generasi Vokasi Unggul & Siap Kerja di Era Industri Modern'); ?>
          </h1>

          <p class="text-base sm:text-lg text-slate-300 leading-relaxed max-w-2xl font-normal">
            <?= htmlspecialchars($site['hero_subtitle'] ?? 'Politeknik Mulia Rokan menyelenggarakan program Sarjana Terapan (D4) berbasis kurikulum industri sawit, manufaktur, dan software enterprise.'); ?>
          </p>

          <!-- Buttons: Primary Solid Hijau Toska Gerigi + Secondary Outline -->
          <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 pt-2">
            <!-- Tombol Utama (Primary CTA): Hijau Toska Solid Gerigi -->
            <a href="<?= htmlspecialchars($site['hero_cta_link'] ?? 'daftar.php'); ?>" class="brand-gear-btn inline-flex justify-center items-center gap-2 px-8 py-4 text-sm font-black rounded-2xl transform hover:-translate-y-0.5">
              <span><?= htmlspecialchars($site['hero_cta_text'] ?? 'Daftar PMB Sekarang'); ?></span>
              <svg class="w-5 h-5 text-[#e9b35d] group-hover:text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
            </a>
            
            <!-- Tombol Sekunder: Outline Hijau Toska Transparan -->
            <a href="#video-profil" class="inline-flex justify-center items-center gap-2 px-6 py-4 text-sm font-bold text-[#1bb28f] hover:text-[#e9b35d] bg-[#131c31]/80 hover:bg-[#131c31] border border-[#1bb28f]/50 hover:border-[#e9b35d]/80 rounded-2xl shadow-sm backdrop-blur-md transition duration-300">
              <span class="w-6 h-6 rounded-full bg-[#e9b35d] text-slate-950 flex items-center justify-center text-xs font-bold">▶</span>
              <span>Tonton Video Profil</span>
            </a>
          </div>

          <!-- Highlight Stats Cards (Elevated Dark Navy Cards with Min 24px Padding) -->
          <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/10">
            <div class="card-elevated p-6 rounded-2xl shadow-lg">
              <div class="text-2xl sm:text-3xl font-black text-[#1bb28f]">3</div>
              <div class="text-[11px] sm:text-xs text-slate-300 font-semibold mt-1">Prodi Sarjana Terapan (D4)</div>
            </div>
            <div class="card-elevated p-6 rounded-2xl shadow-lg">
              <div class="text-2xl sm:text-3xl font-black text-[#e9b35d]">100+</div>
              <div class="text-[11px] sm:text-xs text-slate-300 font-semibold mt-1">Konsorsium Mitra Sawit</div>
            </div>
            <div class="card-elevated p-6 rounded-2xl shadow-lg">
              <div class="text-2xl sm:text-3xl font-black text-[#1b969d]">100%</div>
              <div class="text-[11px] sm:text-xs text-slate-300 font-semibold mt-1">Beasiswa Prestasi & KIP-K</div>
            </div>
          </div>
        </div>

        <!-- Right Column: Visual Hero Emblem Card (Dual Logos on Crisp White Background) -->
        <div class="lg:col-span-5 relative mt-8 lg:mt-0">
          <div class="relative mx-auto max-w-md lg:max-w-none">
            
            <!-- Card Frame with Campus Emblem -->
            <div class="card-elevated p-8 sm:p-10 rounded-3xl shadow-2xl text-center">
              
              <!-- Official Dual Emblem Display (BULAT DENGAN LATAR PUTIH BERSIH) -->
              <div class="relative mx-auto mb-6 flex items-center justify-center gap-4 sm:gap-6 max-w-sm">
                <!-- Logo Yayasan: Bulat dengan background putih -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white p-3 shadow-xl border-4 border-[#e9b35d]/40 flex items-center justify-center overflow-hidden hover:scale-105 transition-transform">
                  <img src="<?= htmlspecialchars($logo_yayasan_img); ?>" alt="Yayasan Rokan Jaya Bermarwah" class="w-full h-full object-contain filter drop-shadow">
                </div>
                
                <div class="w-1 h-12 bg-white/20 rounded-full"></div>

                <!-- Logo Politeknik: Bulat dengan background putih -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full bg-white p-3 shadow-xl border-4 border-[#1bb28f]/40 flex items-center justify-center overflow-hidden hover:scale-105 transition-transform">
                  <img src="<?= htmlspecialchars($logo_img); ?>" alt="Politeknik Mulia Rokan" class="w-full h-full object-contain filter drop-shadow">
                </div>
              </div>

              <div class="space-y-1.5">
                <span class="inline-block text-[10px] font-black text-[#e9b35d] tracking-wider uppercase">
                  YAYASAN ROKAN JAYA BERMARWAH
                </span>
                <h3 class="text-xl font-black text-white leading-tight">
                  <?= htmlspecialchars($site['nama_kampus'] ?? 'POLITEKNIK MULIA ROKAN'); ?>
                </h3>
                <span class="inline-block px-3 py-0.5 bg-[#0b1120] border border-[#1bb28f]/40 text-[#1bb28f] font-bold text-[10px] uppercase tracking-wider rounded-full">
                  Pendidikan Tinggi Vokasi Terakreditasi
                </span>
                <p class="text-xs text-slate-400 leading-relaxed pt-1">
                  Kampus Mandiri Vokasi Berbasis Teaching Factory Pengolahan Kelapa Sawit Terpadu Pertama di Rokan Hilir.
                </p>
              </div>

              <!-- Quick Highlights Badges -->
              <div class="mt-6 pt-5 border-t border-slate-800 grid grid-cols-2 gap-3 text-left">
                <div class="flex items-center gap-2.5 p-3.5 rounded-2xl bg-[#0b1120] border border-slate-700/50">
                  <span class="text-xl">🏭</span>
                  <div>
                    <span class="block text-[11px] font-bold text-white">Magang Industri</span>
                    <span class="block text-[10px] text-slate-400">Pabrik Sawit Riau</span>
                  </div>
                </div>
                <div class="flex items-center gap-2.5 p-3.5 rounded-2xl bg-[#0b1120] border border-slate-700/50">
                  <span class="text-xl">📜</span>
                  <div>
                    <span class="block text-[11px] font-bold text-white">Sertifikasi BNSP</span>
                    <span class="block text-[10px] text-slate-400">Kompetensi Kerja</span>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>

      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       2. FITUR UNGGULAN (EQUAL HEIGHT + ELEVATED CARDS)
       ========================================== -->
  <?php if (($sec_active['fitur'] ?? true) && !empty($fitur_list)): ?>
  <section id="fitur-unggulan" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#1bb28f] tracking-wider uppercase px-4 py-1.5 rounded-full bg-[#131c31] border border-[#1bb28f]/40 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Keunggulan Strategis Kampus</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Mengapa Memilih Politeknik Mulia Rokan?</h2>
        <p class="text-slate-300 text-sm">Fasilitas terdepan, kurikulum berbasis kerja nyata, dan kemitraan industri yang terjamin.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
        <?php foreach ($fitur_list as $fit): ?>
        <div class="card-elevated p-7 sm:p-8 rounded-3xl flex flex-col justify-between h-full group">
          <div>
            <div class="flex justify-between items-start mb-5">
              <?php if (!empty($fit['image_url'])): ?>
                <div class="w-14 h-14 rounded-full bg-white p-2.5 border-2 border-white/30 shadow-md group-hover:scale-110 transition-transform flex items-center justify-center overflow-hidden shrink-0">
                  <img src="<?= htmlspecialchars($fit['image_url']); ?>" alt="<?= htmlspecialchars($fit['judul_fitur']); ?>" class="w-full h-full object-contain">
                </div>
              <?php else: ?>
                <div class="w-14 h-14 rounded-full bg-white p-2 border-2 border-white/30 shadow-md group-hover:scale-110 transition-transform flex items-center justify-center shrink-0">
                  <span class="text-2xl"><?= htmlspecialchars($fit['icon'] ?? '⚡'); ?></span>
                </div>
              <?php endif; ?>
              <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-[#e9b35d]/15 text-[#e9b35d] border border-[#e9b35d]/30 uppercase">
                <?= htmlspecialchars($fit['badge'] ?? 'Unggulan'); ?>
              </span>
            </div>

            <h3 class="text-base font-black text-white mb-1.5 group-hover:text-[#e9b35d] transition-colors">
              <?= htmlspecialchars($fit['judul_fitur']); ?>
            </h3>
            
            <?php if (!empty($fit['sub_judul'])): ?>
              <span class="text-[11px] font-semibold text-[#1bb28f] block mb-3">
                <?= htmlspecialchars($fit['sub_judul']); ?>
              </span>
            <?php endif; ?>

            <p class="text-xs text-slate-300 leading-relaxed mb-5">
              <?= htmlspecialchars($fit['deskripsi']); ?>
            </p>
          </div>

          <?php if (!empty($fit['link_url'])): ?>
            <div class="pt-4 border-t border-slate-700/50">
              <a href="<?= htmlspecialchars($fit['link_url']); ?>" class="text-xs font-bold text-[#1bb28f] hover:text-[#e9b35d] flex items-center gap-1 transition">
                <span>Pelajari Selengkapnya</span> &rarr;
              </a>
            </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       3. VIDEO PROFIL (BALANCED 50:50 GRID)
       ========================================== -->
  <?php if (($sec_active['video'] ?? true) && !empty($video_list)): ?>
  <section id="video-profil" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#1bb28f] tracking-wider uppercase px-4 py-1.5 rounded-full bg-[#131c31] border border-[#1bb28f]/40 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Galeri Video Kampus</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Saksikan Aktivitas & Suasana Kampus</h2>
        <p class="text-slate-300 text-sm">Lihat langsung fasilitas teaching factory, kehidupan mahasiswa vokasi, dan komitmen industri kami.</p>
      </div>

      <!-- Balanced 50:50 Media Grid Layout -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
        <?php 
          $main_video = $video_list[0];
          $embed_url = $main_video['video_url'];
          if (strpos($embed_url, 'watch?v=') !== false) {
              $embed_url = str_replace('watch?v=', 'embed/', $embed_url);
          }
        ?>
        <!-- Media Container (Left 50%) -->
        <div class="card-elevated p-3.5 sm:p-5 rounded-3xl shadow-2xl">
          <div class="relative w-full aspect-video bg-black rounded-2xl overflow-hidden shadow-inner border border-slate-700/50">
            <iframe src="<?= htmlspecialchars($embed_url); ?>" title="<?= htmlspecialchars($main_video['judul']); ?>" class="w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          </div>
          <div class="p-3.5 flex items-center justify-between text-xs text-slate-400">
            <span class="flex items-center gap-2 text-[#1bb28f] font-bold">
              <span class="w-2 h-2 rounded-full bg-[#1bb28f] animate-pulse"></span>
              <span>Dokumentasi Resmi PMR</span>
            </span>
            <span class="text-[11px] text-slate-400 font-mono">Teaching Factory HD</span>
          </div>
        </div>

        <!-- Explanatory Text & Highlights (Right 50%) -->
        <div class="space-y-6">
          <div class="space-y-3">
            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black bg-[#e9b35d]/15 text-[#e9b35d] border border-[#e9b35d]/30 uppercase tracking-wider">
              Video Unggulan Utama
            </span>
            <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">
              <?= htmlspecialchars($main_video['judul']); ?>
            </h3>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
              <?= htmlspecialchars(!empty($main_video['deskripsi']) ? $main_video['deskripsi'] : 'Jelajahi ekosistem pembelajaran vokasi terapan di Politeknik Mulia Rokan, sarana laboratorium terpadu, dan fasilitas pabrik kelapa sawit mini (teaching factory) yang siap menempa mahasiswa menjadi profesional mandiri.'); ?>
            </p>
          </div>

          <!-- Feature Cards for Visual Balance -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
            <div class="card-elevated p-4 rounded-2xl flex items-start gap-3">
              <span class="text-2xl">🏭</span>
              <div>
                <h4 class="text-xs font-bold text-white">Teaching Factory</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Praktik nyata langsung di pabrik sawit mini</p>
              </div>
            </div>
            <div class="card-elevated p-4 rounded-2xl flex items-start gap-3">
              <span class="text-2xl">🎓</span>
              <div>
                <h4 class="text-xs font-bold text-white">Dosen Praktisi</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Instruktur bersertifikasi kompetensi industri</p>
              </div>
            </div>
          </div>

          <!-- Additional Playlist Items -->
          <?php if (count($video_list) > 1): ?>
          <div class="pt-3 border-t border-slate-800 space-y-2">
            <h4 class="text-xs font-black uppercase text-amber-400 tracking-wider">Video Terkait Lainnya:</h4>
            <div class="space-y-2">
              <?php foreach (array_slice($video_list, 1, 2) as $v_item): ?>
                <div class="card-elevated px-4 py-3 rounded-xl flex items-center justify-between text-xs">
                  <span class="font-bold text-white truncate max-w-[240px] sm:max-w-xs"><?= htmlspecialchars($v_item['judul']); ?></span>
                  <a href="<?= htmlspecialchars($v_item['video_url']); ?>" target="_blank" class="text-xs font-bold text-[#1bb28f] hover:text-[#e9b35d] shrink-0 ml-2">
                    ▶ Tonton
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

        </div>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       4. SAMBUTAN DIREKTUR
       ========================================== -->
  <?php if (($sec_active['sambutan'] ?? true) && !empty($site['sambutan_direktur_teks'])): ?>
  <section id="sambutan" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="card-elevated rounded-3xl p-8 sm:p-12 text-white shadow-2xl relative overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
          <div class="lg:col-span-4 text-center">
            <div class="relative w-40 h-40 sm:w-48 sm:h-48 mx-auto rounded-full overflow-hidden border-4 border-[#1bb28f]/50 shadow-xl mb-4">
              <img src="<?= htmlspecialchars($site['sambutan_direktur_foto'] ?? 'assets/images/direktur.jpg'); ?>" alt="Direktur Politeknik Mulia Rokan" class="w-full h-full object-cover">
            </div>
            <h3 class="text-base font-black text-white"><?= htmlspecialchars($site['sambutan_direktur_nama'] ?? 'Direktur'); ?></h3>
            <p class="text-xs text-amber-400 font-semibold"><?= htmlspecialchars($site['sambutan_direktur_jabatan'] ?? 'Direktur Politeknik Mulia Rokan'); ?></p>
          </div>

          <div class="lg:col-span-8 space-y-4">
            <span class="inline-flex items-center gap-2 text-xs font-bold text-[#1bb28f] tracking-wider uppercase px-4 py-1.5 rounded-full bg-[#0b1120] border border-[#1bb28f]/40 shadow-sm">
              <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
              <span>Sambutan Pimpinan Kampus</span>
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Mencetak SDM Vokasi yang Relevan, Berdaya Saing & Berintegritas</h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed italic border-l-4 border-amber-400 pl-4 py-1">
              "<?= nl2br(htmlspecialchars($site['sambutan_direktur_teks'])); ?>"
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       5. VISI & MISI (EQUAL HEIGHT STRETCH)
       ========================================== -->
  <?php if ($sec_active['visi_misi'] ?? true): ?>
  <section id="visi-misi" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto mb-14 space-y-3">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#1bb28f] tracking-wider uppercase px-4 py-1.5 rounded-full bg-[#131c31] border border-[#1bb28f]/40 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Landasan Filosofis & Komitmen</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Visi & Misi Politeknik Mulia Rokan</h2>
      </div>

      <!-- Equal Height Row via items-stretch -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
        <!-- Visi Card -->
        <div class="card-elevated p-8 sm:p-9 rounded-3xl h-full flex flex-col justify-start">
          <div class="w-12 h-12 rounded-2xl bg-[#0b1120] text-[#1bb28f] flex items-center justify-center font-black text-xl mb-4 border border-[#1bb28f]/40">
            🎯
          </div>
          <h3 class="text-lg font-black text-white mb-3">Visi Politeknik</h3>
          <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
            <?= nl2br(htmlspecialchars($site['visi_teks'] ?? 'Menjadi perguruan tinggi vokasi unggulan nasional di bidang industri sawit, manufaktur, dan software digital terapan.')); ?>
          </p>
        </div>

        <!-- Misi Card -->
        <div class="card-elevated p-8 sm:p-9 rounded-3xl h-full flex flex-col justify-start">
          <div class="w-12 h-12 rounded-2xl bg-[#0b1120] text-[#e9b35d] flex items-center justify-center font-black text-xl mb-4 border border-[#e9b35d]/40">
            🚀
          </div>
          <h3 class="text-lg font-black text-white mb-3">Misi Politeknik</h3>
          <div class="text-xs sm:text-sm text-slate-300 leading-relaxed space-y-2">
            <?= nl2br(htmlspecialchars($site['misi_teks'] ?? 'Pendidikan vokasi terapan, riset aplikatif, pengabdian masyarakat, dan kemitraan industri.')); ?>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       6. JALUR PENDAFTARAN (EQUAL HEIGHT + OUTLINE BUTTONS)
       ========================================== -->
  <?php if ($sec_active['jalur'] ?? true): ?>
  <section id="section-jalur" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#1bb28f] tracking-wider uppercase px-4 py-1.5 rounded-full bg-[#131c31] border border-[#1bb28f]/40 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Jalur Masuk PMB 2026</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Pilih Jalur Pendaftaran Sesuai Potensi Anda</h2>
        <p class="text-slate-300 text-sm">Tersedia berbagai pilihan jalur penerimaan dengan fasilitas beasiswa bebas biaya kuliah dan seleksi administrasi mudah.</p>
      </div>

      <!-- Equal Height Row via items-stretch -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        <?php foreach ($jalur_list as $jalur): ?>
        <div class="card-elevated rounded-3xl p-7 sm:p-8 flex flex-col justify-between h-full">
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="text-[11px] font-bold text-amber-300 bg-[#0b1120] border border-amber-400/30 px-2.5 py-1 rounded-lg uppercase">
                Kode: <?= htmlspecialchars($jalur['kode_jalur']); ?>
              </span>
              <span class="text-xs font-bold text-emerald-400 bg-emerald-500/20 px-2.5 py-0.5 rounded-full flex items-center gap-1 border border-emerald-400/30">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>Pendaftaran Buka</span>
              </span>
            </div>
            
            <h3 class="text-lg font-black text-white mb-2">
              <?= htmlspecialchars($jalur['nama_jalur']); ?>
            </h3>
            
            <p class="text-xs text-slate-300 leading-relaxed mb-6">
              <?= htmlspecialchars($jalur['deskripsi']); ?>
            </p>
            
            <div class="border-t border-slate-700/50 pt-4 space-y-2 text-xs text-slate-400 mb-6">
              <div class="flex justify-between items-center">
                <span>Biaya Pendaftaran:</span>
                <span class="font-bold text-sm text-white"><?= $jalur['biaya_daftar'] == 0 ? '<span class="text-emerald-400 bg-emerald-500/20 border border-emerald-400/30 px-2 py-0.5 rounded font-black">GRATIS</span>' : formatRupiah($jalur['biaya_daftar']); ?></span>
              </div>
              <div class="flex justify-between items-center">
                <span>Batas Pendaftaran:</span>
                <span class="font-semibold text-slate-300"><?= formatTanggalIndo($jalur['tgl_tutup']); ?></span>
              </div>
            </div>
          </div>

          <!-- Tombol Sekunder: Bergaya Outline Hijau Toska -->
          <a href="daftar.php?jalur=<?= $jalur['id']; ?>" class="w-full text-center py-3 px-4 rounded-xl text-xs font-bold text-[#1bb28f] hover:text-[#0b1120] border border-[#1bb28f]/50 hover:bg-[#1bb28f] hover:border-[#1bb28f] transition duration-200 block">
            Daftar Jalur Ini &rarr;
          </a>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       7. PROGRAM STUDI (EQUAL HEIGHT + OUTLINE BUTTONS)
       ========================================== -->
  <?php if ($sec_active['prodi'] ?? true): ?>
  <section id="section-prodi" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
        <span class="inline-flex items-center gap-2 text-xs font-bold text-[#1bb28f] tracking-wider uppercase px-4 py-1.5 rounded-full bg-[#131c31] border border-[#1bb28f]/40 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Kurikulum Berbasis Praktik 70% & Industri</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Program Studi Sarjana Terapan (D4)</h2>
        <p class="text-slate-300 text-sm">Gelar Sarjana Terapan Teknik (S.Tr.T) dan Komputer (S.Tr.Kom) yang setara S1 dengan keahlian teknis siap terap.</p>
      </div>

      <!-- Equal Height Row via items-stretch -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        <?php foreach ($prodi_list as $prodi): ?>
        <div class="card-elevated rounded-3xl p-7 sm:p-8 flex flex-col justify-between h-full">
          <div>
            <div class="w-14 h-14 rounded-2xl bg-[#0b1120] text-[#1bb28f] border border-slate-700/60 flex items-center justify-center font-black text-xl mb-4">
              <?= htmlspecialchars($prodi['jenjang']); ?>
            </div>
            
            <span class="text-[11px] font-bold text-[#1bb28f] uppercase tracking-wider block mb-1">
              Kode: <?= htmlspecialchars($prodi['kode_prodi']); ?> • Akreditasi: <strong class="text-amber-400"><?= htmlspecialchars($prodi['akreditasi']); ?></strong>
            </span>
            
            <h3 class="text-lg font-black text-white mb-3 leading-snug">
              <?= htmlspecialchars($prodi['nama_prodi']); ?>
            </h3>
            
            <p class="text-xs text-slate-300 leading-relaxed mb-5">
              <?= htmlspecialchars($prodi['deskripsi']); ?>
            </p>

            <div class="bg-[#0b1120] p-4 rounded-2xl border border-slate-700/50 mb-5">
              <span class="text-[11px] font-bold text-amber-300 block mb-1">💼 Prospek Karir Utama:</span>
              <p class="text-[11px] text-slate-300 leading-relaxed">
                <?= htmlspecialchars($prodi['prospek_karir']); ?>
              </p>
            </div>
          </div>

          <!-- Card Footer with Text Link/Outline Action -->
          <div class="pt-4 border-t border-slate-700/50 flex items-center justify-between">
            <span class="text-xs text-slate-400">Kuota: <strong class="text-white"><?= $prodi['kuota']; ?> Mhs</strong></span>
            <a href="daftar.php?prodi=<?= $prodi['id']; ?>" class="px-4 py-2 rounded-xl text-xs font-bold text-[#1bb28f] hover:text-[#0b1120] border border-[#1bb28f]/50 hover:bg-[#1bb28f] transition flex items-center gap-1">
              <span>Pilih Prodi</span> &rarr;
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       8. FASILITAS KAMPUS (EQUAL HEIGHT)
       ========================================== -->
  <?php if ($sec_active['fasilitas'] ?? true): ?>
  <section id="fasilitas" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full border border-white/25 shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Sarana & Prasarana Terpadu</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Fasilitas Kampus & Laboratorium Terpadu</h2>
        <p class="text-slate-300 text-sm">Menunjang proses belajar mengajar aplikatif dengan standar peralatan industri modern.</p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
        <?php foreach ($fasilitas_list as $fas): ?>
        <div class="card-elevated rounded-3xl p-7 sm:p-8 h-full flex flex-col justify-start">
          <div class="w-12 h-12 rounded-2xl bg-slate-950 text-amber-300 flex items-center justify-center font-bold text-lg mb-4 border border-white/10">
            ⚙️
          </div>
          <span class="text-[10px] font-bold text-[#1bb28f] uppercase tracking-wider block mb-1">
            <?= htmlspecialchars($fas['kategori'] ?? 'Fasilitas Kampus'); ?>
          </span>
          <h3 class="text-base font-black text-white mb-2">
            <?= htmlspecialchars($fas['nama_fasilitas']); ?>
          </h3>
          <p class="text-xs text-slate-300 leading-relaxed">
            <?= htmlspecialchars($fas['deskripsi']); ?>
          </p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       9. ALUR PENDAFTARAN & BIAYA PENDIDIKAN
       ========================================== -->
  <?php if ($sec_active['alur_biaya'] ?? true): ?>
  <section id="section-alur" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        
        <!-- Left: Alur 5 Langkah -->
        <div class="lg:col-span-7 space-y-6">
          <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full border border-white/25 shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
              <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
              <span>Tahapan Seleksi Online</span>
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">5 Langkah Mudah Pendaftaran Mahasiswa Baru</h2>
            <p class="text-slate-300 text-sm mt-1">Daftar secara mandiri 100% online dari rumah tanpa perlu repot datang ke kampus.</p>
          </div>

          <div class="space-y-4">
            <div class="card-elevated p-6 sm:p-7 rounded-2xl flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl font-black flex items-center justify-center shrink-0 text-white shadow-xs" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">1</div>
              <div>
                <h4 class="text-sm font-bold text-white">Isi Formulir Online & Pilih Prodi</h4>
                <p class="text-xs text-slate-300 mt-0.5">Pilih jalur seleksi dan tentukan program studi prioritas pilihan 1 dan 2 Anda.</p>
              </div>
            </div>

            <div class="card-elevated p-6 sm:p-7 rounded-2xl flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl font-black flex items-center justify-center shrink-0 text-white shadow-xs" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">2</div>
              <div>
                <h4 class="text-sm font-bold text-white">Unggah Dokumen Berkas Persyaratan</h4>
                <p class="text-xs text-slate-300 mt-0.5">Unggah pas foto formal warna, scan Kartu Keluarga (KK), dan scan Rapor semester 1-5 atau Ijazah.</p>
              </div>
            </div>

            <div class="card-elevated p-6 sm:p-7 rounded-2xl flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl font-black flex items-center justify-center shrink-0 text-white shadow-xs" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">3</div>
              <div>
                <h4 class="text-sm font-bold text-white">Simpan Nomor Pendaftaran & Kartu Ujian</h4>
                <p class="text-xs text-slate-300 mt-0.5">Dapatkan kode pendaftaran unik (PMB-2026-XXXX) untuk mengecek status seleksi kapan saja.</p>
              </div>
            </div>

            <div class="card-elevated p-6 sm:p-7 rounded-2xl flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl font-black flex items-center justify-center shrink-0 text-white shadow-xs" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">4</div>
              <div>
                <h4 class="text-sm font-bold text-white">Verifikasi Dokumen & Pengumuman Hasil</h4>
                <p class="text-xs text-slate-300 mt-0.5">Panitia PMB memeriksa berkas Anda. Pantau status lolos seleksi melalui menu Cek Status Pendaftaran.</p>
              </div>
            </div>

            <div class="card-elevated p-6 sm:p-7 rounded-2xl flex items-start gap-4">
              <div class="w-9 h-9 rounded-xl bg-[#e9b35d] text-slate-950 font-black flex items-center justify-center shrink-0 shadow-xs">5</div>
              <div>
                <h4 class="text-sm font-bold text-white">Daftar Ulang & Orientasi Mahasiswa Baru</h4>
                <p class="text-xs text-slate-300 mt-0.5">Lakukan registrasi ulang dan ikuti Pengenalan Kehidupan Kampus Mahasiswa Baru (PKKMB).</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Skema Biaya UKT & Beasiswa -->
        <div id="biaya" class="lg:col-span-5 space-y-6">
          <div class="card-elevated p-7 sm:p-8 rounded-3xl">
            <h3 class="text-base font-black text-white mb-1">Skema Biaya Pendidikan Terjangkau</h3>
            <p class="text-xs text-slate-400 mb-5">Bebas uang pangkal/gedung (SPI) dan dapat dicicil per bulan tanpa bunga.</p>

            <div class="space-y-3 text-xs">
              <div class="flex justify-between items-center py-2.5 border-b border-white/10">
                <span class="text-slate-300">Uang Pendaftaran (Prestasi)</span>
                <span class="font-black text-emerald-400 bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-400/30">Rp 0 (GRATIS)</span>
              </div>
              <div class="flex justify-between items-center py-2.5 border-b border-white/10">
                <span class="text-slate-300">Uang Pendaftaran (Reguler)</span>
                <span class="font-bold text-white">Rp 150.000</span>
              </div>
              <div class="flex justify-between items-center py-2.5 border-b border-white/10">
                <span class="text-slate-300">Uang Gedung / SPI</span>
                <span class="font-black text-emerald-400 bg-emerald-500/20 px-2 py-0.5 rounded border border-emerald-400/30">BEBAS BIAYA</span>
              </div>
              <div class="flex justify-between items-center py-2.5 border-b border-white/10">
                <span class="text-slate-300">UKT per Semester (D4)</span>
                <span class="font-bold text-white">Rp 2.500.000 / sem</span>
              </div>
              <div class="flex justify-between items-center py-2.5">
                <span class="text-slate-300">Opsi Cicilan Bulanan</span>
                <span class="font-black text-amber-400">Rp 416.000 / bln</span>
              </div>
            </div>

            <div class="mt-6 p-5 sm:p-6 rounded-2xl bg-amber-500/10 border border-amber-400/30 text-xs">
              <div class="font-black text-amber-300 mb-1">🌟 Kuota Beasiswa Penuh 100%:</div>
              <p class="text-slate-300 leading-relaxed">
                Tersedia beasiswa pembebasan SPP 8 semester untuk penerima KIP-Kuliah, siswa berprestasi ranking 1-5, dan anak petani sawit mitra.
              </p>
            </div>
          </div>
        </div>

      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       10. TESTIMONI & MITRA INDUSTRI (EQUAL HEIGHT)
       ========================================== -->
  <?php if ($sec_active['testimoni'] ?? true): ?>
  <section id="mitra" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-3xl mx-auto space-y-3 mb-14">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full border border-white/25 shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Suara Mitra & Mahasiswa</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Dipercaya Industri, Dicintai Mahasiswa</h2>
      </div>

      <!-- Equal Height Row via items-stretch -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">
        <?php foreach ($testimoni_list as $testi): ?>
        <div class="card-elevated p-7 sm:p-8 rounded-3xl flex flex-col justify-between h-full">
          <div class="mb-4">
            <span class="text-amber-400 text-lg">★★★★★</span>
            <p class="text-xs sm:text-sm text-slate-300 italic leading-relaxed mt-3">
              "<?= htmlspecialchars($testi['pesan']); ?>"
            </p>
          </div>
          <div class="pt-4 border-t border-white/10 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full font-black flex items-center justify-center shrink-0 border border-white/30 text-white shadow-xs" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
              <?= substr($testi['nama'], 0, 1); ?>
            </div>
            <div>
              <h4 class="text-xs font-black text-white"><?= htmlspecialchars($testi['nama']); ?></h4>
              <p class="text-[11px] text-[#1bb28f]"><?= htmlspecialchars($testi['role_instansi']); ?></p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       11. PENGUMUMAN & BERITA KAMPUS (EQUAL HEIGHT)
       ========================================== -->
  <?php if ($sec_active['pengumuman'] ?? true): ?>
  <section id="section-pengumuman" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 mb-14">
        <div>
          <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full border border-white/25 shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
            <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
            <span>Informasi Terkini</span>
          </span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-white mt-2">Pengumuman & Berita Kampus</h2>
        </div>
        <a href="cek_status.php" class="text-xs font-bold text-[#1bb28f] hover:text-[#e9b35d] transition flex items-center gap-1">
          <span>Lihat Pengumuman Hasil Seleksi PMB</span> &rarr;
        </a>
      </div>

      <!-- Equal Height Row via items-stretch -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
        <?php foreach ($pengumuman_list as $info): ?>
        <div class="card-elevated p-7 sm:p-8 rounded-3xl h-full flex flex-col justify-start">
          <div class="flex items-center gap-2 text-[11px] text-slate-400 mb-2">
            <span>📅 <?= formatTanggalIndo($info['tanggal']); ?></span>
            <span>•</span>
            <span class="capitalize text-amber-400 font-bold"><?= htmlspecialchars($info['kategori']); ?></span>
          </div>
          <h3 class="text-sm font-black text-white mb-2 leading-snug">
            <?= htmlspecialchars($info['judul']); ?>
          </h3>
          <p class="text-xs text-slate-300 line-clamp-3 leading-relaxed">
            <?= htmlspecialchars($info['isi']); ?>
          </p>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       12. FAQ (TANYA JAWAB POPULER)
       ========================================== -->
  <?php if ($sec_active['faq'] ?? true): ?>
  <section id="faq" class="py-20 lg:py-24 border-b border-white/10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="text-center space-y-3 mb-14">
        <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full border border-white/25 shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
          <span>Pertanyaan Populer</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Frequently Asked Questions (FAQ)</h2>
      </div>

      <div class="space-y-4">
        <div class="card-elevated rounded-2xl overflow-hidden">
          <button type="button" class="faq-toggle w-full px-6 sm:px-8 py-5 text-left font-bold text-sm text-white flex justify-between items-center">
            <span>Apakah lulusan SMA jurusan IPS atau Madrasah Aliyah bisa mendaftar?</span>
            <span class="faq-icon text-amber-400 transition-transform duration-200">▼</span>
          </button>
          <div class="hidden px-6 sm:px-8 pb-5 text-xs text-slate-300 leading-relaxed border-t border-white/10 pt-3">
            Bisa. Politeknik Mulia Rokan menerima lulusan dari semua jurusan SMA (IPA/IPS/Bahasa), SMK (semua bidang keahlian), maupun MA/Pesantren sederajat. Kampus menyediakan program matrikulasi pengenalan dasar keahlian.
          </div>
        </div>

        <div class="card-elevated rounded-2xl overflow-hidden">
          <button type="button" class="faq-toggle w-full px-6 sm:px-8 py-5 text-left font-bold text-sm text-white flex justify-between items-center">
            <span>Bagaimana cara mendaftar beasiswa bebas biaya kuliah?</span>
            <span class="faq-icon text-amber-400 transition-transform duration-200">▼</span>
          </button>
          <div class="hidden px-6 sm:px-8 pb-5 text-xs text-slate-300 leading-relaxed border-t border-white/10 pt-3">
            Pilih <strong>Jalur Prestasi & KIP-Kuliah</strong> pada formulir pendaftaran online. Unggah scan rapor semester 1-5 atau sertifikat juara, serta kartu KIP/KKS/PKH bagi pemegang bantuan sosial.
          </div>
        </div>

        <div class="card-elevated rounded-2xl overflow-hidden">
          <button type="button" class="faq-toggle w-full px-6 sm:px-8 py-5 text-left font-bold text-sm text-white flex justify-between items-center">
            <span>Apakah ijazah Sarjana Terapan (D4) setara dengan Sarjana (S1)?</span>
            <span class="faq-icon text-amber-400 transition-transform duration-200">▼</span>
          </button>
          <div class="hidden px-6 sm:px-8 pb-5 text-xs text-slate-300 leading-relaxed border-t border-white/10 pt-3">
            Ya! Sesuai Undang-Undang No. 12 Tahun 2012 dan Kerangka Kualifikasi Nasional Indonesia (KKNI), Sarjana Terapan (D4) berada di Level 6 yang sama dengan S1 akademik, berhak menyandang gelar S.Tr.T / S.Tr.Kom, bisa langsung melanjutkan studi Magister (S2), dan melamar CPNS/BUMN di formasi strata 1.
          </div>
        </div>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ==========================================
       13. CTA BANNER AKHIR (ELEVATED CONTAINER)
       ========================================== -->
  <?php if ($sec_active['cta'] ?? true): ?>
  <section class="py-20 lg:py-24 border-t border-white/10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="card-elevated rounded-3xl p-8 sm:p-14 text-center space-y-6 relative overflow-hidden border border-white/10">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-950/60 border border-white/20 text-white text-xs font-bold shadow">
          <span class="w-2 h-2 rounded-full bg-[#e9b35d] animate-ping"></span>
          <span>Penerimaan Mahasiswa Baru Gelombang 1 Dibuka</span>
        </div>
        <h2 class="text-2xl sm:text-4xl font-black text-white">Siap Menjadi Ahli Vokasi & Berkarir di Industri Sawit Nasional?</h2>
        <p class="text-slate-300 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
          Raih masa depan mandiri bersama Politeknik Mulia Rokan. Manfaatkan kuota beasiswa dan program magang kerja terpadu.
        </p>
        
        <!-- CTA Action Buttons -->
        <div class="pt-3 flex flex-col sm:flex-row justify-center items-center gap-4">
          <a href="daftar.php" class="px-8 py-4 text-white font-black text-sm rounded-2xl shadow-xl hover:brightness-110 hover:shadow-2xl transition duration-200" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
            Formulir Pendaftaran PMB Online &rarr;
          </a>
          <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $site['whatsapp_helpdesk'] ?? '6281234567890'); ?>" target="_blank" class="px-7 py-4 text-[#1bb28f] hover:text-[#e9b35d] font-bold rounded-2xl border border-[#1bb28f]/50 hover:border-[#e9b35d] transition duration-300">
            💬 Tanya Panitia via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>

</div> <!-- End of theme-bg-canvas -->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
