<?php
$menus = getNavMenus($pdo);
$site = getSiteSettings($pdo);
$logo_img = !empty($site['logo_url']) ? $site['logo_url'] : 'assets/images/logo-politeknik.png';
$logo_yayasan_img = !empty($site['logo_yayasan_url']) ? $site['logo_yayasan_url'] : 'assets/images/logo-yayasan.png';
$kampus_name = !empty($site['nama_kampus']) ? $site['nama_kampus'] : 'POLITEKNIK MULIA ROKAN';
?>
<!-- MAIN NAVIGATION -->
<nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/90 shadow-sm transition-all duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-20 gap-4">
      
      <!-- Brand Logo (BULAT & BACKGROUND PUTIH) & Campus Title -->
      <a href="index.php" class="flex items-center gap-2.5 shrink-0 group">
        <div class="flex items-center gap-1.5 shrink-0">
          <!-- Logo Yayasan Bulat Latar Putih -->
          <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white p-1 shadow-md border-2 border-slate-200 group-hover:border-[#e9b35d] flex items-center justify-center overflow-hidden transition-all duration-300">
            <img src="<?= htmlspecialchars($logo_yayasan_img); ?>" alt="Yayasan Rokan Jaya Bermarwah" class="w-full h-full object-contain" title="Yayasan Rokan Jaya Bermarwah">
          </div>
          <!-- Logo Politeknik Bulat Latar Putih -->
          <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white p-1 shadow-md border-2 border-slate-200 group-hover:border-[#1bb28f] flex items-center justify-center overflow-hidden transition-all duration-300">
            <img src="<?= htmlspecialchars($logo_img); ?>" alt="Politeknik Mulia Rokan" class="w-full h-full object-contain" title="Politeknik Mulia Rokan">
          </div>
        </div>
        <div class="border-l-2 border-slate-200 pl-2.5">
          <span class="block text-[9px] font-black text-[#e9b35d] uppercase tracking-wider leading-none">
            YAYASAN ROKAN JAYA BERMARWAH
          </span>
          <span class="block text-xs sm:text-sm font-black text-[#1bb28f] tracking-tight leading-tight uppercase group-hover:text-[#e9b35d] transition-colors mt-0.5">
            <?= htmlspecialchars($kampus_name); ?>
          </span>
          <span class="block text-[9px] font-semibold text-slate-400 uppercase tracking-wider leading-none mt-0.5">
            <?= htmlspecialchars($site['tagline'] ?? 'Portal PMB TA 2026/2027'); ?>
          </span>
        </div>
      </a>

      <!-- Desktop Navigation with Dropdowns (Kompak, Rapi & Tidak Tumpang Tindih) -->
      <div class="hidden xl:flex items-center gap-1 text-xs font-bold text-slate-700">
        <?php foreach ($menus as $menu): 
          $judul_lower = strtolower($menu['judul']);
          $is_gradient_menu = (!empty($menu['is_gradient']) || strpos($judul_lower, 'jalur') !== false || strpos($judul_lower, 'layanan') !== false);
        ?>
          <?php if (!empty($menu['children'])): ?>
            <!-- Menu with Dropdown -->
            <div class="nav-item-dropdown relative py-6 px-0.5">
              <?php if ($is_gradient_menu): ?>
                <button type="button" class="menu-gradient-pill flex items-center gap-1 px-3 py-1.5 text-xs font-bold focus:outline-none">
                  <span><?= htmlspecialchars($menu['judul']); ?></span>
                  <svg class="w-3.5 h-3.5 text-white/90 group-hover:text-[#e9b35d] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
              <?php else: ?>
                <button type="button" class="flex items-center gap-1 px-2.5 py-1.5 text-slate-700 hover:text-[#1bb28f] font-bold transition-colors focus:outline-none hover:bg-slate-50 rounded-xl">
                  <span><?= htmlspecialchars($menu['judul']); ?></span>
                  <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-[#1bb28f] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
              <?php endif; ?>

              <!-- Dropdown Content -->
              <div class="nav-dropdown-menu absolute left-0 top-full -mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-100 py-3 px-2 z-50">
                <div class="text-[10px] uppercase font-black tracking-wider text-white px-3 py-1.5 mb-2 rounded-xl shadow-sm flex items-center justify-between" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
                  <span><?= htmlspecialchars($menu['judul']); ?> PMR</span>
                  <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
                </div>
                <?php foreach ($menu['children'] as $child): ?>
                  <a href="<?= htmlspecialchars($child['url']); ?>" target="<?= htmlspecialchars($child['target']); ?>" class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-slate-700 hover:text-[#1bb28f] hover:bg-[#1bb28f]/10 rounded-xl transition">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#e9b35d]"></span>
                    <span><?= htmlspecialchars($child['judul']); ?></span>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
          <?php else: ?>
            <!-- Single Link Menu -->
            <?php if ($is_gradient_menu): ?>
              <a href="<?= htmlspecialchars($menu['url']); ?>" target="<?= htmlspecialchars($menu['target']); ?>" class="menu-gradient-pill py-1.5 px-3 text-xs font-bold transition">
                <?= htmlspecialchars($menu['judul']); ?>
              </a>
            <?php else: ?>
              <a href="<?= htmlspecialchars($menu['url']); ?>" target="<?= htmlspecialchars($menu['target']); ?>" class="py-1.5 px-2.5 text-slate-700 hover:text-[#1bb28f] font-bold hover:bg-[#1bb28f]/10 rounded-xl transition-colors">
                <?= htmlspecialchars($menu['judul']); ?>
              </a>
            <?php endif; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <!-- Action Buttons -->
      <div class="hidden md:flex items-center gap-2 shrink-0">
        <a href="cek_status.php" class="px-3 py-2 text-xs font-bold text-[#1bb28f] hover:text-[#e9b35d] bg-[#1bb28f]/10 hover:bg-[#1bb28f]/20 border border-[#1bb28f]/30 hover:border-[#e9b35d] rounded-xl transition flex items-center gap-1.5 shrink-0">
          <svg class="w-3.5 h-3.5 text-[#1bb28f]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          <span>Cek Kelulusan</span>
        </a>
        <a href="daftar.php" class="brand-gear-btn px-4 py-2 text-xs font-black rounded-xl transition-all duration-200 flex items-center gap-1.5 shrink-0">
          <span>Daftar Sekarang</span>
          <svg class="w-4 h-4 text-[#e9b35d]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
      </div>

      <!-- Mobile Menu Button -->
      <div class="flex items-center xl:hidden">
        <button id="mobile-menu-btn" type="button" class="text-slate-700 hover:text-teal-700 focus:outline-none p-2 rounded-xl bg-slate-100">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Dropdown Menu (No Admin links!) -->
  <div id="mobile-menu" class="hidden xl:hidden bg-white border-b border-slate-200 px-5 pt-3 pb-6 max-h-[80vh] overflow-y-auto space-y-3">
    <?php foreach ($menus as $m_idx => $menu): 
      $m_lower = strtolower($menu['judul']);
      $m_is_grad = (!empty($menu['is_gradient']) || strpos($m_lower, 'jalur') !== false || strpos($m_lower, 'layanan') !== false);
    ?>
      <?php if (!empty($menu['children'])): ?>
        <div class="border-b border-slate-100 pb-2">
          <?php if ($m_is_grad): ?>
            <button type="button" onclick="toggleMobileSubmenu('mob-sub-<?= $m_idx; ?>')" class="w-full flex justify-between items-center py-2 px-3 text-white font-bold text-sm rounded-xl shadow-sm mb-1" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
              <span class="flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
                <?= htmlspecialchars($menu['judul']); ?>
              </span>
              <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
          <?php else: ?>
            <button type="button" onclick="toggleMobileSubmenu('mob-sub-<?= $m_idx; ?>')" class="w-full flex justify-between items-center py-2 text-slate-800 font-bold text-sm">
              <span><?= htmlspecialchars($menu['judul']); ?></span>
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
          <?php endif; ?>
          <div id="mob-sub-<?= $m_idx; ?>" class="hidden pl-3 space-y-1 mt-1 border-l-2 border-[#1bb28f]">
            <?php foreach ($menu['children'] as $child): ?>
              <a href="<?= htmlspecialchars($child['url']); ?>" class="block py-1.5 text-xs font-medium text-slate-600 hover:text-[#1bb28f]">
                <?= htmlspecialchars($child['judul']); ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php else: ?>
        <?php if ($m_is_grad): ?>
          <a href="<?= htmlspecialchars($menu['url']); ?>" class="block py-2 px-3 text-white font-bold text-sm rounded-xl shadow-sm my-1" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
            <?= htmlspecialchars($menu['judul']); ?>
          </a>
        <?php else: ?>
          <a href="<?= htmlspecialchars($menu['url']); ?>" class="block py-2 text-slate-800 font-bold text-sm hover:text-[#1bb28f] border-b border-slate-100">
            <?= htmlspecialchars($menu['judul']); ?>
          </a>
        <?php endif; ?>
      <?php endif; ?>
    <?php endforeach; ?>

    <div class="pt-3 flex flex-col gap-2">
      <a href="cek_status.php" class="w-full text-center py-2.5 font-bold text-xs text-teal-800 bg-teal-50 border border-teal-200 rounded-xl">
        🔍 Cek Status Pendaftaran / Ujian
      </a>
      <a href="daftar.php" class="brand-gear-btn w-full text-center py-3 font-bold text-xs rounded-xl shadow">
        Daftar Mahasiswa Baru Sekarang
      </a>
    </div>
  </div>
</nav>

<script>
function toggleMobileSubmenu(id) {
  var el = document.getElementById(id);
  if (el) {
    el.classList.toggle('hidden');
  }
}
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
  document.getElementById('mobile-menu')?.classList.toggle('hidden');
});
</script>
