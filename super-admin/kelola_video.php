<?php
$page_title = "Kelola Galeri Video Kampus";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Tambah Video Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_video') {
    $judul       = trim($_POST['judul'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $video_url   = trim($_POST['video_url'] ?? '');
    $video_type  = $_POST['video_type'] ?? 'youtube';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $urutan      = (int)($_POST['urutan'] ?? 1);

    if (empty($judul) || empty($video_url)) {
        $error = "Judul video dan URL tautan video wajib diisi!";
    } else {
        // Jika diset sebagai featured, reset featured yang lain
        if ($is_featured) {
            $pdo->query("UPDATE video_kampus SET is_featured = 0");
        }
        $stmt = $pdo->prepare("INSERT INTO video_kampus (judul, deskripsi, video_type, video_url, is_featured, urutan) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$judul, $deskripsi, $video_type, $video_url, $is_featured, $urutan])) {
            $message = "Video baru berhasil ditambahkan ke galeri web depan!";
        } else {
            $error = "Gagal menyimpan video.";
        }
    }
}

// Hapus Video
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM video_kampus WHERE id = ?")->execute([$del_id]);
    $message = "Video berhasil dihapus.";
}

// Set Featured Video
if (isset($_GET['set_featured'])) {
    $f_id = (int)$_GET['set_featured'];
    $pdo->query("UPDATE video_kampus SET is_featured = 0");
    $pdo->prepare("UPDATE video_kampus SET is_featured = 1 WHERE id = ?")->execute([$f_id]);
    $message = "Video berhasil dijadikan Video Utama Unggulan di halaman depan!";
}

$videos = $pdo->query("SELECT * FROM video_kampus ORDER BY is_featured DESC, urutan ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Manajemen Video Galeri & Profil Kampus</h2>
      <p class="text-xs text-slate-500">Tambahkan video profil Politeknik Mulia Rokan, tur fasilitas pabrik kelapa sawit mini, dan liputan kegiatan.</p>
    </div>
    <a href="../index.php#video-profil" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Cek Pemutar Video di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="kelola_video.php" class="underline font-bold text-emerald-900">Tutup</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Kolom Kiri: Form Tambah Video -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-black text-slate-900">➕ Tambah Video Baru</h3>
        <p class="text-[11px] text-slate-500">Mendukung tautan embed YouTube atau file video MP4.</p>
      </div>

      <form action="kelola_video.php" method="POST" class="space-y-3.5">
        <input type="hidden" name="action" value="add_video">

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Judul Video *</label>
          <input type="text" name="judul" required placeholder="Contoh: Tur Laboratorium Otomasi Sawit 2026" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Sumber Video</label>
          <select name="video_type" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
            <option value="youtube">YouTube (URL / Embed)</option>
            <option value="mp4">File Video Direct MP4</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">URL Tautan Video *</label>
          <input type="text" name="video_url" required placeholder="https://www.youtube.com/watch?v=... atau https://..." class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          <p class="text-[10px] text-slate-400 mt-1">Cukup masukkan link video YouTube biasa (sistem akan otomatis memprosesnya).</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Ringkas Video</label>
          <textarea name="deskripsi" rows="3" placeholder="Ceritakan isi video kampus ini..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3 items-center">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urutan</label>
            <input type="number" name="urutan" value="<?= count($videos) + 1; ?>" min="1" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
          <div class="pt-5">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
              <input type="checkbox" name="is_featured" value="1" <?= empty($videos) ? 'checked' : ''; ?> class="rounded text-teal-700 focus:ring-teal-700">
              <span>Jadikan Video Utama</span>
            </label>
          </div>
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
          Simpan & Tampilkan Video &rarr;
        </button>
      </form>
    </div>

    <!-- Kolom Kanan: Daftar Video Terpasang -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
        <div>
          <h3 class="text-sm font-black text-slate-900">Daftar Video Kampus (<?= count($videos); ?>)</h3>
          <p class="text-[11px] text-slate-500">Video yang saat ini tampil pada pemutar video landing page.</p>
        </div>
      </div>

      <div class="space-y-4">
        <?php foreach ($videos as $v): ?>
          <div class="p-4 bg-slate-50 rounded-2xl border <?= $v['is_featured'] ? 'border-amber-400 bg-amber-50/30' : 'border-slate-200'; ?> space-y-2">
            <div class="flex justify-between items-start gap-4">
              <div>
                <div class="flex items-center gap-2 mb-1">
                  <?php if ($v['is_featured']): ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black bg-amber-400 text-slate-950 uppercase">
                      ★ Video Utama
                    </span>
                  <?php endif; ?>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">
                    <?= htmlspecialchars($v['video_type']); ?>
                  </span>
                  <span class="text-[10px] text-slate-400 font-bold">Urut #<?= $v['urutan']; ?></span>
                </div>
                <h4 class="text-xs font-black text-slate-900"><?= htmlspecialchars($v['judul']); ?></h4>
                <p class="text-[11px] text-slate-500 font-mono truncate max-w-sm"><?= htmlspecialchars($v['video_url']); ?></p>
              </div>

              <div class="flex items-center gap-1.5 shrink-0 text-xs">
                <?php if (!$v['is_featured']): ?>
                  <a href="kelola_video.php?set_featured=<?= $v['id']; ?>" class="px-2.5 py-1 bg-white hover:bg-amber-100 border border-slate-200 rounded-lg font-bold text-amber-800 text-[10px]">
                    Set Utama
                  </a>
                <?php endif; ?>
                <a href="<?= htmlspecialchars($v['video_url']); ?>" target="_blank" class="p-1 text-teal-700 hover:underline text-xs" title="Tonton">
                  ▶
                </a>
                <a href="kelola_video.php?delete=<?= $v['id']; ?>" onclick="return confirm('Hapus video ini?')" class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg text-xs" title="Hapus">
                  🗑️
                </a>
              </div>
            </div>
            <?php if (!empty($v['deskripsi'])): ?>
              <p class="text-[11px] text-slate-600 line-clamp-2"><?= htmlspecialchars($v['deskripsi']); ?></p>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
