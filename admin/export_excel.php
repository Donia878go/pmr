<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$filename = "Data_Pendaftar_PMB_Mulia_Rokan_" . date('Ymd_His') . ".csv";

header("Content-Type: text/csv; charset=UTF-8");
header('Content-Disposition: attachment; filename="' . $filename . '"');
header("Pragma: no-cache");
header("Expires: 0");

$output = fopen("php://output", "w");

// UTF-8 BOM untuk Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header Kolom
fputcsv($output, [
    'No',
    'No Pendaftaran',
    'Nama Lengkap',
    'NIK',
    'Jenis Kelamin',
    'Tempat Lahir',
    'Tanggal Lahir',
    'No WhatsApp',
    'Email',
    'Alamat',
    'Asal Sekolah',
    'Jurusan Sekolah',
    'Tahun Lulus',
    'Jalur Pendaftaran',
    'Pilihan Prodi 1',
    'Pilihan Prodi 2',
    'Status Seleksi',
    'Catatan Panitia',
    'Tanggal Mendaftar'
], ';');

// Query Data
$sql = "SELECT p.*, j.nama_jalur, p1.nama_prodi as prodi1_nama, p2.nama_prodi as prodi2_nama 
        FROM pendaftar p 
        LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
        LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
        LEFT JOIN program_studi p2 ON p.prodi_pilihan_2 = p2.id 
        ORDER BY p.id ASC";
$stmt = $pdo->query($sql);
$no = 1;

while ($row = $stmt->fetch()) {
    fputcsv($output, [
        $no++,
        $row['no_pendaftaran'],
        $row['nama_lengkap'],
        "'" . $row['nik'],
        $row['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan',
        $row['tempat_lahir'],
        $row['tanggal_lahir'],
        "'" . $row['no_whatsapp'],
        $row['email'],
        $row['alamat'],
        $row['asal_sekolah'],
        $row['jurusan_sekolah'],
        $row['tahun_lulus'],
        $row['nama_jalur'],
        $row['prodi1_nama'],
        $row['prodi2_nama'] ?? '-',
        strtoupper($row['status_seleksi']),
        $row['catatan_panitia'],
        $row['created_at']
    ], ';');
}

fclose($output);
exit;
?>