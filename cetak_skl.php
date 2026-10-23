<?php
require_once __DIR__ . '/config/database.php';

$no = trim($_GET['no'] ?? '');

if (empty($no)) {
    die("Nomor pendaftaran tidak valid.");
}

$stmt = $pdo->prepare("SELECT p.*, j.nama_jalur, p1.nama_prodi as prodi1_nama, p1.jenjang as prodi1_jenjang, p2.nama_prodi as prodi2_nama 
                       FROM pendaftar p 
                       LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
                       LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
                       LEFT JOIN program_studi p2 ON p.prodi_pilihan_2 = p2.id 
                       WHERE p.no_pendaftaran = ?");
$stmt->execute([$no]);
$data = $stmt->fetch();

if (!$data) {
    die("Data pendaftar tidak ditemukan.");
}

$site = getSiteSettings($pdo);
$logo_img = !empty($site['logo_url']) ? $site['logo_url'] : 'assets/images/logo-resmi.jpg';
$kampus_name = !empty($site['nama_kampus']) ? $site['nama_kampus'] : 'POLITEKNIK MULIA ROKAN';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Surat Keterangan Lulus Seleksi PMB - <?= htmlspecialchars($data['no_pendaftaran']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="bg-slate-100 py-8 px-4 font-serif text-slate-900 leading-relaxed">

  <!-- Print Button Bar -->
  <div class="max-w-3xl mx-auto mb-4 no-print flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200 font-sans">
    <a href="cek_status.php?no_pendaftaran=<?= urlencode($data['no_pendaftaran']); ?>" class="text-xs font-bold text-slate-600 hover:text-teal-800">
      &larr; Kembali
    </a>
    <button onclick="window.print()" class="px-5 py-2 bg-teal-800 hover:bg-teal-900 text-white text-xs font-bold rounded-lg shadow transition">
      🖨️ Cetak Surat Keterangan Lulus (PDF)
    </button>
  </div>

  <div class="max-w-3xl mx-auto bg-white border-2 border-slate-800 p-8 sm:p-12 shadow-lg print-card font-serif text-xs sm:text-sm">
    
    <!-- Kop Surat Resmi -->
    <div class="flex items-center gap-5 border-b-4 border-double border-slate-900 pb-5 mb-8 text-center font-sans">
      <img src="<?= htmlspecialchars($logo_img); ?>" alt="Logo" class="h-20 w-auto max-w-[85px] object-contain">
      <div class="flex-1">
        <h3 class="text-xs font-bold tracking-widest text-slate-700 uppercase">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</h3>
        <h1 class="text-xl sm:text-2xl font-black text-slate-950 uppercase tracking-tight"><?= htmlspecialchars($kampus_name); ?></h1>
        <p class="text-[11px] text-slate-600 mt-0.5"><?= htmlspecialchars($site['sub_nama'] ?? 'Pendidikan Tinggi Vokasi Sarjana Terapan Terakreditasi'); ?></p>
        <p class="text-[10px] text-slate-500 font-mono mt-0.5"><?= htmlspecialchars($site['alamat'] ?? 'Bagansiapiapi, Rokan Hilir, Riau'); ?> | Telp: <?= htmlspecialchars($site['telepon'] ?? '-'); ?></p>
      </div>
    </div>

    <!-- Judul Surat -->
    <div class="text-center space-y-1 mb-8">
      <h2 class="text-base sm:text-lg font-black uppercase tracking-wider underline">SURAT KEPUTUSAN HASIL SELEKSI PMB</h2>
      <p class="text-xs font-sans text-slate-600">Nomor: <?= 'SK/' . date('Y') . '/PMB/' . str_pad($data['id'], 4, '0', STR_PAD_LEFT); ?></p>
    </div>

    <p class="mb-4">
      Berdasarkan hasil verifikasi berkas portofolio dan seleksi administrasi panitia Penerimaan Mahasiswa Baru Tahun Akademik <?= date('Y') . '/' . (date('Y') + 1); ?>, Direktur Politeknik Mulia Rokan menetapkan bahwa:
    </p>

    <!-- Tabel Biodata -->
    <div class="bg-slate-50 p-5 rounded-xl border border-slate-300 font-sans text-xs space-y-2 mb-6">
      <div class="grid grid-cols-12 gap-2">
        <span class="col-span-4 font-bold text-slate-600">Nomor Pendaftaran</span>
        <span class="col-span-8 font-black text-teal-800">: <?= htmlspecialchars($data['no_pendaftaran']); ?></span>
      </div>
      <div class="grid grid-cols-12 gap-2">
        <span class="col-span-4 font-bold text-slate-600">Nama Lengkap</span>
        <span class="col-span-8 font-bold text-slate-900">: <?= htmlspecialchars($data['nama_lengkap']); ?></span>
      </div>
      <div class="grid grid-cols-12 gap-2">
        <span class="col-span-4 font-bold text-slate-600">Nomor Induk Kependudukan</span>
        <span class="col-span-8 text-slate-800">: <?= htmlspecialchars($data['nik']); ?></span>
      </div>
      <div class="grid grid-cols-12 gap-2">
        <span class="col-span-4 font-bold text-slate-600">Asal Sekolah</span>
        <span class="col-span-8 text-slate-800">: <?= htmlspecialchars($data['asal_sekolah']); ?> (Tahun Lulus: <?= $data['tahun_lulus']; ?>)</span>
      </div>
      <div class="grid grid-cols-12 gap-2">
        <span class="col-span-4 font-bold text-slate-600">Jalur Penerimaan</span>
        <span class="col-span-8 font-bold text-slate-900">: <?= htmlspecialchars($data['nama_jalur']); ?></span>
      </div>
      <div class="grid grid-cols-12 gap-2 pt-2 border-t border-slate-200">
        <span class="col-span-4 font-bold text-slate-600">Diterima Pada Program Studi</span>
        <span class="col-span-8 font-black text-sm text-teal-900">: <?= htmlspecialchars($data['prodi1_jenjang']); ?> - <?= htmlspecialchars($data['prodi1_nama']); ?></span>
      </div>
    </div>

    <!-- Status Keputusan Box -->
    <div class="p-4 rounded-xl text-center mb-6 font-sans border-2 <?= $data['status_seleksi'] === 'lulus' ? 'bg-emerald-50 border-emerald-500 text-emerald-950' : 'bg-amber-50 border-amber-500 text-amber-950'; ?>">
      <span class="text-xs font-bold uppercase tracking-widest block text-slate-500">Hasil Keputusan:</span>
      <span class="text-lg sm:text-xl font-black uppercase tracking-wider block mt-1">
        <?= $data['status_seleksi'] === 'lulus' ? 'DIPERIKSA & DINYATAKAN LULUS SELEKSI' : 'STATUS: ' . strtoupper($data['status_seleksi']); ?>
      </span>
      <?php if (!empty($data['catatan_panitia'])): ?>
        <p class="text-xs mt-2 italic font-serif">"<?= htmlspecialchars($data['catatan_panitia']); ?>"</p>
      <?php endif; ?>
    </div>

    <p class="mb-6">
      Calon mahasiswa yang dinyatakan lulus diwajibkan melakukan registrasi ulang di Sekretariat PMB Kampus Politeknik Mulia Rokan atau menghubungi Helpdesk WhatsApp Resmi dengan membawa salinan asli dokumen persyaratan.
    </p>

    <!-- Tanda Tangan Resmi -->
    <div class="pt-8 flex justify-end font-sans">
      <div class="text-center w-64 space-y-1">
        <p class="text-xs text-slate-600">Bagansiapiapi, <?= formatTanggalIndo(date('Y-m-d')); ?></p>
        <p class="text-xs font-bold text-slate-800">Direktur <?= htmlspecialchars($kampus_name); ?></p>
        <div class="h-20 flex items-center justify-center">
          <span class="text-xs text-slate-300 italic">[ Tanda Tangan & Cap Digital ]</span>
        </div>
        <p class="text-xs font-black text-slate-900 underline"><?= htmlspecialchars($site['sambutan_direktur_nama'] ?? 'Ir. H. Syamsul Bahri, M.T.'); ?></p>
        <p class="text-[10px] text-slate-500"><?= htmlspecialchars($site['sambutan_direktur_jabatan'] ?? 'Pimpinan Kampus'); ?></p>
      </div>
    </div>

  </div>

</body>
</html>
