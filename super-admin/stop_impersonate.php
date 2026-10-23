<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

if (!empty($_SESSION['impersonate_mode']) && !empty($_SESSION['original_admin_id'])) {
    $orig_id = $_SESSION['original_admin_id'];
    
    // Log audit trail
    logActivity($pdo, $orig_id, 'STOP_IMPERSONATE', 'User Management', $_SESSION['admin_id'], "Super Admin stopped impersonating user " . ($_SESSION['admin_username'] ?? ''));

    // Restore original superadmin session
    $_SESSION['admin_id'] = $_SESSION['original_admin_id'];
    $_SESSION['admin_username'] = $_SESSION['original_admin_username'];
    $_SESSION['admin_nama'] = $_SESSION['original_admin_nama'];
    $_SESSION['admin_role'] = $_SESSION['original_admin_role'];
    $_SESSION['admin_role_id'] = $_SESSION['original_admin_role_id'];

    unset($_SESSION['impersonate_mode']);
    unset($_SESSION['original_admin_id']);
    unset($_SESSION['original_admin_username']);
    unset($_SESSION['original_admin_nama']);
    unset($_SESSION['original_admin_role']);
    unset($_SESSION['original_admin_role_id']);

    header('Location: kelola_user.php?msg=Sesi+Super+Admin+berhasil+dipulihkan');
    exit;
}

header('Location: index.php');
exit;
