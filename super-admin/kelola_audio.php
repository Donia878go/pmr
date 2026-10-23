<?php
$page_title = "Kelola Musik & Audio Kampus";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audio_title     = trim($_POST['audio_title'] ?? 'Mars Politeknik Mulia Rokan');
    $audio_url_input = trim($_POST['audio_url'] ?? '');
    $audio_is_active = isset($_POST['audio_is_active']) ? 1 : 0;

    $final_audio_url = $audio_url_input ?: ($site['audio_url'] ?? 'assets/audio/mars-politeknik.mp3');

    // Handle Upload File Audio MP3 jika ada
    if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['audio_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
        if (in_array($ext, $allowed)) {
            $dest_dir = __DIR__ . '/../assets/audio/';
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0777, true);
            }
            $new_name = 'audio_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $dest_dir . $new_name)) {
                $final_audio_url = 'assets/audio/' . $new_name;
            }
        } else {
            $error = "Format file audio harus berekstensi MP3, WAV, atau OGG.";
        }
    }

    if (empty($error)) {
        $stmt_up = $pdo->prepare("UPDATE site_settings SET audio_title = ?, audio_url = ?, audio_is_active = ? WHERE id = 1");
        if ($stmt_up->execute([$audio_title, $final_audio_url, $audio_is_active])) {
            $message = "Pengaturan pemutar musik kampus berhasil disimpan!";
            $site = getSiteSettings($pdo);
        } else {
            $error = "Gagal menyimpan audio.";
        }
    }
}

$curr_audio_url = !empty($site['audio_url']) ? $site['audio_url'] : 'assets/audio/mars-politeknik.mp3';
?>

<div class="max-w-4xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Manajemen Musik & Audio Kampus (Mars / Hymne)</h2>
      <p class="text-xs text-slate-500">Atur lagu mars kampus, hymne, atau musik tema yang dapat didengarkan pengunjung melalui pemutar mengambang di web depan.</p>
    </div>
    <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Uji Pemutar di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="../index.php" target="_blank" class="underline font-bold text-emerald-900">Dengarkan di Web Depan &rarr;</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
    
    <!-- Player Preview Card -->
    <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 p-6 rounded-2xl text-white border border-teal-500/30 flex flex-col sm:flex-row justify-between items-center gap-4">
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-amber-400 text-slate-950 flex items-center justify-center font-black text-2xl shadow-lg">
          🎵
        </div>
        <div>
          <span class="text-[10px] font-black uppercase text-amber-300 tracking-wider">Pratinjau Pemutar Musik</span>
          <h4 class="text-base font-black text-white"><?= htmlspecialchars($site['audio_title'] ?? 'Mars Politeknik Mulia Rokan'); ?></h4>
          <span class="text-[11px] text-teal-200/70 font-mono"><?= htmlspecialchars($curr_audio_url); ?></span>
        </div>
      </div>

      <div class="w-full sm:w-auto">
        <audio controls class="w-full sm:w-64 h-10 rounded-xl">
          <source src="../<?= htmlspecialchars($curr_audio_url); ?>" type="audio/mpeg">
          Browser tidak mendukung audio player.
        </audio>
      </div>
    </div>

    <!-- Form Pengaturan Audio -->
    <form action="kelola_audio.php" method="POST" enctype="multipart/form-data" class="space-y-5">
      
      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Judul Lagu / Musik Kampus *</label>
        <input type="text" name="audio_title" value="<?= htmlspecialchars($site['audio_title'] ?? 'Mars Politeknik Mulia Rokan'); ?>" required class="w-full px-4 py-2.5 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Unggah File Audio Baru (MP3 / WAV)</label>
        <input type="file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 cursor-pointer">
        <p class="text-[11px] text-slate-400 mt-1">Format: MP3, WAV, atau OGG. Ukuran maksimal 15 MB.</p>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Atau Gunakan Tautan URL Audio Eksternal (Online Stream)</label>
        <input type="text" name="audio_url" value="<?= htmlspecialchars($site['audio_url'] ?? ''); ?>" placeholder="https://domain.com/lagu-mars.mp3" class="w-full px-4 py-2.5 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        <p class="text-[10px] text-slate-400 mt-1">Jika tidak mengunggah file di atas, Anda dapat mengisi link direct audio MP3 dari server lain.</p>
      </div>

      <div class="pt-2">
        <label class="flex items-center gap-2.5 text-xs font-bold text-slate-800 cursor-pointer p-3 bg-slate-50 rounded-xl border border-slate-200">
          <input type="checkbox" name="audio_is_active" value="1" <?= (!empty($site['audio_is_active'])) ? 'checked' : ''; ?> class="rounded text-teal-700 focus:ring-teal-700 w-4 h-4">
          <div>
            <span class="block">Aktifkan Pemutar Musik Mengambang di Web Depan</span>
            <span class="text-[10px] text-slate-500 font-normal">Jika dicentang, widget audio interaktif akan muncul di pojok kiri bawah website publik.</span>
          </div>
        </label>
      </div>

      <div class="pt-3 border-t border-slate-100 flex justify-end">
        <button type="submit" class="px-8 py-3 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow-lg transition">
          Simpan Pengaturan Musik Audio &rarr;
        </button>
      </div>

    </form>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
