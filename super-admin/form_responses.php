<?php
$page_title = "Respon Formulir Dinamis Masuk";
require_once __DIR__ . '/layout_top.php';

$message = '';
$filter_form = isset($_GET['form_id']) ? (int)$_GET['form_id'] : 0;

// Hapus respon
if (isset($_GET['delete_sub'])) {
    $del_id = (int)$_GET['delete_sub'];
    $stmt_d = $pdo->prepare("DELETE FROM dynamic_form_submissions WHERE id = ?");
    if ($stmt_d->execute([$del_id])) {
        $message = "Respon formulir berhasil dihapus.";
    }
}

// Ambil list semua formulir untuk filter
$forms_list = $pdo->query("SELECT id, judul_form, slug FROM dynamic_forms ORDER BY id DESC")->fetchAll();

// Query Submissions
$sql = "SELECT s.*, f.judul_form, f.slug 
        FROM dynamic_form_submissions s 
        JOIN dynamic_forms f ON s.form_id = f.id 
        WHERE 1=1";
$params = [];

if ($filter_form > 0) {
    $sql .= " AND s.form_id = ?";
    $params[] = $filter_form;
}

$sql .= " ORDER BY s.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$submissions = $stmt->fetchAll();
?>

<div class="max-w-6xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Respon Formulir Publik Masuk</h2>
      <p class="text-xs text-slate-500">Seluruh data yang telah diisi pengunjung melalui formulir kuesioner, survei, atau pengaduan.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="form_builder.php" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
        &larr; Kelola Desain Formulir
      </a>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="form_responses.php<?= $filter_form ? '?form_id=' . $filter_form : ''; ?>" class="underline font-bold text-emerald-900">Tutup</a>
    </div>
  <?php endif; ?>

  <!-- Filter Form Bar -->
  <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
    <form action="form_responses.php" method="GET" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
      <label class="text-xs font-bold text-slate-700">Filter Formulir:</label>
      <select name="form_id" onchange="this.form.submit()" class="px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
        <option value="0">-- Tampilkan Seluruh Formulir --</option>
        <?php foreach ($forms_list as $fl): ?>
          <option value="<?= $fl['id']; ?>" <?= $filter_form === (int)$fl['id'] ? 'selected' : ''; ?>>
            <?= htmlspecialchars($fl['judul_form']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <div class="text-xs text-slate-500">
      Total Respon: <strong><?= count($submissions); ?></strong> data
    </div>
  </div>

  <!-- Data Table Submissions -->
  <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    <?php if (empty($submissions)): ?>
      <div class="p-12 text-center text-xs text-slate-500 space-y-2">
        <div class="text-3xl">📭</div>
        <p class="font-bold">Belum ada respon formulir yang masuk.</p>
        <p class="text-[11px] text-slate-400">Pengunjung dapat mengisi formulir melalui link yang tertera di menu website.</p>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase font-black text-[10px] tracking-wider">
            <tr>
              <th class="py-3.5 px-4 w-12 text-center">ID</th>
              <th class="py-3.5 px-4">Nama Formulir</th>
              <th class="py-3.5 px-4">Isi Respon Data</th>
              <th class="py-3.5 px-4">File Lampiran</th>
              <th class="py-3.5 px-4">Waktu Kirim</th>
              <th class="py-3.5 px-4 text-center w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php foreach ($submissions as $sub): 
              $data = json_decode($sub['data_json'], true) ?: [];
            ?>
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-4 text-center font-bold text-slate-500">
                  #<?= $sub['id']; ?>
                </td>
                <td class="py-4 px-4 font-bold text-teal-800">
                  <?= htmlspecialchars($sub['judul_form']); ?>
                  <span class="block text-[10px] text-slate-400 font-mono">IP: <?= htmlspecialchars($sub['ip_address'] ?? '-'); ?></span>
                </td>
                <td class="py-4 px-4">
                  <div class="space-y-1 max-w-md">
                    <?php foreach ($data as $k => $v): ?>
                      <div class="text-[11px]">
                        <strong class="text-slate-700"><?= htmlspecialchars($k); ?>:</strong>
                        <span class="text-slate-600"><?= htmlspecialchars($v); ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </td>
                <td class="py-4 px-4">
                  <?php if (!empty($sub['file_attachment'])): ?>
                    <a href="../<?= htmlspecialchars($sub['file_attachment']); ?>" target="_blank" class="inline-flex items-center gap-1 px-3 py-1 bg-teal-50 hover:bg-teal-100 text-teal-800 font-bold rounded-lg text-[11px] border border-teal-200 transition">
                      <span>📎 Unduh File</span>
                    </a>
                  <?php else: ?>
                    <span class="text-slate-400 text-[11px]">-</span>
                  <?php endif; ?>
                </td>
                <td class="py-4 px-4 text-slate-500 text-[11px] whitespace-nowrap">
                  <?= $sub['created_at']; ?>
                </td>
                <td class="py-4 px-4 text-center">
                  <a href="form_responses.php?delete_sub=<?= $sub['id']; ?><?= $filter_form ? '&form_id=' . $filter_form : ''; ?>" onclick="return confirm('Hapus data respon formulir ini?')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg text-xs font-bold transition inline-block" title="Hapus">
                    🗑️ Hapus
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
