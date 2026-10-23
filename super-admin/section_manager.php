<?php
$page_title = "Dynamic Section Manager (Frontend Controller)";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// 1. Toggle Status Aktif (On/Off)
if (isset($_GET['toggle'])) {
    $sec_id = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("SELECT id, section_key, nama_seksi, is_active FROM homepage_sections WHERE id = ?");
    $stmt->execute([$sec_id]);
    $sec = $stmt->fetch();
    if ($sec) {
        $new_val = $sec['is_active'] ? 0 : 1;
        $up = $pdo->prepare("UPDATE homepage_sections SET is_active = ? WHERE id = ?");
        $up->execute([$new_val, $sec_id]);
        $status_txt = $new_val ? 'DIAKTIFKAN' : 'DINONAKTIFKAN';
        logActivity($pdo, $_SESSION['admin_id'], 'TOGGLE_HOMEPAGE_SECTION', 'Section Manager', $sec_id, "Seksi '{$sec['nama_seksi']}' ($status_txt)");
        $message = "Seksi '{$sec['nama_seksi']}' berhasil {$status_txt} di halaman utama!";
    }
}

// 2. Reorder Naik / Turun
if (isset($_GET['move']) && isset($_GET['id'])) {
    $move_id = (int)$_GET['id'];
    $dir = $_GET['move']; // 'up' or 'down'

    $stmt = $pdo->prepare("SELECT * FROM homepage_sections WHERE id = ?");
    $stmt->execute([$move_id]);
    $cur = $stmt->fetch();

    if ($cur) {
        if ($dir === 'up') {
            $swap_stmt = $pdo->prepare("SELECT * FROM homepage_sections WHERE urutan < ? ORDER BY urutan DESC LIMIT 1");
        } else {
            $swap_stmt = $pdo->prepare("SELECT * FROM homepage_sections WHERE urutan > ? ORDER BY urutan ASC LIMIT 1");
        }
        $swap_stmt->execute([$cur['urutan']]);
        $swap = $swap_stmt->fetch();

        if ($swap) {
            $pdo->beginTransaction();
            $pdo->prepare("UPDATE homepage_sections SET urutan = ? WHERE id = ?")->execute([$swap['urutan'], $cur['id']]);
            $pdo->prepare("UPDATE homepage_sections SET urutan = ? WHERE id = ?")->execute([$cur['urutan'], $swap['id']]);
            $pdo->commit();
            logActivity($pdo, $_SESSION['admin_id'], 'REORDER_HOMEPAGE_SECTION', 'Section Manager', $cur['id'], "Memindah urutan seksi {$cur['nama_seksi']} ke {$swap['urutan']}");
            $message = "Urutan blok tampilan '{$cur['nama_seksi']}' berhasil dipindahkan!";
        }
    }
}

// 3. Edit Detail Seksi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_section') {
    $sec_id = (int)$_POST['id'];
    $nama_seksi = trim($_POST['nama_seksi'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $urutan = (int)$_POST['urutan'];
    $container_style = $_POST['container_style'] ?? 'default';

    if (!empty($nama_seksi)) {
        $stmt_up = $pdo->prepare("UPDATE homepage_sections SET nama_seksi = ?, deskripsi = ?, urutan = ?, container_style = ? WHERE id = ?");
        $stmt_up->execute([$nama_seksi, $deskripsi, $urutan, $container_style, $sec_id]);
        logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_HOMEPAGE_SECTION', 'Section Manager', $sec_id, "Memperbarui info seksi {$nama_seksi}");
        $message = "Pengaturan blok '{$nama_seksi}' berhasil disimpan!";
    }
}

// Ambil seluruh seksi yang terurut
$sections = $pdo->query("SELECT * FROM homepage_sections ORDER BY urutan ASC")->fetchAll();
?>

<div class="space-y-6">

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
      <span><?= htmlspecialchars($message); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Hero Banner -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-layer-group"></i>
        <span>Dynamic Homepage Layout Controller</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">Dynamic Section Manager</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Atur susunan visual halaman utama institusi secara dinamis tanpa perlu mengubah baris kode (zero hardcoding). Anda dapat menyalakan/mematikan blok konten (On/Off) serta menggeser posisinya (Naik/Turun) sesuai kebutuhan operasional PMB kampus.
      </p>
    </div>
    <i class="fa-solid fa-cubes-stacked text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <!-- Sections List Table Card -->
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">
          Urutan & Status Blok Konten Halaman Depan
        </h3>
        <p class="text-[11px] text-slate-500">Terdapat <?= count($sections); ?> modul blok konten utama yang dapat dikontrol.</p>
      </div>

      <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 hover:bg-teal-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 self-start sm:self-auto shadow">
        <i class="fa-solid fa-arrow-up-right-from-square"></i>
        <span>Lihat Tampilan Web Langsung</span>
      </a>
    </div>

    <div class="divide-y divide-slate-100">
      <?php foreach ($sections as $idx => $sec): 
        $is_first = ($idx === 0);
        $is_last = ($idx === count($sections) - 1);
      ?>
        <div class="p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-4 transition <?= $sec['is_active'] ? 'hover:bg-slate-50/70' : 'bg-slate-100/50 opacity-60' ?>">
          
          <!-- Info Blok -->
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-black text-sm shrink-0 <?= $sec['is_active'] ? 'bg-teal-50 text-teal-800 border border-teal-200' : 'bg-slate-200 text-slate-600'; ?>">
              #<?= $sec['urutan']; ?>
            </div>
            <div>
              <div class="flex items-center gap-2 flex-wrap">
                <h4 class="text-sm font-black text-slate-900"><?= htmlspecialchars($sec['nama_seksi']); ?></h4>
                <code class="text-[10px] px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono">key: <?= htmlspecialchars($sec['section_key']); ?></code>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $sec['is_active'] ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'; ?>">
                  <?= $sec['is_active'] ? '● Aktif Tayang' : '○ Disembunyikan'; ?>
                </span>
              </div>
              <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($sec['deskripsi'] ?: 'Modul seksi pada halaman utama.'); ?></p>
            </div>
          </div>

          <!-- Controls: Reorder, Toggle On/Off, & Edit Modal Trigger -->
          <div class="flex items-center gap-2 flex-wrap lg:justify-end">
            <!-- Tombol Geser Urutan -->
            <div class="inline-flex rounded-xl shadow-sm border border-slate-200 bg-white p-1">
              <a href="section_manager.php?move=up&id=<?= $sec['id']; ?>" class="p-2 text-slate-600 hover:text-teal-700 hover:bg-slate-100 rounded-lg transition <?= $is_first ? 'opacity-30 pointer-events-none' : ''; ?>" title="Pindah Ke Atas">
                <i class="fa-solid fa-arrow-up text-xs"></i>
              </a>
              <span class="w-[1px] bg-slate-200 my-1"></span>
              <a href="section_manager.php?move=down&id=<?= $sec['id']; ?>" class="p-2 text-slate-600 hover:text-teal-700 hover:bg-slate-100 rounded-lg transition <?= $is_last ? 'opacity-30 pointer-events-none' : ''; ?>" title="Pindah Ke Bawah">
                <i class="fa-solid fa-arrow-down text-xs"></i>
              </a>
            </div>

            <!-- Toggle On / Off Switch -->
            <a href="section_manager.php?toggle=<?= $sec['id']; ?>" class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= $sec['is_active'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-600 hover:text-white' : 'bg-slate-200 text-slate-700 hover:bg-emerald-600 hover:text-white'; ?>">
              <i class="fa-solid <?= $sec['is_active'] ? 'fa-toggle-on text-emerald-600 text-sm' : 'fa-toggle-off text-slate-400 text-sm'; ?>"></i>
              <span><?= $sec['is_active'] ? 'Tayang (ON)' : 'Mati (OFF)'; ?></span>
            </a>

            <!-- Quick Edit Info Popover / Form Trigger -->
            <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($sec)); ?>)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
              <i class="fa-solid fa-pen-to-square text-xs text-slate-500"></i>
              <span>Ubah Judul</span>
            </button>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- Modal Ubah Info Seksi -->
<div id="editModal" class="fixed inset-0 z-50 bg-slate-950/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
      <h3 class="text-sm font-bold text-slate-900 uppercase">Ubah Info Seksi Homepage</h3>
      <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 p-1">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <form action="section_manager.php" method="POST" class="space-y-3.5">
      <input type="hidden" name="action" value="edit_section">
      <input type="hidden" name="id" id="modal_id">

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Judul Seksi</label>
        <input type="text" name="nama_seksi" id="modal_nama" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Ringkas</label>
        <textarea name="deskripsi" id="modal_deskripsi" rows="2" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Urutan Tampil</label>
        <input type="number" name="urutan" id="modal_urutan" min="1" max="99" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 mb-1">Gaya Wadah (Container Style)</label>
        <select name="container_style" id="modal_style" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
          <option value="default">Default Clean White</option>
          <option value="slate">Slate Minimalist Gray</option>
          <option value="teal_glow">Teal Brand Gradient Glow</option>
        </select>
      </div>

      <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
        <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">
          Batal
        </button>
        <button type="submit" class="px-4 py-2 bg-teal-800 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow">
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openEditModal(sec) {
  document.getElementById('modal_id').value = sec.id;
  document.getElementById('modal_nama').value = sec.nama_seksi;
  document.getElementById('modal_deskripsi').value = sec.deskripsi || '';
  document.getElementById('modal_urutan').value = sec.urutan;
  document.getElementById('modal_style').value = sec.container_style || 'default';
  document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
  document.getElementById('editModal').classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
