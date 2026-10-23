<?php
$page_title = 'Role & Permission Matrix (RBAC)';
require_once __DIR__ . '/layout_top.php';

$success = '';
$error = '';

// 1. Tambah Role Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_role') {
    $nama_role = trim($_POST['nama_role'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', $nama_role));

    if (empty($nama_role)) {
        $error = 'Nama role tidak boleh kosong.';
    } else {
        // Cek duplikasi
        $stmt_chk = $pdo->prepare("SELECT id FROM roles WHERE slug = ?");
        $stmt_chk->execute([$slug]);
        if ($stmt_chk->fetch()) {
            $error = 'Role dengan nama ini sudah ada.';
        } else {
            $stmt_ins = $pdo->prepare("INSERT INTO roles (slug, nama_role, deskripsi, is_system) VALUES (?, ?, ?, 0)");
            if ($stmt_ins->execute([$slug, $nama_role, $deskripsi])) {
                $new_role_id = $pdo->lastInsertId();
                logActivity($pdo, $_SESSION['admin_id'], 'CREATE_ROLE', 'RBAC Matrix', $new_role_id, "Membuat role baru: {$nama_role} ($slug)");
                $success = "Role '{$nama_role}' berhasil ditambahkan! Silakan atur izin akses modul di bawah.";
            } else {
                $error = 'Gagal menyimpan role baru.';
            }
        }
    }
}

// 2. Simpan Perubahan Matrix Izin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_permissions') {
    $target_role_id = (int)($_POST['role_id'] ?? 0);
    $selected_permissions = $_POST['permissions'] ?? [];

    if ($target_role_id <= 0) {
        $error = 'Role tidak valid.';
    } else {
        // Hapus permission lama role ini
        $stmt_del = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt_del->execute([$target_role_id]);

        // Masukkan yang dipilih
        if (!empty($selected_permissions) && is_array($selected_permissions)) {
            $stmt_ins_perm = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($selected_permissions as $perm_id) {
                $stmt_ins_perm->execute([$target_role_id, (int)$perm_id]);
            }
        }

        logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_ROLE_PERMISSIONS', 'RBAC Matrix', $target_role_id, "Memperbarui matrix izin role ID {$target_role_id} (" . count($selected_permissions) . " izin diberikan)");
        $success = "Matriks izin akses berhasil diperbarui!";
    }
}

// 3. Hapus Custom Role
if (isset($_GET['del_role'])) {
    $del_id = (int)$_GET['del_role'];
    $stmt_r = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
    $stmt_r->execute([$del_id]);
    $role_to_del = $stmt_r->fetch();

    if ($role_to_del) {
        if ($role_to_del['is_system'] == 1 || $role_to_del['id'] == 1) {
            $error = "Role sistem bawaan tidak dapat dihapus demi keamanan.";
        } else {
            $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$del_id]);
            $pdo->prepare("DELETE FROM roles WHERE id = ?")->execute([$del_id]);
            logActivity($pdo, $_SESSION['admin_id'], 'DELETE_ROLE', 'RBAC Matrix', $del_id, "Menghapus custom role: {$role_to_del['nama_role']}");
            $success = "Role '{$role_to_del['nama_role']}' berhasil dihapus.";
        }
    }
}

// Ambil semua roles
$roles = $pdo->query("SELECT r.*, COUNT(rp.permission_id) as total_perms 
                      FROM roles r 
                      LEFT JOIN role_permissions rp ON r.id = rp.role_id 
                      GROUP BY r.id ORDER BY r.id ASC")->fetchAll();

// Role aktif yang sedang disetting di tab
$active_role_id = (int)($_GET['role_id'] ?? ($roles[1]['id'] ?? 2));
$active_role = null;
foreach ($roles as $r) {
    if ($r['id'] == $active_role_id) {
        $active_role = $r;
        break;
    }
}
if (!$active_role && !empty($roles)) {
    $active_role = $roles[0];
    $active_role_id = $active_role['id'];
}

// Ambil seluruh permissions yang tersedia dikelompokkan per modul
$stmt_all_perms = $pdo->query("SELECT * FROM permissions ORDER BY module ASC, id ASC");
$all_permissions = $stmt_all_perms->fetchAll();
$grouped_perms = [];
foreach ($all_permissions as $p) {
    $grouped_perms[$p['module']][] = $p;
}

// Ambil permission yang saat ini dimiliki oleh role aktif
$current_perms_stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
$current_perms_stmt->execute([$active_role_id]);
$current_perm_ids = $current_perms_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<div class="space-y-6">

  <?php if ($success): ?>
    <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center gap-3">
      <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
      <span class="font-medium text-xs sm:text-sm"><?= htmlspecialchars($success); ?></span>
    </div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center gap-3">
      <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
      <span class="font-medium text-xs sm:text-sm"><?= htmlspecialchars($error); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Overview Card -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-shield-halved"></i>
        <span>Security Architecture: Role-Based Access Control</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">Dynamic Role & Permission Matrix</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Kelola hierarki wewenang sistem institusi. Buat role baru spesifik per divisi kampus (Panitia PMB, Keuangan, Jurnalis, Humas) dan tentukan hak akses CRUD untuk setiap modul secara fleksibel tanpa hardcode.
      </p>
    </div>
    <i class="fa-solid fa-user-shield text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Kolom Kiri: Daftar Role & Tambah Role Baru -->
    <div class="space-y-6">

      <!-- Box Tambah Role -->
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
          <i class="fa-solid fa-plus-circle text-teal-600"></i>
          <span>Buat Role Baru</span>
        </h3>
        <form action="rbac_matrix.php" method="POST" class="space-y-4">
          <input type="hidden" name="action" value="add_role">
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Role Divisi</label>
            <input type="text" name="nama_role" placeholder="Contoh: Panitia Keuangan, Humas Kampus" required class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:outline-none">
          </div>
          <div>
            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi & Cakupan Tugas</label>
            <textarea name="deskripsi" rows="2" placeholder="Menangani verifikasi bukti bayar dan skema UKT mahasiswa..." class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-teal-500 focus:outline-none"></textarea>
          </div>
          <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
            <i class="fa-solid fa-check"></i>
            <span>Simpan Role Baru</span>
          </button>
        </form>
      </div>

      <!-- List Roles -->
      <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center justify-between">
          <span>Daftar Role Aktif</span>
          <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-bold"><?= count($roles); ?> Roles</span>
        </h3>
        <p class="text-xs text-slate-500 mb-4">Pilih salah satu role untuk mengedit matriks izin aksesnya:</p>

        <div class="space-y-2">
          <?php foreach ($roles as $r): 
            $is_selected = ($r['id'] == $active_role_id);
          ?>
            <div class="p-3.5 rounded-2xl border transition flex items-center justify-between <?= $is_selected ? 'bg-teal-50/80 border-teal-500 shadow-sm' : 'bg-slate-50 border-slate-200 hover:bg-slate-100/60' ?>">
              <a href="rbac_matrix.php?role_id=<?= $r['id']; ?>" class="flex-1 min-w-0 pr-3 block">
                <div class="flex items-center gap-2">
                  <span class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($r['nama_role']); ?></span>
                  <?php if ($r['is_system']): ?>
                    <span class="text-[9px] bg-slate-200 text-slate-700 font-bold px-1.5 py-0.5 rounded uppercase">System</span>
                  <?php endif; ?>
                </div>
                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-0.5">
                  <span>Slug: <code class="text-teal-700 font-semibold"><?= htmlspecialchars($r['slug']); ?></code></span>
                  <span>&bull;</span>
                  <span><?= $r['id'] == 1 ? 'Akses Penuh (Bypass)' : ($r['total_perms'] . ' Izin') ?></span>
                </div>
              </a>

              <?php if (!$r['is_system'] && $r['id'] != 1): ?>
                <a href="rbac_matrix.php?del_role=<?= $r['id']; ?>" onclick="return confirm('Hapus role <?= htmlspecialchars($r['nama_role']); ?>? Seluruh izin terkait akan dihapus.')" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition" title="Hapus Role">
                  <i class="fa-solid fa-trash-can text-xs"></i>
                </a>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>

    <!-- Kolom Kanan: Matrix Permissions Form -->
    <div class="lg:col-span-2">
      <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 mb-6 border-b border-slate-100 gap-3">
          <div>
            <span class="text-[11px] uppercase tracking-wider font-bold text-teal-700">Konfigurasi Hak Akses</span>
            <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
              <span><?= htmlspecialchars($active_role['nama_role']); ?></span>
              <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-teal-100 text-teal-800">
                ID: <?= $active_role['id']; ?>
              </span>
            </h3>
            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($active_role['deskripsi'] ?: 'Tidak ada keterangan.'); ?></p>
          </div>

          <?php if ($active_role['id'] == 1): ?>
            <div class="px-4 py-2 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-2xl text-xs font-bold flex items-center gap-2">
              <i class="fa-solid fa-unlock-keyhole text-emerald-600"></i>
              <span>Super Administrator memiliki seluruh hak akses secara otomatis (God Mode Bypass).</span>
            </div>
          <?php endif; ?>
        </div>

        <form action="rbac_matrix.php?role_id=<?= $active_role_id; ?>" method="POST" class="space-y-8">
          <input type="hidden" name="action" value="save_permissions">
          <input type="hidden" name="role_id" value="<?= $active_role_id; ?>">

          <?php 
          $module_labels = [
            'pmb' => ['title' => 'Modul PMB & Pendaftaran', 'icon' => 'fa-graduation-cap', 'desc' => 'Kelola pelamar, verifikasi berkas ujian, dan keputusan kelulusan PMB'],
            'cms' => ['title' => 'Modul CMS & Tata Letak Halaman', 'icon' => 'fa-layer-group', 'desc' => 'Section homepage, form builder, navigasi menu, dan multimedia'],
            'publish' => ['title' => 'Modul Publikasi Berita & Pengumuman', 'icon' => 'fa-newspaper', 'desc' => 'Penyusunan artikel, kategori, dan izin penerbitan publikasi'],
            'academic' => ['title' => 'Modul Master Data Akademik', 'icon' => 'fa-building-columns', 'desc' => 'Fakultas, program studi, kuota, dan direktori dosen/staf'],
            'system' => ['title' => 'Modul Konfigurasi Sistem & Server', 'icon' => 'fa-gears', 'desc' => 'Audit log, integrasi SMTP/Payment, dan manajemen pengguna'],
          ];

          foreach ($grouped_perms as $mod => $perms): 
            $meta = $module_labels[$mod] ?? ['title' => 'Modul ' . strtoupper($mod), 'icon' => 'fa-folder', 'desc' => 'Hak akses modul ' . $mod];
          ?>
            <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-5">
              <div class="flex items-center justify-between mb-3 border-b border-slate-200/60 pb-3">
                <div class="flex items-center gap-2.5">
                  <div class="w-7 h-7 rounded-lg bg-teal-800 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid <?= $meta['icon']; ?>"></i>
                  </div>
                  <div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-800"><?= $meta['title']; ?></h4>
                    <p class="text-[11px] text-slate-500"><?= $meta['desc']; ?></p>
                  </div>
                </div>
                <button type="button" onclick="toggleModuleCheckboxes(this)" class="text-[11px] font-bold text-teal-700 hover:text-teal-900 transition">
                  Pilih Semua
                </button>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2">
                <?php foreach ($perms as $p): 
                  $is_checked = in_array($p['id'], $current_perm_ids) || ($active_role['id'] == 1);
                  $disabled = ($active_role['id'] == 1);
                ?>
                  <label class="flex items-start gap-3 p-3 bg-white rounded-xl border border-slate-200 hover:border-teal-400 transition cursor-pointer select-none">
                    <input type="checkbox" name="permissions[]" value="<?= $p['id']; ?>" <?= $is_checked ? 'checked' : ''; ?> <?= $disabled ? 'disabled' : ''; ?> class="mt-0.5 w-4 h-4 text-teal-600 rounded border-slate-300 focus:ring-teal-500">
                    <div class="min-w-0">
                      <span class="block text-xs font-bold text-slate-800"><?= htmlspecialchars($p['nama_permission']); ?></span>
                      <span class="block text-[11px] text-slate-500 leading-tight"><?= htmlspecialchars($p['deskripsi'] ?: ('Aksi: ' . $p['action'])); ?></span>
                    </div>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>

          <?php if ($active_role['id'] != 1): ?>
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
              <button type="submit" class="px-6 py-3 bg-teal-800 hover:bg-teal-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-teal-900/20 transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i>
                <span>Simpan Matriks Izin Akses</span>
              </button>
            </div>
          <?php endif; ?>

        </form>

      </div>
    </div>

  </div>

</div>

<script>
function toggleModuleCheckboxes(btn) {
  const container = btn.closest('.bg-slate-50\\/70');
  const checkboxes = container.querySelectorAll('input[type="checkbox"]:not([disabled])');
  const allChecked = Array.from(checkboxes).every(cb => cb.checked);
  checkboxes.forEach(cb => cb.checked = !allChecked);
  btn.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih';
}
</script>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
