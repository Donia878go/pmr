<?php
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/database.php';

$target_id = (int)($_GET['id'] ?? 0);

if (!$target_id) {
    header('Location: kelola_user.php?err=User+tidak+valid');
    exit;
}

// Ambil user target
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->execute([$target_id]);
$target_user = $stmt->fetch();

if (!$target_user) {
    header('Location: kelola_user.php?err=User+tidak+ditemukan');
    exit;
}

if ($target_user['id'] == $_SESSION['admin_id']) {
    header('Location: kelola_user.php?err=Anda+sudah+login+sebagai+user+ini');
    exit;
}

// Simpan sesi superadmin asli untuk restore
if (empty($_SESSION['impersonate_mode'])) {
    $_SESSION['impersonate_mode'] = true;
    $_SESSION['original_admin_id'] = $_SESSION['admin_id'];
    $_SESSION['original_admin_username'] = $_SESSION['admin_username'];
    $_SESSION['original_admin_nama'] = $_SESSION['admin_nama'];
    $_SESSION['original_admin_role'] = $_SESSION['admin_role'];
    $_SESSION['original_admin_role_id'] = $_SESSION['admin_role_id'] ?? 1;
}

// Log audit trail
logActivity($pdo, $_SESSION['original_admin_id'], 'IMPERSONATE_USER', 'User Management', $target_user['id'], "Super Admin impersonated user {$target_user['username']} ({$target_user['nama_lengkap']})");

// Switch session ke target user
$_SESSION['admin_id'] = $target_user['id'];
$_SESSION['admin_username'] = $target_user['username'];
$_SESSION['admin_nama'] = $target_user['nama_lengkap'];
$_SESSION['admin_role'] = $target_user['role'];
$_SESSION['admin_role_id'] = $target_user['role_id'] ?? 2;

// Redirect sesuai role target user
if ($target_user['role'] === 'superadmin') {
    header('Location: index.php?msg=Berhasil+impersonate+user+' . urlencode($target_user['nama_lengkap']));
} else {
    header('Location: ../admin/index.php?msg=Berhasil+impersonate+user+' . urlencode($target_user['nama_lengkap']));
}
exit;
