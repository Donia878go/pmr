<?php
/**
 * Pop-up Modal Promo & Pengumuman
 * Dinonaktifkan sesuai arahan: Halaman utama bersih dari elemen pop-up interupsi.
 * Pengaturan dapat dinyalakan kembali secara selektif melalui Super Admin jika diperlukan.
 */
if (!empty($site['popup_is_active']) && $site['popup_is_active'] == 1): 
    $pop_title = !empty($site['popup_title']) ? $site['popup_title'] : 'Informasi PMB TA 2026/2027';
    $pop_content = !empty($site['popup_content']) ? $site['popup_content'] : '';
    $pop_cta_text = !empty($site['popup_cta_text']) ? $site['popup_cta_text'] : 'Pelajari Selengkapnya';
    $pop_cta_link = !empty($site['popup_cta_link']) ? $site['popup_cta_link'] : 'daftar.php';
?>
<!-- POPUP ANNOUNCEMENT PROMO MODAL (OFF BY DEFAULT) -->
<div id="popup-promo-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm hidden">
  <div class="relative max-w-lg w-full bg-slate-900 text-white rounded-3xl shadow-2xl border border-teal-500/40 overflow-hidden transform transition-all duration-300">
    <div class="bg-gradient-to-r from-teal-800 to-teal-900 p-6 text-white text-center relative border-b border-teal-700">
      <span class="inline-block px-3 py-1 bg-amber-400 text-slate-950 font-black text-[10px] uppercase tracking-wider rounded-full mb-2">
        Pemberitahuan Khusus PMB
      </span>
      <h3 class="text-xl font-black leading-tight"><?= htmlspecialchars($pop_title); ?></h3>
      <button type="button" onclick="closePopupModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-black/40 hover:bg-black/60 text-white flex items-center justify-center font-bold text-sm transition">
        &times;
      </button>
    </div>

    <div class="p-6 sm:p-8 space-y-5 text-center">
      <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
        <?= nl2br(htmlspecialchars($pop_content)); ?>
      </p>

      <div class="pt-2 flex flex-col sm:flex-row gap-3 justify-center items-center">
        <a href="<?= htmlspecialchars($pop_cta_link); ?>" class="w-full sm:w-auto px-6 py-3 bg-teal-600 hover:bg-amber-400 text-white hover:text-slate-950 font-bold text-xs rounded-xl shadow-lg transition">
          <?= htmlspecialchars($pop_cta_text); ?> &rarr;
        </a>
        <button type="button" onclick="closePopupModal()" class="w-full sm:w-auto px-5 py-3 text-xs font-semibold text-slate-400 hover:text-white">
          Tutup
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function closePopupModal() {
  var m = document.getElementById('popup-promo-modal');
  if (m) {
    m.classList.add('hidden');
    sessionStorage.setItem('pmr_popup_closed', 'true');
  }
}
</script>
<?php endif; ?>
