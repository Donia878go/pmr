<?php
$page_title = "Audit Trail & Server Activity Logs";
require_once __DIR__ . '/layout_top.php';

$message = '';

// Tindakan Bersihkan Log
if (isset($_POST['action']) && $_POST['action'] === 'clear_logs') {
    $pdo->query("TRUNCATE TABLE audit_logs");
    logActivity($pdo, $_SESSION['admin_id'], 'CLEAR_LOGS', 'System Audit', null, "Seluruh riwayat audit log dibersihkan oleh Super Admin");
    $message = "Seluruh riwayat audit log berhasil dibersihkan.";
}

// Filter Pencarian
$q = trim($_GET['q'] ?? '');
$module_filter = trim($_GET['module'] ?? '');
$action_filter = trim($_GET['action_type'] ?? '');

$sql = "SELECT * FROM audit_logs WHERE 1=1";
$params = [];

if (!empty($q)) {
    $sql .= " AND (username LIKE ? OR detail LIKE ? OR ip_address LIKE ?)";
    $like = "%{$q}%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($module_filter)) {
    $sql .= " AND module = ?";
    $params[] = $module_filter;
}

if (!empty($action_filter)) {
    $sql .= " AND action LIKE ?";
    $params[] = "%{$action_filter}%";
}

$sql .= " ORDER BY id DESC LIMIT 150";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Ambil daftar modul yang ada untuk dropdown filter
$modules = $pdo->query("SELECT DISTINCT module FROM audit_logs ORDER BY module ASC")->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="space-y-6">

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
      <span><?= htmlspecialchars($message); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Hero Card -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-teal-500/20 text-teal-300 border border-teal-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>Security Compliance & Audit Trail</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">Audit Trail (Activity Log Server)</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Pencatatan real-time otomatis untuk setiap perubahan di dashboard institusi: Waktu aktivitas, IP Address, Nama Pengguna, Tingkat Hak Akses, Aksi CRUD, Modul yang diubah, dan Detail modifikasi untuk akuntabilitas sistem.
      </p>
    </div>
    <i class="fa-solid fa-fingerprint text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <!-- Filter & Action Bar -->
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
    <form action="audit_logs.php" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
      <div>
        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Cari Kata Kunci / IP</label>
        <div class="relative">
          <input type="text" name="q" value="<?= htmlspecialchars($q); ?>" placeholder="Nama user, IP, detail aksi..." class="w-full pl-9 pr-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
        </div>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Filter Modul</label>
        <select name="module" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
          <option value="">-- Semua Modul --</option>
          <?php foreach ($modules as $m): ?>
            <option value="<?= htmlspecialchars($m); ?>" <?= $module_filter === $m ? 'selected' : ''; ?>>
              <?= htmlspecialchars($m); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Jenis Aksi (CRUD)</label>
        <select name="action_type" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
          <option value="">-- Semua Aksi --</option>
          <option value="CREATE" <?= $action_filter === 'CREATE' ? 'selected' : ''; ?>>CREATE (Tambah Data)</option>
          <option value="UPDATE" <?= $action_filter === 'UPDATE' ? 'selected' : ''; ?>>UPDATE (Ubah Data)</option>
          <option value="DELETE" <?= $action_filter === 'DELETE' ? 'selected' : ''; ?>>DELETE (Hapus Data)</option>
          <option value="IMPERSONATE" <?= $action_filter === 'IMPERSONATE' ? 'selected' : ''; ?>>IMPERSONATE (Login Pengguna)</option>
        </select>
      </div>

      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 py-2 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl transition flex items-center justify-center gap-1.5 shadow">
          <i class="fa-solid fa-filter"></i>
          <span>Terapkan Filter</span>
        </button>
        <a href="audit_logs.php" class="p-2 text-slate-500 hover:text-slate-800 border border-slate-200 rounded-xl hover:bg-slate-50 transition" title="Reset Filter">
          <i class="fa-solid fa-rotate-left"></i>
        </a>
      </div>
    </form>

    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs text-slate-500">
      <div>
        Menampilkan <strong><?= count($logs); ?></strong> riwayat log aktivitas terbaru.
      </div>

      <form action="audit_logs.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan seluruh riwayat audit log? Tindakan ini tidak dapat dibatalkan.')">
        <input type="hidden" name="action" value="clear_logs">
        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs flex items-center gap-1 hover:underline">
          <i class="fa-solid fa-trash-can"></i>
          <span>Bersihkan Riwayat Log</span>
        </button>
      </form>
    </div>
  </div>

  <!-- Audit Table Card -->
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-slate-900 text-slate-200 uppercase tracking-wider text-[10px] font-black">
            <th class="py-3.5 px-4">Waktu (WIB)</th>
            <th class="py-3.5 px-4">Pengguna</th>
            <th class="py-3.5 px-4">Modul</th>
            <th class="py-3.5 px-4">Aksi</th>
            <th class="py-3.5 px-4">Detail Perubahan</th>
            <th class="py-3.5 px-4">IP Address</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php if (empty($logs)): ?>
            <tr>
              <td colspan="6" class="text-center py-12 text-slate-400">
                <i class="fa-solid fa-clipboard-check text-4xl mb-3 block text-slate-300"></i>
                Tidak ada data log yang cocok dengan filter pencarian.
              </td>
            </tr>
          <?php endif; ?>

          <?php foreach ($logs as $log): 
            $action = strtoupper($log['action']);
            $badge_color = 'bg-slate-100 text-slate-700';
            if (str_contains($action, 'CREATE')) $badge_color = 'bg-emerald-100 text-emerald-800 font-bold';
            elseif (str_contains($action, 'UPDATE') || str_contains($action, 'TOGGLE')) $badge_color = 'bg-amber-100 text-amber-800 font-bold';
            elseif (str_contains($action, 'DELETE')) $badge_color = 'bg-rose-100 text-rose-800 font-bold';
            elseif (str_contains($action, 'IMPERSONATE')) $badge_color = 'bg-indigo-100 text-indigo-800 font-bold';
          ?>
            <tr class="hover:bg-slate-50 transition">
              <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] text-slate-500">
                <?= htmlspecialchars($log['created_at']); ?>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                <div class="font-bold text-slate-900"><?= htmlspecialchars($log['username'] ?: 'System'); ?></div>
                <span class="text-[10px] text-teal-700 font-semibold uppercase"><?= htmlspecialchars($log['user_role'] ?: '-'); ?></span>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap font-semibold text-slate-800">
                <?= htmlspecialchars($log['module']); ?>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap">
                <span class="px-2 py-0.5 rounded text-[10px] uppercase <?= $badge_color; ?>">
                  <?= htmlspecialchars($log['action']); ?>
                </span>
              </td>
              <td class="py-3.5 px-4 text-slate-600 max-w-md break-words">
                <?= htmlspecialchars($log['detail']); ?>
              </td>
              <td class="py-3.5 px-4 whitespace-nowrap font-mono text-[11px] text-slate-500">
                <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200">
                  <?= htmlspecialchars($log['ip_address'] ?? '127.0.0.1'); ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
