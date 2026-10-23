<?php
$page_title = "Central Media Library (File Manager)";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// 1. Upload File Media Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_media') {
    $caption = trim($_POST['caption'] ?? '');
    
    if (!empty($_FILES['media_file']['name'])) {
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif', 'pdf', 'docx', 'xlsx'];
        $orig_name = basename($_FILES['media_file']['name']);
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        $file_size = (int)$_FILES['media_file']['size'];

        if (!in_array($ext, $allowed)) {
            $error = "Ekstensi file tidak diizinkan. Gunakan JPG, PNG, SVG, WEBP, atau PDF.";
        } elseif ($file_size > 10 * 1024 * 1024) { // 10MB
            $error = "Ukuran file terlalu besar! Maksimal 10 MB.";
        } else {
            $type_group = in_array($ext, ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif']) ? 'image' : 'document';
            $unique_name = 'media_' . time() . '_' . rand(100, 999) . '.' . $ext;
            
            // Simpan di assets/images atau assets/documents
            $subfolder = ($type_group === 'image') ? 'assets/images/' : 'assets/docs/';
            $dest_dir = __DIR__ . '/../' . $subfolder;
            if (!is_dir($dest_dir)) {
                mkdir($dest_dir, 0777, true);
            }
            
            $dest_path = $dest_dir . $unique_name;
            $relative_path = $subfolder . $unique_name;

            if (move_uploaded_file($_FILES['media_file']['tmp_name'], $dest_path)) {
                $uploaded_by = $_SESSION['admin_username'] ?? 'superadmin';
                $stmt = $pdo->prepare("INSERT INTO media_library (file_name, file_path, file_type, file_size, caption, uploaded_by, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$orig_name, $relative_path, $type_group, $file_size, $caption ?: $orig_name, $uploaded_by]);
                $new_id = $pdo->lastInsertId();
                logActivity($pdo, $_SESSION['admin_id'], 'UPLOAD_MEDIA', 'Media Library', $new_id, "Mengunggah aset: $orig_name ($type_group)");
                $message = "File '$orig_name' berhasil diunggah ke Media Library!";
            } else {
                $error = "Gagal memindahkan file ke direktori server.";
            }
        }
    } else {
        $error = "Silakan pilih file yang ingin diunggah.";
    }
}

// 2. Hapus Media
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT * FROM media_library WHERE id = ?");
    $stmt->execute([$del_id]);
    $media = $stmt->fetch();

    if ($media) {
        $real_path = __DIR__ . '/../' . $media['file_path'];
        if (file_exists($real_path)) {
            @unlink($real_path);
        }
        $pdo->prepare("DELETE FROM media_library WHERE id = ?")->execute([$del_id]);
        logActivity($pdo, $_SESSION['admin_id'], 'DELETE_MEDIA', 'Media Library', $del_id, "Menghapus aset media: {$media['file_name']}");
        $message = "Aset file '{$media['file_name']}' berhasil dihapus.";
    }
}

// Filter Media Type
$type_filter = trim($_GET['type'] ?? '');
$sql = "SELECT * FROM media_library WHERE 1=1";
$params = [];

if (!empty($type_filter)) {
    $sql .= " AND file_type = ?";
    $params[] = $type_filter;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$media_items = $stmt->fetchAll();

// Total count
$total_all = $pdo->query("SELECT COUNT(*) FROM media_library")->fetchColumn();
$total_img = $pdo->query("SELECT COUNT(*) FROM media_library WHERE file_type = 'image'")->fetchColumn();
$total_doc = $pdo->query("SELECT COUNT(*) FROM media_library WHERE file_type = 'document'")->fetchColumn();
?>

<div class="space-y-6">

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
      <span><?= htmlspecialchars($message); ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
      <span><?= htmlspecialchars($error); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Hero Card -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-photo-film"></i>
        <span>Central Asset Manager</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">Central Media Library</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Pusat penyimpanan global untuk seluruh aset gambar, brosur PMB, logo, dan dokumen regulasi kampus. Unggah berkas sekali, salin URL instan (Copy Link), dan sematkan di modul mana pun tanpa batasan.
      </p>
    </div>
    <i class="fa-solid fa-images text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- Form Upload Media Baru -->
    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
        <i class="fa-solid fa-cloud-arrow-up text-teal-600 text-sm"></i>
        <span>Unggah Berkas Baru</span>
      </h3>

      <form action="media_library.php" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="action" value="upload_media">

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih File (Gambar / PDF) *</label>
          <input type="file" name="media_file" required accept=".jpg,.jpeg,.png,.svg,.webp,.gif,.pdf,.docx,.xlsx" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 border border-slate-200 rounded-xl p-1">
          <p class="text-[10px] text-slate-400 mt-1">Mendukung format JPG, PNG, SVG, WEBP, dan PDF (Maks. 10MB).</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Keterangan / Caption Berkas</label>
          <input type="text" name="caption" placeholder="Contoh: Brosur Beasiswa PMB 2026" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none">
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
          <i class="fa-solid fa-upload"></i>
          <span>Unggah ke Media Library</span>
        </button>
      </form>

      <!-- Storage Info Box -->
      <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-xs space-y-2">
        <div class="font-bold text-slate-800 flex items-center gap-2">
          <i class="fa-solid fa-server text-teal-600"></i>
          <span>Ringkasan Media Disimpan</span>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Total Berkas:</span>
          <strong><?= $total_all; ?> Item</strong>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Gambar (JPG/PNG/SVG):</span>
          <strong><?= $total_img; ?> Item</strong>
        </div>
        <div class="flex justify-between text-slate-600">
          <span>Dokumen (PDF/Docs):</span>
          <strong><?= $total_doc; ?> Item</strong>
        </div>
      </div>
    </div>

    <!-- Media Gallery Grid -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      
      <!-- Filter Tabs -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-3">
        <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">
          Semua Aset Media (<?= count($media_items); ?>)
        </h3>
        
        <div class="flex items-center gap-1.5 text-xs">
          <a href="media_library.php" class="px-3 py-1 rounded-xl font-bold transition <?= empty($type_filter) ? 'bg-teal-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
            Semua (<?= $total_all; ?>)
          </a>
          <a href="media_library.php?type=image" class="px-3 py-1 rounded-xl font-bold transition <?= $type_filter === 'image' ? 'bg-teal-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
            Gambar (<?= $total_img; ?>)
          </a>
          <a href="media_library.php?type=document" class="px-3 py-1 rounded-xl font-bold transition <?= $type_filter === 'document' ? 'bg-teal-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">
            Dokumen (<?= $total_doc; ?>)
          </a>
        </div>
      </div>

      <!-- Grid Cards -->
      <?php if (empty($media_items)): ?>
        <div class="text-center py-16 text-slate-400">
          <i class="fa-regular fa-folder-open text-4xl mb-3 block text-slate-300"></i>
          Belum ada berkas media di kategori ini.
        </div>
      <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
          <?php foreach ($media_items as $m): 
            $is_img = ($m['file_type'] === 'image');
            $size_kb = round($m['file_size'] / 1024, 1);
            $size_txt = ($size_kb > 1024) ? round($size_kb / 1024, 2) . ' MB' : $size_kb . ' KB';
          ?>
            <div class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between group hover:border-teal-400 transition hover:shadow-sm">
              
              <!-- Thumbnail -->
              <div class="h-36 bg-slate-900/5 relative overflow-hidden flex items-center justify-center">
                <?php if ($is_img): ?>
                  <img src="../<?= htmlspecialchars($m['file_path']); ?>" alt="Media" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                <?php else: ?>
                  <div class="text-center p-4">
                    <i class="fa-solid fa-file-pdf text-rose-500 text-4xl mb-1"></i>
                    <span class="block text-[10px] font-bold text-slate-500 uppercase">PDF Document</span>
                  </div>
                <?php endif; ?>

                <span class="absolute top-2 right-2 px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-slate-900/80 text-white backdrop-blur-md">
                  <?= $size_txt; ?>
                </span>
              </div>

              <!-- Metadata Info -->
              <div class="p-3.5 space-y-2 flex-1 flex flex-col justify-between">
                <div>
                  <h4 class="text-xs font-bold text-slate-900 truncate" title="<?= htmlspecialchars($m['caption'] ?: $m['file_name']); ?>">
                    <?= htmlspecialchars($m['caption'] ?: $m['file_name']); ?>
                  </h4>
                  <p class="text-[10px] text-slate-400 font-mono truncate"><?= htmlspecialchars($m['file_name']); ?></p>
                </div>

                <!-- Action Buttons: Copy Link & Delete -->
                <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between gap-2">
                  <button type="button" onclick="copyMediaUrl('<?= htmlspecialchars($m['file_path']); ?>')" class="flex-1 py-1 px-2 bg-white hover:bg-teal-50 text-teal-800 border border-slate-200 rounded-lg text-[11px] font-bold transition flex items-center justify-center gap-1">
                    <i class="fa-solid fa-link text-[10px]"></i>
                    <span>Salin URL</span>
                  </button>

                  <a href="../<?= htmlspecialchars($m['file_path']); ?>" target="_blank" class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-200 rounded-lg transition" title="Buka File">
                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                  </a>

                  <a href="media_library.php?delete=<?= $m['id']; ?>" onclick="return confirm('Hapus permanen berkas <?= htmlspecialchars($m['file_name']); ?>?')" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-100 rounded-lg transition" title="Hapus Berkas">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                  </a>
                </div>
              </div>

            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </div>

  </div>

</div>

<script>
function copyMediaUrl(path) {
  navigator.clipboard.writeText(path).then(() => {
    alert('URL Berkas berhasil disalin ke clipboard:\n' + path);
  }).catch(() => {
    prompt('Salin URL berikut secara manual:', path);
  });
}
</script>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
