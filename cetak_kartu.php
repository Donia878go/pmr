<?php
require_once __DIR__ . '/config/database.php';

$no = trim($_GET['no'] ?? '');

if (empty($no)) {
    die("Nomor pendaftaran tidak valid.");
}

$stmt = $pdo->prepare("SELECT p.*, j.nama_jalur, j.biaya_daftar, p1.nama_prodi as prodi1_nama, p1.jenjang as prodi1_jenjang, p2.nama_prodi as prodi2_nama, p2.jenjang as prodi2_jenjang 
                       FROM pendaftar p 
                       LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
                       LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
                       LEFT JOIN program_studi p2 ON p.prodi_pilihan_2 = p2.id 
                       WHERE p.no_pendaftaran = ?");
$stmt->execute([$no]);
$data = $stmt->fetch();

if (!$data) {
    die("Data pendaftar dengan nomor {$no} tidak ditemukan.");
}

$site = getSiteSettings($pdo);
$logo_img = !empty($site['logo_url']) ? $site['logo_url'] : 'assets/images/logo-resmi.jpg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kartu Tanda Peserta PMB 2026 - <?= htmlspecialchars($data['no_pendaftaran']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="bg-slate-100 py-8 px-4 font-sans text-slate-800">

  <!-- Print Button Bar -->
  <div class="max-w-2xl mx-auto mb-4 no-print flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200">
    <a href="cek_status.php?no_pendaftaran=<?= urlencode($data['no_pendaftaran']); ?>" class="text-xs font-bold text-slate-600 hover:text-primary">
      &larr; Kembali ke Portal
    </a>
    <button onclick="window.print()" class="px-5 py-2 bg-primary hover:bg-primary-dark text-white text-xs font-bold rounded-lg shadow transition">
      🖨️ Cetak / Simpan PDF
    </button>
  </div>

  <!-- Official Card Body -->
  <div class="max-w-2xl mx-auto bg-white border-2 border-slate-900 rounded-lg p-6 sm:p-8 shadow-md print-card">
    
    <!-- Header Kop Surat -->
    <div class="flex items-center gap-4 border-b-2 border-slate-900 pb-4 mb-6">
      <div class="w-16 h-16 rounded-full bg-white p-1 border border-slate-400 flex items-center justify-center shrink-0">
        <img src="<?= htmlspecialchars($logo_img); ?>" alt="Logo" class="w-full h-full object-contain">
      </div>
      <div class="flex-1 text-center">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Penerimaan Mahasiswa Baru</h3>
        <h1 class="text-lg sm:text-xl font-black uppercase text-slate-900 tracking-tight"><?= htmlspecialchars($site['nama_kampus'] ?? 'POLITEKNIK MULIA ROKAN'); ?></h1>
        <p class="text-[11px] text-slate-600"><?= htmlspecialchars($site['alamat'] ?? 'Kampus Utama Bagansiapiapi, Rokan Hilir, Riau'); ?></p>
        <p class="text-[10px] text-slate-500 font-medium">Email: <?= htmlspecialchars($site['email'] ?? 'pmb@politeknikmuliarokan.ac.id'); ?> | Helpdesk WA: <?= htmlspecialchars($site['whatsapp_helpdesk'] ?? '0812-3456-7890'); ?></p>
      </div>
      <div class="w-16 h-16 rounded-full bg-white p-1 border border-slate-400 flex items-center justify-center shrink-0">
        <img src="<?= htmlspecialchars($site['logo_yayasan_url'] ?? 'assets/images/logo-yayasan.png'); ?>" alt="Yayasan" class="w-full h-full object-contain">
      </div>
    </div>

    <!-- Title of Document -->
    <div class="text-center mb-6">
      <h2 class="text-base font-bold uppercase tracking-wider underline">KARTU TANDA PESERTA PMB TA 2026/2027</h2>
      <span class="text-xs font-mono font-bold bg-slate-100 px-3 py-1 rounded border border-slate-300 inline-block mt-1">
        NOMOR: <?= htmlspecialchars($data['no_pendaftaran']); ?>
      </span>
    </div>

    <!-- Main Info Grid -->
    <div class="grid grid-cols-12 gap-6 items-start mb-6">
      
      <!-- Pas Foto Box -->
      <div class="col-span-4 text-center">
        <div class="w-32 h-40 border-2 border-dashed border-slate-400 mx-auto rounded flex items-center justify-center bg-slate-50 overflow-hidden">
          <?php if (!empty($data['foto']) && file_exists(__DIR__ . '/uploads/' . $data['foto'])): ?>
            <img src="uploads/<?= htmlspecialchars($data['foto']); ?>" alt="Foto" class="w-full h-full object-cover">
          <?php else: ?>
            <div class="text-[10px] text-slate-400 p-2">
              PAS FOTO<br>3 x 4 cm
            </div>
          <?php endif; ?>
        </div>
        <span class="text-[10px] text-slate-500 block mt-2">Status: <strong><?= strtoupper($data['status_seleksi']); ?></strong></span>
      </div>

      <!-- Biodata Table -->
      <div class="col-span-8 text-xs space-y-2">
        <table class="w-full">
          <tr>
            <td class="w-32 py-1 font-semibold text-slate-600">Nama Lengkap</td>
            <td class="w-2">:</td>
            <td class="font-bold text-slate-900"><?= htmlspecialchars($data['nama_lengkap']); ?></td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">NIK</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['nik']); ?></td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">Jenis Kelamin</td>
            <td>:</td>
            <td><?= $data['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">Tempat, Tgl Lahir</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['tempat_lahir']); ?>, <?= formatTanggalIndo($data['tanggal_lahir']); ?></td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">Asal Sekolah</td>
            <td>:</td>
            <td><?= htmlspecialchars($data['asal_sekolah']); ?> (Lulus <?= $data['tahun_lulus']; ?>)</td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">Jalur Seleksi</td>
            <td>:</td>
            <td class="font-bold text-slate-800"><?= htmlspecialchars($data['nama_jalur']); ?></td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">Pilihan Prodi 1</td>
            <td>:</td>
            <td class="font-bold text-primary"><?= htmlspecialchars($data['prodi1_jenjang']); ?> - <?= htmlspecialchars($data['prodi1_nama']); ?></td>
          </tr>
          <tr>
            <td class="py-1 font-semibold text-slate-600">Pilihan Prodi 2</td>
            <td>:</td>
            <td><?= !empty($data['prodi2_nama']) ? htmlspecialchars($data['prodi2_jenjang']) . ' - ' . htmlspecialchars($data['prodi2_nama']) : '-'; ?></td>
          </tr>
        </table>
      </div>

    </div>

    <!-- Notes & Signature Box -->
    <div class="border-t border-slate-200 pt-4 text-[11px] text-slate-600 space-y-3">
      <p><strong>Ketentuan Peserta:</strong></p>
      <ol class="list-decimal list-inside space-y-1 text-[10px]">
        <li>Kartu tanda peserta ini wajib dicetak dan dibawa saat verifikasi fisik berkas atau pelaksanaan ujian/wawancara.</li>
        <li>Pantau pengumuman kelulusan dan jadwal daftar ulang secara berkala di portal resmi <code>pmb.muliarokan.ac.id</code>.</li>
        <li>Untuk informasi dan kendala teknis, hubungi Sekretariat PMB Mulia Rokan di Bagansiapiapi.</li>
      </ol>

      <div class="pt-6 flex justify-between items-end text-xs">
        <div>
          <div class="w-20 h-20 border border-slate-300 p-1 flex items-center justify-center text-[9px] text-center font-mono">
            QR CODE<br>VERIFIKASI
          </div>
          <span class="text-[9px] text-slate-400">Dicetak: <?= date('d/m/Y H:i'); ?> WIB</span>
        </div>
        <div class="text-center w-48">
          <p class="text-[11px]">Bagansiapiapi, <?= formatTanggalIndo(date('Y-m-d')); ?></p>
          <p class="text-[11px] font-semibold mt-1">Panitia Penerimaan PMB,</p>
          <div class="h-14"></div>
          <p class="font-bold text-xs underline text-slate-900">TIM SELEKSI PMB</p>
          <p class="text-[9px] text-slate-500">Politeknik Mulia Rokan</p>
        </div>
      </div>
    </div>

  </div>

</body>
</html>
