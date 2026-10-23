<?php
$page_title = "Kelola Banner Pop-up Modal Promo";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $popup_title     = trim($_POST['popup_title'] ?? '');
    $popup_content   = trim($_POST['popup_content'] ?? '');
    $popup_cta_text  = trim($_POST['popup_cta_text'] ?? 'Daftar Sekarang');
    $popup_cta_link  = trim($_POST['popup_cta_link'] ?? 'daftar.php');
    $popup_is_active = isset($_POST['popup_is_active']) ? 1 : 0;

    $stmt_up = $pdo->prepare("UPDATE site_settings SET popup_title = ?, popup_content = ?, popup_cta_text = ?, popup_cta_link = ?, popup_is_active = ? WHERE id = 1");
    if ($stmt_up->execute([$popup_title, $popup_content, $popup_cta_text, $popup_cta_link, $popup_is_active])) {
        $message = "Pengaturan banner pop-up modal berhasil diperbarui!";
        $site = getSiteSettings($pdo);
    } else {
        $error = "Gagal menyimpan pengaturan pop-up.";
    }
}
?>

<div class="max-w-4xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Manajemen Banner Pop-up Modal Promo</h2>
      <p class="text-xs text-slate-500">Banner pengumuman otomatis yang muncul menyapa pengunjung saat membuka website utama.</p>
    </div>
    <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Uji Pop-up di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="../index.php" target="_blank" class="underline font-bold text-emerald-900">Buka Web Depan &rarr;</a>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
    <form action="kelola_popup.php" method="POST" class="space-y-4">
      
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pop-up Modal *</label>
        <input type="text" name="popup_title" value="<?= htmlspecialchars($site['popup_title'] ?? 'Beasiswa Penuh 100% Dibuka!'); ?>" required class="w-full px-4 py-2.5 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pesan Pengumuman *</label>
        <textarea name="popup_content" rows="4" required class="w-full px-4 py-2.5 text-xs leading-relaxed border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['popup_content'] ?? ''); ?></textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Teks Tombol Aksi (CTA)</label>
          <input type="text" name="popup_cta_text" value="<?= htmlspecialchars($site['popup_cta_text'] ?? 'Daftar Sekarang'); ?>" class="w-full px-4 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tautan URL Tombol</label>
          <input type="text" name="popup_cta_link" value="<?= htmlspecialchars($site['popup_cta_link'] ?? 'daftar.php'); ?>" class="w-full px-4 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>
      </div>

      <div class="pt-2">
        <label class="flex items-center gap-2.5 text-xs font-bold text-slate-800 cursor-pointer p-3 bg-slate-50 rounded-xl border border-slate-200">
          <input type="checkbox" name="popup_is_active" value="1" <?= (!empty($site['popup_is_active'])) ? 'checked' : ''; ?> class="rounded text-teal-700 focus:ring-teal-700 w-4 h-4">
          <div>
            <span class="block">Aktifkan Banner Pop-up Modal di Halaman Depan</span>
            <span class="text-[10px] text-slate-500 font-normal">Pop-up akan muncul otomatis saat calon mahasiswa baru pertama kali mengunjungi website.</span>
          </div>
        </label>
      </div>

      <div class="pt-3 border-t border-slate-100 flex justify-end">
        <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow-lg transition">
          Simpan Banner Pop-up &rarr;
        </button>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
