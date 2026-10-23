<?php
$page_title = "Kelola Testimoni & Mitra Industri";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_testi') {
    $nama          = trim($_POST['nama'] ?? '');
    $role_instansi = trim($_POST['role_instansi'] ?? '');
    $pesan         = trim($_POST['pesan'] ?? '');
    $tipe          = $_POST['tipe'] ?? 'mitra_industri';
    $urutan        = (int)($_POST['urutan'] ?? 1);

    if (empty($nama) || empty($pesan)) {
        $error = "Nama dan pesan testimoni wajib diisi!";
    } else {
        $stmt = $pdo->prepare("INSERT INTO testimoni_mitra (nama, role_instansi, pesan, tipe, urutan) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nama, $role_instansi, $pesan, $tipe, $urutan])) {
            $message = "Testimoni baru berhasil ditambahkan!";
        }
    }
}

if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM testimoni_mitra WHERE id = ?")->execute([$del_id]);
    $message = "Testimoni berhasil dihapus.";
}

$testimoni = $pdo->query("SELECT * FROM testimoni_mitra ORDER BY urutan ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div>
    <h2 class="text-xl font-black text-slate-900">Kelola Testimoni & Kemitraan Industri</h2>
    <p class="text-xs text-slate-500">Testimoni dari mitra pabrik kelapa sawit, alumni berkarir, dan mahasiswa berprestasi.</p>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
      ✓ <?= htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
        ➕ Tambah Testimoni Baru
      </h3>

      <form action="kelola_testimoni.php" method="POST" class="space-y-3.5">
        <input type="hidden" name="action" value="add_testi">

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemberi Testimoni / Instansi *</label>
          <input type="text" name="nama" required placeholder="Contoh: PT. Rokan Sawit Makmur" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Peran / Jabatan / Instansi</label>
          <input type="text" name="role_instansi" placeholder="Konsorsium Pabrik Kelapa Sawit" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Testimoni</label>
          <select name="tipe" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
            <option value="mitra_industri">Mitra Industri Sawit / Perusahaan</option>
            <option value="alumni">Alumni Telah Bekerja</option>
            <option value="mahasiswa">Mahasiswa Aktif Penerima Beasiswa</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pesan Testimoni *</label>
          <textarea name="pesan" rows="3" required placeholder="Tuliskan ulasan apresiasi..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
          <input type="number" name="urutan" value="<?= count($testimoni) + 1; ?>" min="1" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
          Simpan Testimoni &rarr;
        </button>
      </form>
    </div>

    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100">
        Daftar Testimoni Aktif (<?= count($testimoni); ?>)
      </h3>

      <div class="space-y-3">
        <?php foreach ($testimoni as $t): ?>
          <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex justify-between items-start gap-4">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 uppercase">
                  <?= htmlspecialchars($t['tipe']); ?>
                </span>
                <span class="text-xs font-black text-slate-900"><?= htmlspecialchars($t['nama']); ?></span>
                <span class="text-[11px] text-slate-500">(<?= htmlspecialchars($t['role_instansi']); ?>)</span>
              </div>
              <p class="text-xs text-slate-600 italic">"<?= htmlspecialchars($t['pesan']); ?>"</p>
            </div>
            <a href="kelola_testimoni.php?delete=<?= $t['id']; ?>" onclick="return confirm('Hapus testimoni ini?')" class="text-rose-600 hover:underline font-bold text-[11px] shrink-0">
              Hapus
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
