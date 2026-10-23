<?php
$page_title = "Tambah & Kelola Fitur Web Depan";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Tambah Fitur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_fitur') {
    $judul_fitur = trim($_POST['judul_fitur'] ?? '');
    $sub_judul   = trim($_POST['sub_judul'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $icon        = trim($_POST['icon'] ?? '⚡');
    $badge       = trim($_POST['badge'] ?? 'Keunggulan');
    $link_url    = trim($_POST['link_url'] ?? '');
    $urutan      = (int)($_POST['urutan'] ?? 1);
    $image_url   = trim($_POST['image_url_custom'] ?? '');

    // Handle Upload File Logo / Gambar Fitur
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = 'fitur_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $new_name;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $image_url = 'uploads/' . $new_name;
            }
        } else {
            $error = "Format file gambar/logo tidak didukung. Harap unggah PNG, JPG, WEBP, atau SVG.";
        }
    }

    if (empty($judul_fitur) || empty($deskripsi)) {
        $error = "Judul fitur dan deskripsi wajib diisi!";
    } elseif (empty($error)) {
        $stmt = $pdo->prepare("INSERT INTO fitur_unggulan (judul_fitur, sub_judul, deskripsi, icon, image_url, badge, link_url, urutan, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        if ($stmt->execute([$judul_fitur, $sub_judul, $deskripsi, $icon, $image_url, $badge, $link_url, $urutan])) {
            $message = "Fitur baru '{$judul_fitur}' berhasil ditambahkan ke web depan!";
        } else {
            $error = "Gagal menyimpan fitur.";
        }
    }
}

// Edit Fitur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_fitur') {
    $id          = (int)$_POST['id'];
    $judul_fitur = trim($_POST['judul_fitur'] ?? '');
    $sub_judul   = trim($_POST['sub_judul'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $icon        = trim($_POST['icon'] ?? '⚡');
    $badge       = trim($_POST['badge'] ?? 'Keunggulan');
    $link_url    = trim($_POST['link_url'] ?? '');
    $urutan      = (int)($_POST['urutan'] ?? 1);
    $is_active   = isset($_POST['is_active']) ? 1 : 0;
    $image_url   = trim($_POST['image_url_custom'] ?? '');

    // Cek apakah hapus gambar
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '1') {
        $image_url = '';
    } elseif (empty($image_url) && !empty($_POST['existing_image'])) {
        $image_url = trim($_POST['existing_image']);
    }

    // Upload file baru jika ada
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $new_name = 'fitur_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/' . $new_name;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $dest)) {
                $image_url = 'uploads/' . $new_name;
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE fitur_unggulan SET judul_fitur = ?, sub_judul = ?, deskripsi = ?, icon = ?, image_url = ?, badge = ?, link_url = ?, urutan = ?, is_active = ? WHERE id = ?");
    if ($stmt->execute([$judul_fitur, $sub_judul, $deskripsi, $icon, $image_url, $badge, $link_url, $urutan, $is_active, $id])) {
        $message = "Fitur berhasil diperbarui!";
    }
}

// Hapus Fitur
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM fitur_unggulan WHERE id = ?")->execute([$del_id]);
    $message = "Fitur berhasil dihapus dari web depan.";
}

// Data Edit
$edit_fitur = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt_e = $pdo->prepare("SELECT * FROM fitur_unggulan WHERE id = ?");
    $stmt_e->execute([$edit_id]);
    $edit_fitur = $stmt_e->fetch();
}

$fitur_list = $pdo->query("SELECT * FROM fitur_unggulan ORDER BY urutan ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Manajemen Fitur & Keunggulan Web Depan</h2>
      <p class="text-xs text-slate-500">Tambahkan kartu fitur terobosan baru, unggah logo/gambar atau pilih ikon, yang langsung tampil di beranda utama.</p>
    </div>
    <a href="../index.php#fitur-unggulan" target="_blank" class="px-4 py-2 text-white rounded-xl text-xs font-bold shadow hover:brightness-110 transition flex items-center gap-1.5" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
      <span>🌐 Lihat Fitur di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="kelola_fitur.php" class="underline font-bold text-emerald-900">Tutup</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Kolom Kiri: Form Tambah / Edit -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
          <span><?= $edit_fitur ? '✏️ Edit Fitur Kampus' : '➕ Tambah Fitur Baru di Web Utama'; ?></span>
        </h3>
        <p class="text-[11px] text-slate-500">
          <?= $edit_fitur ? 'Sesuaikan logo, gambar, ikon, dan detail kartu fitur.' : 'Kartu baru akan otomatis muncul pada seksi Keunggulan di web depan.'; ?>
        </p>
      </div>

      <form action="kelola_fitur.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="action" value="<?= $edit_fitur ? 'edit_fitur' : 'add_fitur'; ?>">
        <?php if ($edit_fitur): ?>
          <input type="hidden" name="id" value="<?= $edit_fitur['id']; ?>">
          <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_fitur['image_url'] ?? ''); ?>">
        <?php endif; ?>

        <!-- Input Gambar / Logo Fitur -->
        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-3">
          <label class="block text-xs font-bold text-slate-800 flex items-center justify-between">
            <span>🖼️ Unggah Logo / Gambar / Icon Fitur</span>
            <span class="text-[10px] text-[#1bb28f] font-bold">Format PNG / SVG / JPG</span>
          </label>

          <?php if (!empty($edit_fitur['image_url'])): ?>
            <div class="flex items-center gap-3 p-3 bg-white rounded-2xl border border-slate-200 shadow-xs">
              <div class="w-14 h-14 rounded-full bg-white p-2 border-2 border-[#1bb28f] flex items-center justify-center shrink-0 shadow-sm">
                <img src="../<?= htmlspecialchars($edit_fitur['image_url']); ?>" alt="Logo Fitur" class="w-full h-full object-contain">
              </div>
              <div class="text-xs truncate flex-1">
                <span class="block font-bold text-slate-800 text-[11px]">Logo/Gambar Aktif:</span>
                <span class="text-[10px] text-slate-500 font-mono block truncate"><?= htmlspecialchars($edit_fitur['image_url']); ?></span>
                <span class="text-[10px] text-emerald-600 font-bold block">✓ Tampil Bulat Latar Putih di Web Depan</span>
              </div>
              <label class="text-[11px] text-rose-600 font-bold flex items-center gap-1 cursor-pointer bg-rose-50 px-2 py-1 rounded-lg">
                <input type="checkbox" name="remove_image" value="1" class="rounded text-rose-600">
                <span>Hapus</span>
              </label>
            </div>
          <?php endif; ?>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Berkas Icon Baru dari Komputer:</label>
            <input type="file" name="image_file" accept=".png,.jpg,.jpeg,.webp,.svg" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1bb28f]/10 file:text-[#1bb28f] hover:file:bg-[#1bb28f]/20 cursor-pointer">
            <p class="text-[10px] text-slate-400 mt-1">Otomatis ditampilkan dalam bingkai bulat dengan latar putih bersih.</p>
          </div>

          <div>
            <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Atau URL Gambar Eksternal / Aset:</label>
            <input type="text" name="image_url_custom" value="<?= htmlspecialchars($edit_fitur['image_url'] ?? ''); ?>" placeholder="assets/images/icons/icon-factory.svg" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
          </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Ikon / Emoji Fallback *</label>
            <input type="text" id="icon_input" name="icon" value="<?= ($edit_fitur['icon'] ?? '⚡') === '??' ? '⚡' : htmlspecialchars($edit_fitur['icon'] ?? '⚡'); ?>" required placeholder="🏭" class="w-full px-3 py-2 text-center text-lg border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
          </div>
          <div class="col-span-2">
            <label class="block text-xs font-bold text-slate-700 mb-1">Badge Label</label>
            <input type="text" name="badge" value="<?= htmlspecialchars($edit_fitur['badge'] ?? 'Unggulan'); ?>" placeholder="Link & Match / Standar BNSP" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
          </div>
        </div>

        <!-- Preset Icon Cepat -->
        <div>
          <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Klik Cepat Pilihan Preset Ikon:</label>
          <div class="flex flex-wrap gap-1">
            <?php 
            $presets = [
              '🏭' => 'Pabrik', '🤝' => 'Magang', '📜' => 'Sertifikat', '🎓' => 'Beasiswa',
              '💻' => 'IoT', '🏢' => 'Asrama', '⚡' => 'Energi', '🧪' => 'Lab', '⚙️' => 'Mesin', '🚜' => 'Sawit'
            ];
            foreach ($presets as $emoji => $label): ?>
              <button type="button" onclick="document.getElementById('icon_input').value = '<?= $emoji; ?>'" class="px-2 py-1 rounded-lg border border-slate-200 bg-slate-50 hover:bg-[#1bb28f]/10 hover:border-[#1bb28f] text-xs flex items-center gap-1 font-medium transition" title="<?= $label; ?>">
                <span><?= $emoji; ?></span>
                <span class="text-[10px] text-slate-600"><?= $label; ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Judul Fitur Utama *</label>
          <input type="text" name="judul_fitur" value="<?= htmlspecialchars($edit_fitur['judul_fitur'] ?? ''); ?>" required placeholder="Contoh: Teaching Factory Mini Pabrik PKS" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Sub-Judul / Tagline Fitur</label>
          <input type="text" name="sub_judul" value="<?= htmlspecialchars($edit_fitur['sub_judul'] ?? ''); ?>" placeholder="Contoh: PKS Berbasis Digital SCADA Otomasi" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Lengkap Fitur *</label>
          <textarea name="deskripsi" rows="3" required placeholder="Jelaskan keistimewaan fitur ini bagi calon mahasiswa..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]"><?= htmlspecialchars($edit_fitur['deskripsi'] ?? ''); ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tautan URL (Opsional)</label>
            <input type="text" name="link_url" value="<?= htmlspecialchars($edit_fitur['link_url'] ?? ''); ?>" placeholder="index.php#fasilitas" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urutan</label>
            <input type="number" name="urutan" value="<?= htmlspecialchars($edit_fitur['urutan'] ?? (count($fitur_list) + 1)); ?>" min="1" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1bb28f]">
          </div>
        </div>

        <?php if ($edit_fitur): ?>
          <div class="pt-1">
            <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
              <input type="checkbox" name="is_active" value="1" <?= $edit_fitur['is_active'] ? 'checked' : ''; ?> class="rounded text-[#1bb28f] focus:ring-[#1bb28f]">
              <span>Status Aktif Tampil di Beranda</span>
            </label>
          </div>
        <?php endif; ?>

        <div class="pt-2 flex justify-between items-center">
          <?php if ($edit_fitur): ?>
            <a href="kelola_fitur.php" class="text-xs font-bold text-slate-500 hover:text-slate-800">
              &larr; Batal
            </a>
          <?php else: ?>
            <span></span>
          <?php endif; ?>
          <button type="submit" class="px-6 py-2.5 text-white font-bold text-xs rounded-xl shadow hover:brightness-110 transition" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
            <?= $edit_fitur ? 'Perbarui Fitur &rarr;' : 'Simpan & Publikasikan &rarr;'; ?>
          </button>
        </div>

      </form>
    </div>

    <!-- Kolom Kanan: Daftar Fitur Terpasang -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
        <div>
          <h3 class="text-sm font-black text-slate-900">Daftar Fitur Aktif di Web Depan</h3>
          <p class="text-[11px] text-slate-500">Semua kartu keunggulan yang dapat dibaca pengunjung di landing page.</p>
        </div>
        <span class="text-xs font-bold text-[#1bb28f] bg-[#1bb28f]/10 px-2.5 py-1 rounded-lg">
          <?= count($fitur_list); ?> Fitur
        </span>
      </div>

      <div class="space-y-3">
        <?php foreach ($fitur_list as $f): ?>
          <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 hover:border-[#1bb28f] transition flex items-start justify-between gap-4">
            <div class="flex items-start gap-3">
              <!-- Thumbnail Logo / Gambar atau Icon (BULAT & LATAR PUTIH) -->
              <div class="w-12 h-12 rounded-full bg-white p-2 shadow-sm border-2 border-slate-200 shrink-0 flex items-center justify-center overflow-hidden">
                <?php if (!empty($f['image_url'])): ?>
                  <img src="../<?= htmlspecialchars($f['image_url']); ?>" alt="Logo" class="w-full h-full object-contain">
                <?php else: ?>
                  <span class="text-xl"><?= ($f['icon'] === '??' || empty($f['icon'])) ? '⚡' : htmlspecialchars($f['icon']); ?></span>
                <?php endif; ?>
              </div>

              <div>
                <div class="flex items-center gap-2 mb-0.5">
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#e9b35d]/20 text-[#d99f43] uppercase">
                    <?= htmlspecialchars($f['badge']); ?>
                  </span>
                  <span class="text-[10px] text-slate-400 font-bold">Urut #<?= $f['urutan']; ?></span>
                  <?php if (!empty($f['image_url'])): ?>
                    <span class="text-[9px] bg-teal-50 text-teal-700 px-1.5 py-0.2 rounded border border-teal-200 font-semibold">Ada Logo/Gambar</span>
                  <?php endif; ?>
                </div>
                <h4 class="text-xs font-black text-slate-900"><?= htmlspecialchars($f['judul_fitur']); ?></h4>
                <?php if (!empty($f['sub_judul'])): ?>
                  <span class="text-[10px] text-[#1bb28f] font-semibold block"><?= htmlspecialchars($f['sub_judul']); ?></span>
                <?php endif; ?>
                <p class="text-[11px] text-slate-600 mt-1 line-clamp-2"><?= htmlspecialchars($f['deskripsi']); ?></p>
              </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0 text-xs">
              <a href="kelola_fitur.php?edit=<?= $f['id']; ?>" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg font-bold text-slate-700 text-[11px]">
                Edit
              </a>
              <a href="kelola_fitur.php?delete=<?= $f['id']; ?>" onclick="return confirm('Hapus fitur ini dari web depan?')" class="p-1 text-rose-600 hover:bg-rose-50 rounded-lg text-xs" title="Hapus">
                🗑️
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
