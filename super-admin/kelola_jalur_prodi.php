<?php
$page_title = "Jalur PMB & Program Studi";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Update Jalur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_jalur') {
    $id        = (int)$_POST['id'];
    $nama      = trim($_POST['nama_jalur'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $biaya     = (float)($_POST['biaya_daftar'] ?? 0);
    $tgl_tutup = $_POST['tgl_tutup'] ?? '';

    $stmt = $pdo->prepare("UPDATE jalur_pendaftaran SET nama_jalur = ?, deskripsi = ?, biaya_daftar = ?, tgl_tutup = ? WHERE id = ?");
    if ($stmt->execute([$nama, $deskripsi, $biaya, $tgl_tutup, $id])) {
        $message = "Data jalur pendaftaran berhasil diperbarui!";
    }
}

// Update Prodi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_prodi') {
    $id         = (int)$_POST['id'];
    $nama       = trim($_POST['nama_prodi'] ?? '');
    $akreditasi = trim($_POST['akreditasi'] ?? 'Baik');
    $kuota      = (int)($_POST['kuota'] ?? 60);
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $karir      = trim($_POST['prospek_karir'] ?? '');

    $stmt = $pdo->prepare("UPDATE program_studi SET nama_prodi = ?, akreditasi = ?, kuota = ?, deskripsi = ?, prospek_karir = ? WHERE id = ?");
    if ($stmt->execute([$nama, $akreditasi, $kuota, $deskripsi, $karir, $id])) {
        $message = "Data program studi berhasil diperbarui!";
    }
}

$jalur_list = $pdo->query("SELECT * FROM jalur_pendaftaran ORDER BY id ASC")->fetchAll();
$prodi_list = $pdo->query("SELECT * FROM program_studi ORDER BY id ASC")->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-8">

  <div>
    <h2 class="text-xl font-black text-slate-900">Kelola Jalur PMB & Program Studi (D4)</h2>
    <p class="text-xs text-slate-500">Sesuaikan biaya pendaftaran, kuota daya tampung, akreditasi, dan prospek karir vokasi.</p>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold">
      ✓ <?= htmlspecialchars($message); ?>
    </div>
  <?php endif; ?>

  <!-- Jalur Pendaftaran Section -->
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-6">
    <div class="border-b border-slate-100 pb-3">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider">
        1. Jalur Penerimaan Mahasiswa Baru
      </h3>
      <p class="text-[11px] text-slate-500">Ubah biaya pendaftaran dan tanggal batas akhir setiap jalur.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($jalur_list as $jl): ?>
        <form action="kelola_jalur_prodi.php" method="POST" class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 flex flex-col justify-between">
          <input type="hidden" name="action" value="edit_jalur">
          <input type="hidden" name="id" value="<?= $jl['id']; ?>">

          <div class="space-y-3">
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">
              Kode: <?= htmlspecialchars($jl['kode_jalur']); ?>
            </span>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Jalur</label>
              <input type="text" name="nama_jalur" value="<?= htmlspecialchars($jl['nama_jalur']); ?>" required class="w-full px-3 py-1.5 text-xs font-bold border border-slate-300 rounded-lg bg-white">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Biaya Pendaftaran (Rp)</label>
              <input type="number" name="biaya_daftar" value="<?= (int)$jl['biaya_daftar']; ?>" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">
              <span class="text-[10px] text-slate-400">Isi 0 untuk GRATIS</span>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Batas Akhir Pendaftaran</label>
              <input type="date" name="tgl_tutup" value="<?= $jl['tgl_tutup']; ?>" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Deskripsi Syarat</label>
              <textarea name="deskripsi" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white leading-relaxed"><?= htmlspecialchars($jl['deskripsi']); ?></textarea>
            </div>
          </div>

          <button type="submit" class="w-full py-2 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition mt-2">
            Simpan Jalur Ini
          </button>
        </form>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Program Studi Section -->
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-6">
    <div class="border-b border-slate-100 pb-3">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider">
        2. Program Studi Sarjana Terapan (D4)
      </h3>
      <p class="text-[11px] text-slate-500">Ubah kuota daya tampung, akreditasi, dan prospek karir lulusan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($prodi_list as $pr): ?>
        <form action="kelola_jalur_prodi.php" method="POST" class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 flex flex-col justify-between">
          <input type="hidden" name="action" value="edit_prodi">
          <input type="hidden" name="id" value="<?= $pr['id']; ?>">

          <div class="space-y-3">
            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900 uppercase">
              Jenjang: <?= htmlspecialchars($pr['jenjang']); ?> (<?= htmlspecialchars($pr['kode_prodi']); ?>)
            </span>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Program Studi</label>
              <input type="text" name="nama_prodi" value="<?= htmlspecialchars($pr['nama_prodi']); ?>" required class="w-full px-3 py-1.5 text-xs font-bold border border-slate-300 rounded-lg bg-white">
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Akreditasi</label>
                <input type="text" name="akreditasi" value="<?= htmlspecialchars($pr['akreditasi']); ?>" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kuota Mhs</label>
                <input type="number" name="kuota" value="<?= $pr['kuota']; ?>" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white">
              </div>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Fokus & Deskripsi</label>
              <textarea name="deskripsi" rows="3" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white leading-relaxed"><?= htmlspecialchars($pr['deskripsi']); ?></textarea>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-slate-700 mb-1">Prospek Karir Lulusan</label>
              <textarea name="prospek_karir" rows="2" class="w-full px-3 py-1.5 text-xs border border-slate-300 rounded-lg bg-white leading-relaxed"><?= htmlspecialchars($pr['prospek_karir']); ?></textarea>
            </div>
          </div>

          <button type="submit" class="w-full py-2 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition mt-2">
            Simpan Prodi Ini
          </button>
        </form>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
