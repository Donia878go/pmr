<?php
$page_title = "User Central Management & Impersonation";
require_once __DIR__ . '/layout_top.php';

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

// Ambil semua role yang tersedia untuk dropdown
$available_roles = $pdo->query("SELECT * FROM roles ORDER BY id ASC")->fetchAll();

// 1. Tambah Pengguna Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
    $username     = trim($_POST['username'] ?? '');
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $role_id      = (int)($_POST['role_id'] ?? 2);

    if (empty($username) || empty($password) || empty($nama_lengkap)) {
        $error = "Kolom Username, Nama Lengkap, dan Password wajib diisi!";
    } else {
        $cek = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
        $cek->execute([$username]);
        if ($cek->fetch()) {
            $error = "Username '{$username}' sudah digunakan oleh akun lain!";
        } else {
            // Ambil role slug
            $r_stmt = $pdo->prepare("SELECT slug FROM roles WHERE id = ?");
            $r_stmt->execute([$role_id]);
            $role_slug = $r_stmt->fetchColumn() ?: 'panitia';

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, nama_lengkap, email, role, role_id, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'aktif', NOW())");
            if ($stmt->execute([$username, $hash, $nama_lengkap, $email, $role_slug, $role_id])) {
                $new_id = $pdo->lastInsertId();
                logActivity($pdo, $_SESSION['admin_id'], 'CREATE_USER', 'User Management', $new_id, "Menambahkan pengguna baru: {$username} ({$nama_lengkap}) Role ID: {$role_id}");
                $message = "Akun pengguna baru berhasil ditambahkan!";
            }
        }
    }
}

// 2. Suspend / Aktifkan User
if (isset($_GET['toggle_status'])) {
    $target_uid = (int)$_GET['toggle_status'];
    if ($target_uid === (int)$_SESSION['admin_id']) {
        $error = "Anda tidak dapat mengubah status akun Anda sendiri yang sedang aktif!";
    } else {
        $u_stmt = $pdo->prepare("SELECT id, username, status FROM admin_users WHERE id = ?");
        $u_stmt->execute([$target_uid]);
        $target_data = $u_stmt->fetch();

        if ($target_data) {
            $new_status = ($target_data['status'] === 'aktif') ? 'suspended' : 'aktif';
            $up_stmt = $pdo->prepare("UPDATE admin_users SET status = ? WHERE id = ?");
            $up_stmt->execute([$new_status, $target_uid]);
            logActivity($pdo, $_SESSION['admin_id'], 'TOGGLE_USER_STATUS', 'User Management', $target_uid, "Mengubah status user {$target_data['username']} menjadi {$new_status}");
            $message = "Status akun '{$target_data['username']}' berhasil diubah menjadi: " . strtoupper($new_status);
        }
    }
}

// 3. Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_pass') {
    $user_id  = (int)$_POST['user_id'];
    $new_pass = trim($_POST['new_password'] ?? '');

    if (!empty($new_pass)) {
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hash, $user_id])) {
            logActivity($pdo, $_SESSION['admin_id'], 'RESET_PASSWORD', 'User Management', $user_id, "Mereset kata sandi akun ID {$user_id}");
            $message = "Kata sandi akun pengguna berhasil diperbarui!";
        }
    }
}

// 4. Update Role Pengguna
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $user_id = (int)$_POST['user_id'];
    $new_role_id = (int)$_POST['role_id'];

    if ($user_id === (int)$_SESSION['admin_id'] && $new_role_id != 1) {
        $error = "Anda tidak dapat menurunkan hak akses akun Super Admin Anda sendiri!";
    } else {
        $r_stmt = $pdo->prepare("SELECT slug FROM roles WHERE id = ?");
        $r_stmt->execute([$new_role_id]);
        $role_slug = $r_stmt->fetchColumn() ?: 'panitia';

        $stmt = $pdo->prepare("UPDATE admin_users SET role_id = ?, role = ? WHERE id = ?");
        if ($stmt->execute([$new_role_id, $role_slug, $user_id])) {
            logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_USER_ROLE', 'User Management', $user_id, "Mengubah role akun ID {$user_id} ke role ID {$new_role_id} ({$role_slug})");
            $message = "Role hak akses pengguna berhasil diubah!";
        }
    }
}

// 5. Hapus User
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($del_id === (int)$_SESSION['admin_id']) {
        $error = "Anda tidak dapat menghapus akun Anda sendiri!";
    } else {
        $pdo->prepare("DELETE FROM admin_users WHERE id = ?")->execute([$del_id]);
        logActivity($pdo, $_SESSION['admin_id'], 'DELETE_USER', 'User Management', $del_id, "Menghapus akun pengguna ID {$del_id}");
        $message = "Akun pengguna berhasil dihapus dari sistem.";
    }
}

// Ambil daftar seluruh user beserta nama role
$users = $pdo->query("SELECT u.*, r.nama_role 
                      FROM admin_users u 
                      LEFT JOIN roles r ON u.role_id = r.id 
                      ORDER BY u.id ASC")->fetchAll();
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

  <!-- Top Hero Info -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-rose-500/20 text-rose-300 border border-rose-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-users-gear"></i>
        <span>Security & Access Control Center</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">User Central Management & Impersonation</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Kelola dan pantau seluruh akun internal institusi. Anda dapat membuat akun baru, memblokir/suspend akun secara instan, mereset password, serta melakukan <strong>User Impersonation</strong> untuk masuk sementara sebagai staf admin tanpa kata sandi demi pengujian alur kerja.
      </p>
    </div>
    <i class="fa-solid fa-user-lock text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
    
    <!-- Form Tambah User Baru -->
    <div class="lg:col-span-4 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider pb-3 border-b border-slate-100 flex items-center gap-2">
        <i class="fa-solid fa-user-plus text-teal-600 text-sm"></i>
        <span>Buat Akun Pengguna Baru</span>
      </h3>

      <form action="kelola_user.php" method="POST" class="space-y-3.5">
        <input type="hidden" name="action" value="add_user">

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Username Login *</label>
          <input type="text" name="username" required placeholder="Contoh: panitia_pmb2" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Lengkap & Gelar *</label>
          <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Santoso, S.Kom" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Alamat Email Resmi</label>
          <input type="email" name="email" placeholder="Contoh: budi@politeknikmuliarokan.ac.id" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password Sementara *</label>
          <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tingkat Hak Akses (Role) *</label>
          <select name="role_id" required class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white font-semibold">
            <?php foreach ($available_roles as $ar): ?>
              <option value="<?= $ar['id']; ?>">
                <?= htmlspecialchars($ar['nama_role']); ?> (<?= htmlspecialchars($ar['slug']); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition flex items-center justify-center gap-2">
          <i class="fa-solid fa-check"></i>
          <span>Daftarkan Pengguna Baru</span>
        </button>
      </form>
    </div>

    <!-- Daftar User Aktif & Action Hub -->
    <div class="lg:col-span-8 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
      <div class="flex items-center justify-between pb-3 border-b border-slate-100">
        <div>
          <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">
            Daftar Pengguna Sistem
          </h3>
          <p class="text-[11px] text-slate-500">Total <?= count($users); ?> akun terdaftar di sistem basis data.</p>
        </div>
        <a href="rbac_matrix.php" class="px-3 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
          <i class="fa-solid fa-shield-halved"></i>
          <span>Matriks RBAC &rarr;</span>
        </a>
      </div>

      <div class="space-y-4">
        <?php foreach ($users as $u): 
          $is_me = ($u['id'] === (int)$_SESSION['admin_id']);
          $is_suspended = ($u['status'] === 'suspended');
        ?>
          <div class="p-4 rounded-2xl border transition <?= $is_suspended ? 'bg-rose-50/50 border-rose-200' : 'bg-slate-50/80 border-slate-200' ?>">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/70">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center font-bold text-sm shrink-0 <?= $u['role'] === 'superadmin' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-teal-100 text-teal-900 border border-teal-300'; ?>">
                  <?= strtoupper(substr($u['nama_lengkap'], 0, 2)); ?>
                </div>
                <div>
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="text-xs font-black text-slate-900"><?= htmlspecialchars($u['nama_lengkap']); ?></h4>
                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase <?= $u['role'] === 'superadmin' ? 'bg-amber-100 text-amber-900' : 'bg-teal-100 text-teal-800'; ?>">
                      <?= htmlspecialchars($u['nama_role'] ?: $u['role']); ?>
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= $is_suspended ? 'bg-rose-200 text-rose-900 font-black animate-pulse' : 'bg-emerald-100 text-emerald-800'; ?>">
                      <?= $u['status'] ?? 'aktif'; ?>
                    </span>
                  </div>
                  <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-0.5 flex-wrap">
                    <span>User: <code class="font-bold text-slate-700"><?= htmlspecialchars($u['username']); ?></code></span>
                    <?php if (!empty($u['email'])): ?>
                      <span>&bull;</span>
                      <span><?= htmlspecialchars($u['email']); ?></span>
                    <?php endif; ?>
                    <span>&bull;</span>
                    <span>Login Terakhir: <?= $u['last_login'] ? htmlspecialchars($u['last_login']) : 'Belum pernah'; ?></span>
                  </div>
                </div>
              </div>

              <!-- Quick Action Buttons -->
              <div class="flex items-center gap-2 flex-wrap sm:justify-end">
                <?php if (!$is_me): ?>
                  <!-- Impersonate Button -->
                  <a href="impersonate.php?id=<?= $u['id']; ?>" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm" title="Masuk Sementara Sebagai Pengguna Ini Tanpa Password">
                    <i class="fa-solid fa-masks-theater text-xs"></i>
                    <span>Impersonate</span>
                  </a>

                  <!-- Suspend Toggle -->
                  <a href="kelola_user.php?toggle_status=<?= $u['id']; ?>" class="px-3 py-1.5 <?= $is_suspended ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-600 hover:text-white' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-600 hover:text-white'; ?> border rounded-xl text-xs font-bold transition flex items-center gap-1 shadow-sm">
                    <i class="fa-solid <?= $is_suspended ? 'fa-lock-open' : 'fa-ban'; ?> text-xs"></i>
                    <span><?= $is_suspended ? 'Aktifkan' : 'Suspend'; ?></span>
                  </a>

                  <!-- Delete -->
                  <a href="kelola_user.php?delete=<?= $u['id']; ?>" onclick="return confirm('Hapus permanen akun <?= htmlspecialchars($u['nama_lengkap']); ?>?')" class="p-1.5 text-rose-500 hover:text-white hover:bg-rose-600 rounded-lg transition" title="Hapus Akun">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                  </a>
                <?php else: ?>
                  <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-200 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>Akun Anda Saat Ini</span>
                  </span>
                <?php endif; ?>
              </div>
            </div>

            <!-- Form Ubah Role & Reset Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3">
              <!-- Ganti Role -->
              <form action="kelola_user.php" method="POST" class="flex items-center gap-2">
                <input type="hidden" name="action" value="update_role">
                <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                <select name="role_id" class="w-full px-2.5 py-1 text-[11px] border border-slate-300 rounded-lg bg-white font-medium" <?= ($is_me && $u['role_id'] == 1) ? 'disabled' : ''; ?>>
                  <?php foreach ($available_roles as $ar): ?>
                    <option value="<?= $ar['id']; ?>" <?= ($u['role_id'] == $ar['id'] || ($u['role'] === $ar['slug'])) ? 'selected' : ''; ?>>
                      Ubah Role: <?= htmlspecialchars($ar['nama_role']); ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="px-2.5 py-1 bg-slate-800 hover:bg-slate-900 text-white font-bold text-[11px] rounded-lg shrink-0" <?= ($is_me && $u['role_id'] == 1) ? 'disabled' : ''; ?>>
                  Simpan
                </button>
              </form>

              <!-- Reset Password -->
              <form action="kelola_user.php" method="POST" class="flex items-center gap-2">
                <input type="hidden" name="action" value="reset_pass">
                <input type="hidden" name="user_id" value="<?= $u['id']; ?>">
                <input type="password" name="new_password" required placeholder="Ganti password..." class="w-full px-2.5 py-1 text-[11px] border border-slate-300 rounded-lg bg-white">
                <button type="submit" class="px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white font-bold text-[11px] rounded-lg shrink-0">
                  Reset
                </button>
              </form>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
