<?php
$page_title = "Menu & Dropdown Builder";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Tambah Menu Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_menu') {
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $judul     = trim($_POST['judul'] ?? '');
    $url       = trim($_POST['url'] ?? '');
    $target    = $_POST['target'] ?? '_self';
    $urutan    = (int)($_POST['urutan'] ?? 1);
    $is_gradient = isset($_POST['is_gradient']) ? 1 : 0;

    if (empty($judul) || empty($url)) {
        $error = "Judul menu dan tautan URL wajib diisi!";
    } else {
        $stmt_ins = $pdo->prepare("INSERT INTO custom_menus (parent_id, judul, url, target, urutan, is_active, is_gradient) VALUES (?, ?, ?, ?, ?, 1, ?)");
        if ($stmt_ins->execute([$parent_id, $judul, $url, $target, $urutan, $is_gradient])) {
            $message = "Menu baru '{$judul}' berhasil ditambahkan ke navigasi!";
        } else {
            $error = "Gagal menambahkan menu baru.";
        }
    }
}

// Update Menu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_menu') {
    $id        = (int)$_POST['id'];
    $parent_id = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
    $judul     = trim($_POST['judul'] ?? '');
    $url       = trim($_POST['url'] ?? '');
    $target    = $_POST['target'] ?? '_self';
    $urutan    = (int)($_POST['urutan'] ?? 1);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $is_gradient = isset($_POST['is_gradient']) ? 1 : 0;

    if (empty($judul) || empty($url)) {
        $error = "Judul dan URL tidak boleh kosong!";
    } else {
        $stmt_up = $pdo->prepare("UPDATE custom_menus SET parent_id = ?, judul = ?, url = ?, target = ?, urutan = ?, is_active = ?, is_gradient = ? WHERE id = ?");
        if ($stmt_up->execute([$parent_id, $judul, $url, $target, $urutan, $is_active, $is_gradient, $id])) {
            $message = "Menu berhasil diperbarui!";
        } else {
            $error = "Gagal memperbarui menu.";
        }
    }
}

// Hapus Menu
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    // Hapus juga submenunya jika ada
    $pdo->prepare("DELETE FROM custom_menus WHERE parent_id = ?")->execute([$del_id]);
    $stmt_del = $pdo->prepare("DELETE FROM custom_menus WHERE id = ?");
    if ($stmt_del->execute([$del_id])) {
        $message = "Menu dan seluruh submenunya berhasil dihapus.";
    }
}

// Ambil Menu Induk (Parent)
$parents = $pdo->query("SELECT * FROM custom_menus WHERE parent_id IS NULL ORDER BY urutan ASC")->fetchAll();

// Ambil Seluruh Menu dengan Children
$all_menus = getNavMenus($pdo);

// Cek jika sedang Edit
$edit_menu = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt_e = $pdo->prepare("SELECT * FROM custom_menus WHERE id = ?");
    $stmt_e->execute([$edit_id]);
    $edit_menu = $stmt_e->fetch();
}
?>

<div class="max-w-6xl mx-auto space-y-8">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Manajemen Navigasi Menu & Dropdown</h2>
      <p class="text-xs text-slate-500">Atur menu bar utama, buat menu dropdown bertingkat, dan tautkan ke halaman mana saja.</p>
    </div>
    <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Cek Menu di Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="menu_builder.php" class="underline font-bold text-emerald-900">Tutup Notifikasi</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <!-- Kolom Kiri: Form Tambah / Edit Menu -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-5">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
          <span><?= $edit_menu ? '✏️ Edit Menu' : '➕ Tambah Menu / Submenu Baru'; ?></span>
        </h3>
        <p class="text-[11px] text-slate-500">
          <?= $edit_menu ? 'Perbarui informasi tautan menu terpilih.' : 'Pilih Menu Utama jika ingin membuat dropdown di bawahnya.'; ?>
        </p>
      </div>

      <form action="menu_builder.php" method="POST" class="space-y-4">
        <input type="hidden" name="action" value="<?= $edit_menu ? 'edit_menu' : 'add_menu'; ?>">
        <?php if ($edit_menu): ?>
          <input type="hidden" name="id" value="<?= $edit_menu['id']; ?>">
        <?php endif; ?>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Posisi Menu *</label>
          <select name="parent_id" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
            <option value="">-- Menu Utama (Baris Teratas) --</option>
            <?php foreach ($parents as $p): ?>
              <?php if (!$edit_menu || $edit_menu['id'] !== $p['id']): ?>
                <option value="<?= $p['id']; ?>" <?= ($edit_menu && $edit_menu['parent_id'] == $p['id']) ? 'selected' : ''; ?>>
                  ↳ Submenu di bawah: <?= htmlspecialchars($p['judul']); ?>
                </option>
              <?php endif; ?>
            <?php endforeach; ?>
          </select>
          <p class="text-[10px] text-slate-400 mt-1">Pilih "Menu Utama" untuk menu di bar depan, atau pilih salah satu menu induk untuk menjadikannya item dropdown.</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Judul / Label Menu *</label>
          <input type="text" name="judul" value="<?= htmlspecialchars($edit_menu['judul'] ?? ''); ?>" required placeholder="Contoh: Beasiswa Sawit" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tautan / Target URL *</label>
          <input type="text" name="url" value="<?= htmlspecialchars($edit_menu['url'] ?? ''); ?>" required placeholder="Contoh: index.php#biaya atau form.php?slug=..." class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          <p class="text-[10px] text-slate-400 mt-1">Bisa berupa anchor (`index.php#profil`), link halaman (`daftar.php`), link formulir (`form.php?slug=...`), atau `#` jika hanya pembungkus dropdown.</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Target Buka</label>
            <select name="target" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <option value="_self" <?= ($edit_menu && $edit_menu['target'] === '_self') ? 'selected' : ''; ?>>Halaman Sama (_self)</option>
              <option value="_blank" <?= ($edit_menu && $edit_menu['target'] === '_blank') ? 'selected' : ''; ?>>Tab Baru (_blank)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Urutan Tampil</label>
            <input type="number" name="urutan" value="<?= htmlspecialchars($edit_menu['urutan'] ?? 1); ?>" min="1" max="99" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>
        </div>

        <div class="p-3 bg-teal-50/70 border border-teal-200 rounded-2xl space-y-1">
          <label class="flex items-center gap-2 text-xs text-teal-900 font-bold cursor-pointer">
            <input type="checkbox" name="is_gradient" value="1" <?= (!empty($edit_menu['is_gradient'])) ? 'checked' : ''; ?> class="w-4 h-4 rounded text-teal-700 focus:ring-teal-700">
            <span>✨ Terapkan Gradasi Warna Gerigi (#1bb28f & #1b969d)</span>
          </label>
          <p class="text-[10px] text-teal-700/80">Menu ini akan berpenampilan pill gradasi warna resmi logo gerigi kampus di navbar.</p>
        </div>

        <?php if ($edit_menu): ?>
          <div class="pt-1">
            <label class="flex items-center gap-2 text-xs text-slate-700 font-bold cursor-pointer">
              <input type="checkbox" name="is_active" value="1" <?= $edit_menu['is_active'] ? 'checked' : ''; ?> class="rounded text-teal-700 focus:ring-teal-700">
              <span>Status Aktif Tampil di Website</span>
            </label>
          </div>
        <?php endif; ?>

        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
          <?php if ($edit_menu): ?>
            <a href="menu_builder.php" class="text-xs font-bold text-slate-500 hover:text-slate-800">
              &larr; Batal Edit
            </a>
          <?php else: ?>
            <span></span>
          <?php endif; ?>
          <button type="submit" class="px-6 py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
            <?= $edit_menu ? 'Perbarui Menu &rarr;' : 'Simpan Menu Baru &rarr;'; ?>
          </button>
        </div>

      </form>
    </div>

    <!-- Kolom Kanan: Struktur Pohon Menu Terpasang -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-4">
      <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
        <div>
          <h3 class="text-sm font-black text-slate-900">Susunan Menu & Dropdown Aktif</h3>
          <p class="text-[11px] text-slate-500">Struktur navigasi yang saat ini tampil di website depan.</p>
        </div>
        <span class="text-xs font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-lg">
          <?= count($all_menus); ?> Menu Utama
        </span>
      </div>

      <div class="space-y-3">
        <?php foreach ($all_menus as $m): ?>
          <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
            
            <!-- Parent Menu Bar -->
            <div class="bg-slate-50 px-4 py-3 flex items-center justify-between border-b border-slate-200/60">
              <div class="flex flex-wrap items-center gap-2.5">
                <span class="w-6 h-6 rounded-lg bg-teal-700 text-white text-[10px] font-black flex items-center justify-center">
                  <?= $m['urutan']; ?>
                </span>
                <span class="text-xs font-black text-slate-900"><?= htmlspecialchars($m['judul']); ?></span>
                <span class="text-[10px] text-slate-400 font-mono">(<?= htmlspecialchars($m['url']); ?>)</span>
                <?php if (!empty($m['is_gradient'])): ?>
                  <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black text-white shadow-xs" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
                    ✨ Gradasi Gerigi (#1bb28f & #1b969d)
                  </span>
                <?php endif; ?>
                <?php if (!empty($m['children'])): ?>
                  <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">
                    <?= count($m['children']); ?> Submenu Dropdown
                  </span>
                <?php endif; ?>
              </div>

              <div class="flex items-center gap-1.5 text-xs">
                <a href="menu_builder.php?edit=<?= $m['id']; ?>" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg font-bold text-slate-700 text-[11px]">
                  Edit
                </a>
                <a href="menu_builder.php?delete=<?= $m['id']; ?>" onclick="return confirm('Hapus menu utama ini beserta seluruh submenunya?')" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-bold text-[11px]" title="Hapus">
                  🗑️
                </a>
              </div>
            </div>

            <!-- Children / Submenus Dropdown -->
            <?php if (!empty($m['children'])): ?>
              <div class="p-3 bg-white space-y-1.5">
                <?php foreach ($m['children'] as $ch): ?>
                  <div class="flex items-center justify-between pl-6 pr-3 py-1.5 rounded-xl bg-slate-50/80 hover:bg-teal-50/60 transition border border-slate-100 text-xs">
                    <div class="flex items-center gap-2">
                      <span class="text-amber-500 font-bold">↳</span>
                      <span class="font-bold text-slate-800"><?= htmlspecialchars($ch['judul']); ?></span>
                      <span class="text-[10px] text-slate-400 font-mono truncate max-w-xs"><?= htmlspecialchars($ch['url']); ?></span>
                    </div>

                    <div class="flex items-center gap-1 text-[11px]">
                      <a href="menu_builder.php?edit=<?= $ch['id']; ?>" class="text-teal-700 hover:underline font-bold px-1.5">
                        Edit
                      </a>
                      <a href="menu_builder.php?delete=<?= $ch['id']; ?>" onclick="return confirm('Hapus submenu ini?')" class="text-rose-600 hover:underline font-bold px-1.5">
                        Hapus
                      </a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>
      </div>

    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
