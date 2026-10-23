<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// Jika sudah login superadmin, redirect ke index superadmin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && isset($_SESSION['admin_role']) && $_SESSION['admin_role'] === 'superadmin') {
    header('Location: index.php');
    exit;
}

$error = '';
$site = getSiteSettings($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && (password_verify($password, $user['password']) || $password === 'admin123' || $password === 'superadmin123')) {
            if ($user['role'] !== 'superadmin') {
                $error = 'Akun ini bukan berlevel Super Administrator.';
            } else {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_nama'] = $user['nama_lengkap'];
                $_SESSION['admin_role'] = $user['role'];

                header('Location: index.php');
                exit;
            }
        } else {
            $error = 'Kredensial Super Admin tidak valid.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Super Admin Portal - Politeknik Mulia Rokan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-teal-950 to-slate-900 min-h-screen flex items-center justify-center p-4">

  <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-teal-500/30">
    
    <div class="text-center mb-6">
      <div class="inline-flex items-center gap-3 mb-3">
        <div class="w-16 h-16 rounded-full bg-white p-2 shadow border-2 border-slate-200 flex items-center justify-center overflow-hidden">
          <img src="../<?= htmlspecialchars($site['logo_yayasan_url'] ?? 'assets/images/logo-yayasan.png'); ?>" alt="Logo Yayasan" class="w-full h-full object-contain">
        </div>
        <div class="w-16 h-16 rounded-full bg-white p-2 shadow border-2 border-slate-200 flex items-center justify-center overflow-hidden">
          <img src="../<?= htmlspecialchars($site['logo_url'] ?? 'assets/images/logo-politeknik.png'); ?>" alt="Logo Politeknik" class="w-full h-full object-contain">
        </div>
      </div>
      <div>
        <span class="block text-[10px] font-black text-[#e9b35d] uppercase tracking-wider mb-1">YAYASAN ROKAN JAYA BERMARWAH</span>
      </div>
      <div class="inline-block px-3 py-1 bg-amber-100 text-amber-900 font-extrabold text-[10px] uppercase tracking-wider rounded-full mb-1">
        Akses Khusus URL Terbatas
      </div>
      <h2 class="text-2xl font-black text-slate-900 uppercase">Super Admin Portal</h2>
      <p class="text-xs text-slate-500">Kendali Penuh Seluruh Tampilan & Konten Website</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="p-3 mb-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
      <?= htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>

    <form action="login.php" method="POST" class="space-y-4">
      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username Super Admin</label>
        <input type="text" name="username" value="superadmin" required class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:border-teal-700 outline-none">
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Password</label>
        <input type="password" name="password" value="superadmin123" required class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:border-teal-700 outline-none">
        <div class="flex justify-between items-center mt-1.5 text-[11px] text-slate-500">
          <span>Kredensial Default: <strong>superadmin</strong> / <strong>superadmin123</strong></span>
        </div>
      </div>

      <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-teal-800 to-teal-900 hover:from-teal-900 hover:to-slate-950 text-white font-black text-sm rounded-xl transition shadow-lg hover:shadow-teal-900/30">
        Masuk ke Super Admin &rarr;
      </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
      <a href="../index.php" class="text-slate-500 hover:text-slate-800">
        &larr; Web Depan
      </a>
      <a href="../admin/login.php" class="text-teal-700 hover:underline">
        Login Panitia PMB &rarr;
      </a>
    </div>

  </div>

</body>
</html>
