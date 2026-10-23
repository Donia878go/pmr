<?php
$site = getSiteSettings($pdo);
$socials = getSocialMedia($pdo);
$logo_img = !empty($site['logo_url']) ? $site['logo_url'] : 'assets/images/logo-politeknik.png';
$logo_yayasan_img = !empty($site['logo_yayasan_url']) ? $site['logo_yayasan_url'] : 'assets/images/logo-yayasan.png';
$kampus_name = !empty($site['nama_kampus']) ? $site['nama_kampus'] : 'POLITEKNIK MULIA ROKAN';
?>
<!-- FOOTER IDENTITAS RESMI -->
<footer id="kontak" class="bg-slate-950 text-slate-400 text-sm mt-auto border-t border-slate-800">
  <!-- Accent Bar with Gear Brand Colors #1bb28f, #e9b35d and #1b969d -->
  <div class="h-2 w-full" style="background: linear-gradient(90deg, #1bb28f 0%, #e9b35d 50%, #1b969d 100%);"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">
      
      <!-- Col 1: Identity & Social Media -->
      <div class="space-y-4">
        <div class="flex items-center gap-3">
          <div class="flex items-center gap-1.5 shrink-0">
            <div class="w-10 h-10 rounded-full bg-white p-1 shadow-sm border border-slate-700 flex items-center justify-center overflow-hidden">
              <img src="<?= htmlspecialchars($logo_yayasan_img); ?>" alt="Yayasan" class="w-full h-full object-contain" title="Yayasan Rokan Jaya Bermarwah">
            </div>
            <div class="w-10 h-10 rounded-full bg-white p-1 shadow-sm border border-slate-700 flex items-center justify-center overflow-hidden">
              <img src="<?= htmlspecialchars($logo_img); ?>" alt="Politeknik" class="w-full h-full object-contain" title="Politeknik Mulia Rokan">
            </div>
          </div>
          <div>
            <span class="block text-[10px] font-bold text-[#e9b35d] uppercase tracking-wider leading-none">YAYASAN ROKAN JAYA BERMARWAH</span>
            <h4 class="text-white font-extrabold text-sm uppercase tracking-tight mt-0.5"><?= htmlspecialchars($kampus_name); ?></h4>
            <p class="text-[11px] text-[#1bb28f] font-semibold"><?= htmlspecialchars($site['tagline'] ?? 'Kampus Vokasi Terapan Riau'); ?></p>
          </div>
        </div>
        <p class="text-xs leading-relaxed text-slate-400">
          <?= htmlspecialchars($site['sub_nama'] ?? 'Pendidikan Tinggi Vokasi Unggulan di Kabupaten Rokan Hilir, Riau. Mempersiapkan SDM profesional siap kerja di industri perkebunan sawit, otomasi manufaktur, dan software engineering.'); ?>
        </p>
        
        <!-- Social Media Badges -->
        <div class="pt-2">
          <span class="text-[11px] font-bold text-slate-300 block mb-2 uppercase tracking-wider">Sosial Media Resmi:</span>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($socials as $soc): 
              $soc_icon = strtolower($soc['platform']);
              $soc_label = htmlspecialchars($soc['nama_label']);
              $soc_url = htmlspecialchars($soc['url']);
            ?>
              <a href="<?= $soc_url; ?>" target="_blank" title="<?= $soc_label; ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-teal-900/60 border border-slate-800 text-xs text-slate-300 hover:text-amber-400 transition">
                <span><?= $soc_label; ?></span>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- Col 2: Program Studi Sarjana Terapan -->
      <div>
        <h4 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-teal-500"></span>
          <span>Program Studi D4</span>
        </h4>
        <ul class="space-y-2.5 text-xs text-slate-400">
          <li>
            <a href="index.php#section-prodi" class="hover:text-amber-400 transition block">
              <strong class="text-slate-200">D4 Rekayasa Pengolahan Kelapa Sawit</strong>
              <span class="block text-[11px] text-slate-500">Otomasi Pabrik Kelapa Sawit & Refinery</span>
            </a>
          </li>
          <li>
            <a href="index.php#section-prodi" class="hover:text-amber-400 transition block">
              <strong class="text-slate-200">D4 Rekayasa Mesin & Manufaktur</strong>
              <span class="block text-[11px] text-slate-500">PLC Otomasi, CNC & Fabrikasi Presisi</span>
            </a>
          </li>
          <li>
            <a href="index.php#section-prodi" class="hover:text-amber-400 transition block">
              <strong class="text-slate-200">D4 Rekayasa Perangkat Lunak</strong>
              <span class="block text-[11px] text-slate-500">Software Enterprise, Mobile & IoT Industri</span>
            </a>
          </li>
        </ul>
      </div>

      <!-- Col 3: Layanan Calon Mahasiswa -->
      <div>
        <h4 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          <span>Layanan Informasi</span>
        </h4>
        <ul class="space-y-2 text-xs">
          <li><a href="daftar.php" class="hover:text-amber-400 transition flex items-center gap-1.5"><span>&rsaquo;</span> Pendaftaran Online PMB</a></li>
          <li><a href="cek_status.php" class="hover:text-amber-400 transition flex items-center gap-1.5"><span>&rsaquo;</span> Tracking & Cetak Kartu Ujian</a></li>
          <li><a href="index.php#section-jalur" class="hover:text-amber-400 transition flex items-center gap-1.5"><span>&rsaquo;</span> Syarat Jalur Prestasi & KIP-K</a></li>
          <li><a href="index.php#section-alur" class="hover:text-amber-400 transition flex items-center gap-1.5"><span>&rsaquo;</span> Rincian UKT & Beasiswa</a></li>
          <li><a href="form.php?slug=survei-minat-bakat" class="hover:text-amber-400 transition flex items-center gap-1.5"><span>&rsaquo;</span> Survei Minat & Bakat Siswa</a></li>
          <li><a href="form.php?slug=layanan-aspirasi" class="hover:text-amber-400 transition flex items-center gap-1.5"><span>&rsaquo;</span> Konsultasi & Pengaduan PMB</a></li>
        </ul>
      </div>

      <!-- Col 4: Kontak & Sekretariat -->
      <div>
        <h4 class="text-white font-bold text-sm uppercase mb-4 tracking-wider flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-teal-500"></span>
          <span>Sekretariat PMB</span>
        </h4>
        <p class="text-xs text-slate-400 leading-relaxed mb-3">
          <?= nl2br(htmlspecialchars($site['alamat'] ?? 'Jl. Lintas Bagansiapiapi - Ujung Tanjung, Rokan Hilir, Riau')); ?>
        </p>
        <div class="space-y-1.5 text-xs text-slate-400">
          <p class="flex items-center gap-2">
            <span class="text-amber-400">📞 Telepon:</span>
            <span><?= htmlspecialchars($site['telepon'] ?? '-'); ?></span>
          </p>
          <p class="flex items-center gap-2">
            <span class="text-emerald-400">💬 WhatsApp:</span>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $site['whatsapp_helpdesk'] ?? ''); ?>" target="_blank" class="text-emerald-300 hover:underline">
              <?= htmlspecialchars($site['whatsapp_helpdesk'] ?? '-'); ?>
            </a>
          </p>
          <p class="flex items-center gap-2">
            <span class="text-amber-400">✉️ Email:</span>
            <span><?= htmlspecialchars($site['email'] ?? '-'); ?></span>
          </p>
          <p class="flex items-center gap-2 text-[11px] text-slate-500">
            <span>⏰ <?= htmlspecialchars($site['jam_operasional'] ?? '-'); ?></span>
          </p>
        </div>
      </div>

    </div>

    <!-- Google Maps Preview jika ada -->
    <?php if (!empty($site['google_maps_iframe'])): ?>
    <div class="mt-6 mb-8 rounded-2xl overflow-hidden border border-slate-800 shadow-lg">
      <div class="bg-slate-900 px-4 py-2 border-b border-slate-800 flex items-center justify-between text-xs text-slate-400">
        <span class="font-bold text-slate-300">📍 Lokasi Kampus Utama di Google Maps</span>
        <span>Bagansiapiapi, Rokan Hilir</span>
      </div>
      <div class="w-full h-56 sm:h-64">
        <?= $site['google_maps_iframe']; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Bottom Copyright (CLEAN - NO ADMIN LOGIN LINKS) -->
    <div class="border-t border-slate-850 pt-6 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500 gap-2">
      <p>&copy; <?= date('Y'); ?> <?= htmlspecialchars($kampus_name); ?>. Seluruh hak cipta dilindungi.</p>
      <p class="text-slate-600 text-[11px]">
        Portal Resmi PMB Terpadu Kampus Vokasi Riau
      </p>
    </div>
  </div>
</footer>

<!-- Include Audio Player & Promo Popup Modal -->
<?php 
require_once __DIR__ . '/audio_player.php'; 
require_once __DIR__ . '/popup_modal.php'; 
?>

<?php if (!empty($site['custom_body_scripts'])): ?>
  <!-- Custom Body / Chatbot Scripts Injection -->
  <?= $site['custom_body_scripts']; ?>
<?php endif; ?>

<script src="assets/js/main.js"></script>
</body>
</html>
