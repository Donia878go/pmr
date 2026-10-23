<?php
$page_title = "Kelola & Tambah Logo Sosial Media";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Tambah Sosmed Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_sosmed') {
    $platform   = strtolower(trim($_POST['platform'] ?? ''));
    $nama_label = trim($_POST['nama_label'] ?? '');
    $url        = trim($_POST['url'] ?? '');
    $urutan     = (int)($_POST['urutan'] ?? 1);
    $icon       = $platform;

    // Upload custom icon/logo image jika ada
    if (isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['icon_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'svg', 'jpg', 'jpeg', 'webp'];
        if (in_array($ext, $allowed)) {
            $icon_name = 'soc_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/' . $icon_name;
            if (move_uploaded_file($_FILES['icon_file']['tmp_name'], $dest)) {
                $icon = 'assets/images/' . $icon_name;
            }
        }
    }

    if (empty($nama_label) || empty($url)) {
        $error = "Nama label dan URL akun sosial media wajib diisi!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO social_media (platform, nama_label, url, icon, is_active, urutan) VALUES (?, ?, ?, ?, 1, ?)");
        if ($stmt->execute([$platform, $nama_label, $url, $icon, $urutan])) {
            $message = "Akun sosial media '{$nama_label}' berhasil ditambahkan ke web depan!";
        } else {
            $error = "Gagal menyimpan akun sosial media.";
        }
    }
}

// Edit Sosmed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_sosmed') {
    $id         = (int)$_POST['id'];
    $url        = trim($_POST['url'] ?? '');
    $nama_label = trim($_POST['nama_label'] ?? '');
    $is_active  = isset($_POST['is_active']) ? 1 : 0;
    $urutan     = (int)($_POST['urutan'] ?? 1);

    // Ambil data lama
    $curr_soc = $pdo->prepare("SELECT icon FROM social_media WHERE id = ?");
    $curr_soc->execute([$id]);
    $icon = $curr_soc->fetchColumn() ?: 'globe';

    if (isset($_FILES['icon_file']) && $_FILES['icon_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['icon_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['png', 'svg', 'jpg', 'jpeg', 'webp'];
        if (in_array($ext, $allowed)) {
            $icon_name = 'soc_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/' . $icon_name;
            if (move_uploaded_file($_FILES['icon_file']['tmp_name'], $dest)) {
                $icon = 'assets/images/' . $icon_name;
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE social_media SET url = ?, nama_label = ?, icon = ?, is_active = ?, urutan = ? WHERE id = ?");
    if ($stmt->execute([$url, $nama_label, $icon, $is_active, $urutan, $id])) {
        $message = "Akun sosial media berhasil diperbarui!";
    }
}

// Hapus Sosmed
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM social_media WHERE id = ?")->execute([$del_id]);
    $message = "Akun sosial media berhasil dihapus.";
}

$socials = $pdo->query("SELECT * FROM social_media ORDER BY urutan ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Manajemen Logo & Tautan Sosial Media</h2>
      <p class="text-xs text-slate-500">Tambahkan platform baru, unggah logo ikon sosial media kustom, dan atur tautan resmi kampus.</p>
    </div>
    <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Cek Ikon di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="kelola_sosmed.php" class="underline font-bold text-emerald-900">Tutup</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Kolom Kiri: Form Tambah Sosmed Baru -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-black text-slate-900">➕ Tambah Akun / Logo Sosmed Baru</h3>
        <p class="text-[11px] text-slate-500">Bisa memilih platform populer atau mengunggah logo gambar sendiri.</p>
      </div>

      <form action="kelola_sosmed.php" method="POST" enctype="multipart/form-data" class="space-y-3.5">
        <input type="hidden" name="action" value="add_sosmed">

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Platform Media Sosial *</label>
          <select name="platform" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white font-semibold">
            <option value="instagram">Instagram</option>
            <option value="facebook">Facebook</option>
            <option value="youtube">YouTube</option>
            <option value="tiktok">TikTok</option>
            <option value="whatsapp">WhatsApp Helpdesk</option>
            <option value="telegram">Telegram Official</option>
            <option value="linkedin">LinkedIn</option>
            <option value="twitter">X / Twitter</option>
            <option value="threads">Threads</option>
            <option value="spotify">Spotify Podcast Kampus</option>
            <option value="other">Platform Lainnya (Kustom)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Label Nama Tampilan *</label>
          <input type="text" name="nama_label" required placeholder="Contoh: Instagram Resmi @politeknikmuliarokan" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">URL / Tautan Profil Akun *</label>
          <input type="text" name="url" required placeholder="https://instagram.com/..." class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Gambar Logo / Ikon Kustom (Opsional)</label>
          <input type="file" name="icon_file" accept=".png,.svg,.jpg,.webp" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 cursor-pointer">
          <p class="text-[10px] text-slate-400 mt-1">Jika dikosongkan, sistem akan otomatis menggunakan ikon bawaan dari platform yang dipilih.</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= count($socials) + 1; ?>" min="1" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
          Simpan Logo Sosial Media &rarr;
        </button>
      </form>
    </div>

    <!-- Kolom Kanan: Daftar Sosmed Terdaftar -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
        <div>
          <h3 class="text-sm font-black text-slate-900">Daftar Akun Sosial Media (<?= count($socials); ?>)</h3>
          <p class="text-[11px] text-slate-500">Tampil di bilah navigasi atas dan bagian footer identitas website.</p>
        </div>
      </div>

      <div class="space-y-3">
        <?php foreach ($socials as $soc): ?>
          <form action="kelola_sosmed.php" method="POST" enctype="multipart/form-data" class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
            <input type="hidden" name="action" value="edit_sosmed">
            <input type="hidden" name="id" value="<?= $soc['id']; ?>">

            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2.5">
                <?php if (strpos($soc['icon'], 'assets/images/') !== false): ?>
                  <img src="../<?= htmlspecialchars($soc['icon']); ?>" alt="Icon" class="w-6 h-6 object-contain rounded-md bg-white p-0.5 border">
                <?php else: ?>
                  <span class="w-6 h-6 rounded-md bg-teal-800 text-white text-[11px] font-black flex items-center justify-center uppercase">
                    <?= substr($soc['platform'], 0, 2); ?>
                  </span>
                <?php endif; ?>
                <span class="text-xs font-black text-slate-900 uppercase"><?= htmlspecialchars($soc['platform']); ?></span>
              </div>

              <a href="kelola_sosmed.php?delete=<?= $soc['id']; ?>" onclick="return confirm('Hapus sosial media ini?')" class="text-rose-600 hover:underline font-bold text-[11px]">
                🗑️ Hapus
              </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Label Nama</label>
                <input type="text" name="nama_label" value="<?= htmlspecialchars($soc['nama_label']); ?>" required class="w-full px-3 py-1.5 text-xs font-bold border border-slate-300 rounded-lg bg-white">
              </div>
              <div>
                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">Urutan</label>
                <input type="number" name="urutan" value="<?= $soc['urutan']; ?>" min="1" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">
              </div>
            </div>

            <div>
              <label class="block text-[10px] font-bold text-slate-500 uppercase mb-0.5">URL / Tautan Akun</label>
              <input type="text" name="url" value="<?= htmlspecialchars($soc['url']); ?>" required class="w-full px-3 py-1.5 text-xs font-mono border border-slate-300 rounded-lg bg-white">
            </div>

            <div class="flex justify-between items-center pt-1 border-t border-slate-200">
              <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" <?= $soc['is_active'] ? 'checked' : ''; ?> class="rounded text-teal-700 focus:ring-teal-700">
                <span>Tampil Aktif</span>
              </label>
              <button type="submit" class="px-3.5 py-1.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-lg shadow">
                Perbarui
              </button>
            </div>
          </form>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
