<?php
$page_title = "Kelola Berita & Pengumuman";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_pengumuman') {
    $judul    = trim($_POST['judul'] ?? '');
    $kategori = $_POST['kategori'] ?? 'pengumuman';
    $isi      = trim($_POST['isi'] ?? '');
    $tanggal  = $_POST['tanggal'] ?? date('Y-m-d');
    $status   = $_POST['status'] ?? 'publish';

    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul))) . '-' . rand(100, 999);

    if (empty($judul) || empty($isi)) {
        $error = "Judul dan isi pengumuman wajib diisi!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO pengumuman (judul, slug, kategori, isi, tanggal, status) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$judul, $slug, $kategori, $isi, $tanggal, $status])) {
            $message = "Pengumuman baru berhasil dipublikasikan ke web depan!";
        }
    }
}

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM pengumuman WHERE id = ?")->execute([$del_id]);
    $message = "Pengumuman berhasil dihapus.";
}

$pengumuman = $pdo->query("SELECT * FROM pengumuman ORDER BY tanggal DESC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div>
    <h2 class="text-xl font-black text-slate-900">Kelola Informasi, Berita & Pengumuman</h2>
    <p class="text-xs text-slate-500">Kabar terbaru, jadwal penerimaan, beasiswa, dan info seleksi untuk calon mahasiswa.</p>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
      ✓ <?= htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
        ➕ Buat Pengumuman Baru
      </h3>

      <form action="kelola_pengumuman.php" method="POST" class="space-y-3.5">
        <input type="hidden" name="action" value="add_pengumuman">

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Judul Pengumuman *</label>
          <input type="text" name="judul" required placeholder="Contoh: Jadwal Ujian Masuk Gelombang 1" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
            <select name="kategori" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              <option value="pengumuman">Pengumuman</option>
              <option value="jadwal">Jadwal Seleksi</option>
              <option value="beasiswa">Info Beasiswa</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="<?= date('Y-m-d'); ?>" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
          </div>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Isi Berita / Pengumuman *</label>
          <textarea name="isi" rows="4" required placeholder="Tuliskan detail pengumuman secara lengkap..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
          Publikasikan Pengumuman &rarr;
        </button>
      </form>
    </div>

    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
        Daftar Pengumuman Terbit (<?= count($pengumuman); ?>)
      </h3>

      <div class="space-y-3">
        <?php foreach ($pengumuman as $p): ?>
          <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 space-y-2">
            <div class="flex justify-between items-start">
              <div>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">
                  <?= htmlspecialchars($p['kategori']); ?>
                </span>
                <span class="text-[10px] text-slate-400 ml-2">📅 <?= $p['tanggal']; ?></span>
                <h4 class="text-xs font-black text-slate-900 mt-1"><?= htmlspecialchars($p['judul']); ?></h4>
              </div>
              <a href="kelola_pengumuman.php?delete=<?= $p['id']; ?>" onclick="return confirm('Hapus pengumuman ini?')" class="text-rose-600 hover:underline font-bold text-[11px] shrink-0">
                Hapus
              </a>
            </div>
            <p class="text-xs text-slate-600 line-clamp-2"><?= htmlspecialchars($p['isi']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
