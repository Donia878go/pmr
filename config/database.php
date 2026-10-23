<?php
/**
 * Konfigurasi Koneksi Database MySQL XAMPP
 * Politeknik Mulia Rokan - PMB
 */

$host     = 'localhost';
$username = 'root';
$password = '';
$database = 'pmb_mulia_rokan';

try {
    $pdo = new PDO("mysql:host={$host};dbname={$database};charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Tampilkan panduan ramah jika database belum diimport di phpMyAdmin
    die("<div style='font-family: sans-serif; padding: 30px; max-width: 650px; margin: 40px auto; border: 2px solid #ef4444; border-radius: 12px; background: #fef2f2;'>
        <h2 style='color: #b91c1c; margin-top: 0;'>Koneksi Database Gagal!</h2>
        <p>Aplikasi tidak dapat terhubung ke database <strong>{$database}</strong>.</p>
        <p><strong>Langkah Mengatasi di XAMPP:</strong></p>
        <ol style='line-height: 1.6;'>
            <li>Pastikan modul <strong>Apache</strong> dan <strong>MySQL</strong> sudah berjalan di XAMPP Control Panel.</li>
            <li>Buka browser dan akses <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a>.</li>
            <li>Buat database baru bernama <code>{$database}</code>.</li>
            <li>Import file <code>database/pmb_mulia_rokan.sql</code> ke dalam database tersebut.</li>
            <li>Refresh halaman ini kembali.</li>
        </ol>
        <small style='color: #6b7280;'>Pesan Teknis: " . htmlspecialchars($e->getMessage()) . "</small>
    </div>");
}

// Base URL helper
function base_url($path = '') {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // normalisasi subfolder jika ada
    $base = rtrim($protocol . $host . $script, '/');
    // jika berada di subfolder admin, naik satu tingkat
    if (basename($base) === 'admin') {
        $base = dirname($base);
    }
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

// Generate Nomor Pendaftaran Otomatis: PMB-2026-XXXX
function generateNoPendaftaran($pdo) {
    $tahun = date('Y');
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM pendaftar");
    $row = $stmt->fetch();
    $next_id = ($row && $row['max_id']) ? $row['max_id'] + 1 : 1;
    return 'PMB-' . $tahun . '-' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
}

// Format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Format Tanggal Indonesia
function formatTanggalIndo($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $pecahkan = explode('-', $tanggal);
    if (count($pecahkan) === 3) {
        return $pecahkan[2] . ' ' . ($bulan[(int)$pecahkan[1]] ?? '') . ' ' . $pecahkan[0];
    }
    return $tanggal;
}

// Ambil Pengaturan Situs Dinamis
function getSiteSettings($pdo) {
    static $settings = null;
    if ($settings === null) {
        try {
            $stmt = $pdo->query("SELECT * FROM site_settings WHERE id = 1");
            $settings = $stmt->fetch();
        } catch (Exception $e) {
            $settings = [];
        }
    }
    return $settings ?: [];
}

// Ambil Akun Sosial Media Aktif
function getSocialMedia($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM social_media WHERE is_active = 1 ORDER BY urutan ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Ambil Menu Navigasi Bertingkat (Dropdown)
function getNavMenus($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM custom_menus WHERE is_active = 1 ORDER BY urutan ASC");
        $all = $stmt->fetchAll();
        $parents = [];
        $children = [];
        foreach ($all as $m) {
            if (empty($m['parent_id'])) {
                $parents[] = $m;
            } else {
                $children[$m['parent_id']][] = $m;
            }
        }
        foreach ($parents as &$p) {
            $p['children'] = $children[$p['id']] ?? [];
        }
        return $parents;
    } catch (Exception $e) {
        return [];
    }
}

// Ambil Fitur Unggulan Dinamis
function getFiturUnggulan($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM fitur_unggulan WHERE is_active = 1 ORDER BY urutan ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Ambil Video Profil & Galeri
function getVideoKampus($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM video_kampus ORDER BY is_featured DESC, urutan ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Log Aktivitas Super Admin & Admin (Audit Trail)
function logActivity($pdo, $admin_id, $action, $module, $target_id = null, $detail = null) {
    try {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
        
        $uname = 'system';
        $urole = 'superadmin';
        if ($admin_id) {
            $u_stmt = $pdo->prepare("SELECT username, role FROM admin_users WHERE id = ?");
            $u_stmt->execute([$admin_id]);
            $u_row = $u_stmt->fetch();
            if ($u_row) {
                $uname = $u_row['username'];
                $urole = $u_row['role'];
            }
        }
        $full_detail = $detail ?? $action;
        if ($target_id) {
            $full_detail = "[Target ID: {$target_id}] " . $full_detail;
        }

        $stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, username, user_role, module, action, detail, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$admin_id, $uname, $urole, $module, $action, $full_detail, $ip, $user_agent]);
    } catch (Exception $e) {
        // Fallback silently if table not reachable to avoid breaking core flows
    }
}

// Cek Hak Akses Role (RBAC)
function hasPermission($pdo, $role_id, $permission_slug) {
    // Role 1 (superadmin) has all permissions
    if ($role_id == 1) return true;
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM role_permissions rp 
                               JOIN permissions p ON rp.permission_id = p.id 
                               WHERE rp.role_id = ? AND p.slug = ?");
        $stmt->execute([$role_id, $permission_slug]);
        return (int)$stmt->fetchColumn() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Ambil Seksi Homepage Dinamis
function getHomepageSections($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM homepage_sections WHERE is_active = 1 ORDER BY urutan ASC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

// Periksa Mode Maintenance
function checkMaintenanceMode($pdo) {
    $settings = getSiteSettings($pdo);
    if (!empty($settings['is_maintenance'])) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Superadmin bypass
        if (!empty($_SESSION['admin_logged_in']) && (($_SESSION['admin_role'] ?? '') === 'superadmin' || ($_SESSION['admin_role_id'] ?? 0) == 1)) {
            return; // bypass maintenance
        }
        // Redirect to maintenance page
        if (basename($_SERVER['SCRIPT_NAME']) !== 'maintenance.php' && !str_contains($_SERVER['SCRIPT_NAME'], '/super-admin') && !str_contains($_SERVER['SCRIPT_NAME'], '/admin')) {
            http_response_code(503);
            header("Retry-After: 3600");
            include __DIR__ . '/../maintenance.php';
            exit;
        }
    }
}
?>