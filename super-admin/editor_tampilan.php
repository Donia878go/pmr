<?php
$page_title = "Editor Tulisan & Banner Depan";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_badge              = trim($_POST['hero_badge'] ?? '');
    $hero_title              = trim($_POST['hero_title'] ?? '');
    $hero_subtitle           = trim($_POST['hero_subtitle'] ?? '');
    $hero_cta_text           = trim($_POST['hero_cta_text'] ?? '');
    $hero_cta_link           = trim($_POST['hero_cta_link'] ?? '');
    $sambutan_direktur_nama  = trim($_POST['sambutan_direktur_nama'] ?? '');
    $sambutan_direktur_jabatan = trim($_POST['sambutan_direktur_jabatan'] ?? '');
    $sambutan_direktur_teks  = trim($_POST['sambutan_direktur_teks'] ?? '');
    $visi_teks               = trim($_POST['visi_teks'] ?? '');
    $misi_teks               = trim($_POST['misi_teks'] ?? '');

    $stmt_up = $pdo->prepare("UPDATE site_settings SET 
        hero_badge = ?, hero_title = ?, hero_subtitle = ?,
        hero_cta_text = ?, hero_cta_link = ?,
        sambutan_direktur_nama = ?, sambutan_direktur_jabatan = ?, sambutan_direktur_teks = ?,
        visi_teks = ?, misi_teks = ?
        WHERE id = 1");

    $res = $stmt_up->execute([
        $hero_badge, $hero_title, $hero_subtitle,
        $hero_cta_text, $hero_cta_link,
        $sambutan_direktur_nama, $sambutan_direktur_jabatan, $sambutan_direktur_teks,
        $visi_teks, $misi_teks
    ]);

    if ($res) {
        $message = "Seluruh tulisan dan konten teks halaman depan berhasil diperbarui!";
        $site = getSiteSettings($pdo);
    } else {
        $error = "Terjadi kegagalan saat menyimpan teks.";
    }
}
?>

<div class="max-w-5xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Editor Tulisan, Hero Banner & Visi Misi</h2>
      <p class="text-xs text-slate-500">Sesuaikan kalimat promosi utama, sambutan pimpinan, dan visi misi kampus.</p>
    </div>
    <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Pratinjau Teks di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= $message; ?></span>
      <a href="../index.php" target="_blank" class="underline font-bold text-emerald-900">Lihat Hasil Langsung &rarr;</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= $error; ?>
    </div>
  <?php endif; ?>

  <form action="editor_tampilan.php" method="POST" class="space-y-6">

    <!-- Card 1: Hero Section Copywriting -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>📢</span>
        <span>1. Banner Utama (Hero Section Copywriting)</span>
      </h3>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Teks Badge / Label Kecil di Atas Judul</label>
          <input type="text" name="hero_badge" value="<?= htmlspecialchars($site['hero_badge'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Judul Utama Halaman Depan (Headline Utama) *</label>
          <input type="text" name="hero_title" value="<?= htmlspecialchars($site['hero_title'] ?? ''); ?>" required class="w-full px-4 py-3 text-sm font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Sub-Judul / Paragraf Penjelasan Singkat *</label>
          <textarea name="hero_subtitle" rows="3" required class="w-full px-4 py-2.5 text-xs leading-relaxed border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['hero_subtitle'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Teks Tombol Aksi Utama (Call to Action)</label>
            <input type="text" name="hero_cta_text" value="<?= htmlspecialchars($site['hero_cta_text'] ?? 'Daftar PMB Sekarang'); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tautan / Link Tombol Aksi</label>
            <input type="text" name="hero_cta_link" value="<?= htmlspecialchars($site['hero_cta_link'] ?? 'daftar.php'); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Sambutan Direktur Kampus -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>👨‍💼</span>
        <span>2. Sambutan Pimpinan / Direktur Perguruan Tinggi</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap & Gelar Direktur</label>
          <input type="text" name="sambutan_direktur_nama" value="<?= htmlspecialchars($site['sambutan_direktur_nama'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan Resmi</label>
          <input type="text" name="sambutan_direktur_jabatan" value="<?= htmlspecialchars($site['sambutan_direktur_jabatan'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div class="sm:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Teks Lengkap Sambutan Direktur</label>
          <textarea name="sambutan_direktur_teks" rows="4" class="w-full px-4 py-2.5 text-xs leading-relaxed border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['sambutan_direktur_teks'] ?? ''); ?></textarea>
        </div>
      </div>
    </div>

    <!-- Card 3: Visi & Misi Kampus -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>🎯</span>
        <span>3. Visi & Misi Perguruan Tinggi</span>
      </h3>

      <div class="space-y-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Rumusan Visi Perguruan Tinggi</label>
          <textarea name="visi_teks" rows="3" class="w-full px-4 py-2.5 text-xs leading-relaxed border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['visi_teks'] ?? ''); ?></textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Poin-Poin Misi Perguruan Tinggi</label>
          <textarea name="misi_teks" rows="5" class="w-full px-4 py-2.5 text-xs leading-relaxed border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['misi_teks'] ?? ''); ?></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Gunakan angka urut (1., 2., dst) atau baris baru untuk memisahkan setiap butir misi.</p>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-end gap-4 pt-2">
      <a href="index.php" class="px-5 py-3 text-xs font-bold text-slate-600 hover:text-slate-900">
        Batal
      </a>
      <button type="submit" class="px-8 py-3.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
        Simpan Perubahan Tulisan &rarr;
      </button>
    </div>

  </form>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
