<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if (empty($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'superadmin') {
    die("<div style='font-family: sans-serif; padding: 40px; text-align: center; max-width: 600px; margin: 50px auto; border: 1px solid #fecaca; background: #fef2f2; border-radius: 16px;'>
        <h2 style='color: #b91c1c;'>Akses Dibatasi Khusus Super Administrator</h2>
        <p style='color: #7f1d1d;'>Akun Anda memiliki hak akses <strong>Panitia PMB</strong>. Halaman ini hanya diperuntukkan bagi Super Administrator yang berwenang mengubah konten dan struktur web.</p>
        <p><a href='../admin/index.php' style='display: inline-block; padding: 10px 20px; background: #0f766e; color: #fff; text-decoration: none; border-radius: 8px; font-weight: bold;'>Kembali ke Panel Admin PMB</a></p>
    </div>");
}
?>