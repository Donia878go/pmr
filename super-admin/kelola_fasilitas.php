<?php
$page_title = "Kelola Fasilitas Kampus";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Tambah Fasilitas
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_fasilitas') {
    $nama      = trim($_POST['nama_fasilitas'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? 'Laboratorium');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $urutan    = (int)($_POST['urutan'] ?? 1);

    if (empty($nama) || empty($deskripsi)) {
        $error = "Nama fasilitas dan deskripsi wajib diisi!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO fasilitas_kampus (nama_fasilitas, kategori, deskripsi, urutan) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$nama, $kategori, $deskripsi, $urutan])) {
            $message = "Fasilitas baru berhasil ditambahkan!";
        }
    }
}

// Hapus Fasilitas
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM fasilitas_kampus WHERE id = ?")->execute([$del_id]);
    $message = "Fasilitas berhasil dihapus.";
}

$fasilitas = $pdo->query("SELECT * FROM fasilitas_kampus ORDER BY urutan ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div class="flex justify-between items-center">
    <div>
      <h2 class="text-xl font-black text-slate-900">Kelola Fasilitas & Laboratorium Kampus</h2>
      <p class="text-xs text-slate-500">Konten sarana prasarana yang tampil di seksi Fasilitas website utama.</p>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
      ✓ <?= htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Form Tambah -->
    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
        ➕ Tambah Fasilitas Baru
      </h3>

      <form action="kelola_fasilitas.php" method="POST" class="space-y-3.5">
        <input type="hidden" name="action" value="add_fasilitas">

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Fasilitas / Lab *</label>
          <input type="text" name="nama_fasilitas" required placeholder="Contoh: Lab PLC & Otomasi Pabrik" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Fasilitas</label>
          <input type="text" name="kategori" placeholder="Laboratorium Khusus / Workshop" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat *</label>
          <textarea name="deskripsi" rows="3" required placeholder="Jelaskan spesifikasi dan kegunaan fasilitas..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urutan Tampil</label>
          <input type="number" name="urutan" value="<?= count($fasilitas) + 1; ?>" min="1" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
          Simpan Fasilitas &rarr;
        </button>
      </form>
    </div>

    <!-- Daftar Fasilitas -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex justify-between items-center">
        <span>Fasilitas Terdaftar (<?= count($fasilitas); ?>)</span>
      </h3>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($fasilitas as $f): ?>
          <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col justify-between">
            <div>
              <div class="flex justify-between items-start mb-2">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">
                  <?= htmlspecialchars($f['kategori']); ?>
                </span>
                <span class="text-[11px] font-bold text-slate-400">Urut #<?= $f['urutan']; ?></span>
              </div>
              <h4 class="text-xs font-black text-slate-900 mb-1"><?= htmlspecialchars($f['nama_fasilitas']); ?></h4>
              <p class="text-[11px] text-slate-600 leading-relaxed"><?= htmlspecialchars($f['deskripsi']); ?></p>
            </div>
            <div class="pt-3 border-t border-slate-200 mt-3 text-right">
              <a href="kelola_fasilitas.php?delete=<?= $f['id']; ?>" onclick="return confirm('Hapus fasilitas ini?')" class="text-rose-600 hover:underline font-bold text-[11px]">
                Hapus
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
