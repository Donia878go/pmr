<?php
$page_title = "Cek Status Pendaftaran PMB 2026";
require_once __DIR__ . '/config/database.php';

$search = trim($_GET['no_pendaftaran'] ?? $_POST['keyword'] ?? '');
$pendaftar = null;
$message = '';

if (!empty($search)) {
    $stmt = $pdo->prepare("SELECT p.*, j.nama_jalur, p1.nama_prodi as prodi1_nama, p1.jenjang as prodi1_jenjang, p2.nama_prodi as prodi2_nama, p2.jenjang as prodi2_jenjang 
                           FROM pendaftar p 
                           LEFT JOIN jalur_pendaftaran j ON p.jalur_id = j.id 
                           LEFT JOIN program_studi p1 ON p.prodi_pilihan_1 = p1.id 
                           LEFT JOIN program_studi p2 ON p.prodi_pilihan_2 = p2.id 
                           WHERE p.no_pendaftaran = ? OR p.nik = ? OR p.no_whatsapp = ?");
    $stmt->execute([$search, $search, $search]);
    $pendaftar = $stmt->fetch();

    if (!$pendaftar) {
        $message = "Data pendaftar dengan kata kunci '{$search}' tidak ditemukan. Pastikan Nomor Pendaftaran atau NIK yang Anda masukkan benar.";
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="theme-bg-canvas py-12 flex-1">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="text-center max-w-2xl mx-auto space-y-3 mb-8">
      <span class="inline-flex items-center gap-1.5 text-xs font-bold text-white tracking-wider uppercase px-4 py-1.5 rounded-full shadow-sm" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
        <span class="w-2 h-2 rounded-full bg-[#e9b35d]"></span>
        <span>Tracking Pendaftaran PMB</span>
      </span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-white drop-shadow-md">Cek Status Penerimaan Mahasiswa Baru</h1>
      <p class="text-white/90 text-sm">Masukkan Nomor Pendaftaran (contoh: <code>PMB-2026-0001</code>), NIK KTP, atau Nomor WhatsApp yang didaftarkan.</p>
    </div>

    <!-- Search Box -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm mb-8">
      <form action="cek_status.php" method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
          <input type="text" name="no_pendaftaran" value="<?= htmlspecialchars($search); ?>" required placeholder="Masukkan Nomor Pendaftaran / NIK / No. WA..." class="w-full pl-4 pr-10 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
          <span class="absolute right-3 top-3.5 text-slate-400">🔍</span>
        </div>
        <button type="submit" class="brand-gear-btn px-6 py-3 text-sm font-bold rounded-xl transition shadow">
          Periksa Status
        </button>
      </form>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="p-4 mb-6 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start gap-3">
      <span class="text-xl">🎉</span>
      <div>
        <strong>Pendaftaran Berhasil Dikirim!</strong><br>
        Nomor pendaftaran resmi Anda adalah <strong><?= htmlspecialchars($search); ?></strong>. Harap simpan nomor pendaftaran ini untuk mengecek hasil seleksi berkas dan mencetak kartu pendaftaran.
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($message)): ?>
    <div class="p-4 mb-6 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
      <?= htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>

    <!-- Result Card -->
    <?php if ($pendaftar): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
      
      <!-- Top Status Banner -->
      <?php
      $badge_color = 'bg-slate-100 text-slate-800 border-slate-200';
      $status_text = 'Menunggu Verifikasi';
      if ($pendaftar['status_seleksi'] === 'terverifikasi') {
          $badge_color = 'bg-blue-50 text-blue-700 border-blue-200';
          $status_text = 'Berkas Terverifikasi';
      } elseif ($pendaftar['status_seleksi'] === 'lulus') {
          $badge_color = 'bg-emerald-50 text-emerald-700 border-emerald-200';
          $status_text = 'Selamat! Anda Dinyatakan LULUS Seleksi';
      } elseif ($pendaftar['status_seleksi'] === 'tidak_lulus') {
          $badge_color = 'bg-rose-50 text-rose-700 border-rose-200';
          $status_text = 'Belum Lulus Seleksi';
      }
      ?>
      
      <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
        <div>
          <span class="text-xs font-semibold text-slate-500">Nomor Registrasi:</span>
          <h2 class="text-xl font-black text-primary"><?= htmlspecialchars($pendaftar['no_pendaftaran']); ?></h2>
        </div>
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-xs font-bold <?= $badge_color; ?>">
          <span>●</span>
          <span><?= $status_text; ?></span>
        </div>
      </div>

      <!-- Applicant Details -->
      <div class="p-6 sm:p-8 space-y-6">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
          <div>
            <span class="text-slate-500 block">Nama Lengkap</span>
            <span class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($pendaftar['nama_lengkap']); ?></span>
          </div>
          <div>
            <span class="text-slate-500 block">NIK</span>
            <span class="font-bold text-slate-800"><?= htmlspecialchars($pendaftar['nik']); ?></span>
          </div>
          <div>
            <span class="text-slate-500 block">Jalur Pendaftaran</span>
            <span class="font-bold text-primary"><?= htmlspecialchars($pendaftar['nama_jalur']); ?></span>
          </div>
          <div>
            <span class="text-slate-500 block">Asal Sekolah</span>
            <span class="font-bold text-slate-800"><?= htmlspecialchars($pendaftar['asal_sekolah']); ?> (<?= $pendaftar['tahun_lulus']; ?>)</span>
          </div>
          <div>
            <span class="text-slate-500 block">Program Studi Pilihan 1</span>
            <span class="font-bold text-slate-900"><?= htmlspecialchars($pendaftar['prodi1_jenjang']); ?> - <?= htmlspecialchars($pendaftar['prodi1_nama']); ?></span>
          </div>
          <div>
            <span class="text-slate-500 block">Program Studi Pilihan 2</span>
            <span class="font-semibold text-slate-700"><?= !empty($pendaftar['prodi2_nama']) ? htmlspecialchars($pendaftar['prodi2_jenjang']) . ' - ' . htmlspecialchars($pendaftar['prodi2_nama']) : '-'; ?></span>
          </div>
        </div>

        <!-- Catatan Panitia -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
          <span class="text-xs font-bold text-slate-700 block mb-1">📢 Catatan Panitia PMB:</span>
          <p class="text-xs text-slate-600 leading-relaxed">
            <?= !empty($pendaftar['catatan_panitia']) ? nl2br(htmlspecialchars($pendaftar['catatan_panitia'])) : 'Belum ada catatan panitia.'; ?>
          </p>
        </div>

        <!-- Action Print -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100">
          <span class="text-xs text-slate-500">Tanggal Mendaftar: <?= formatTanggalIndo(substr($pendaftar['created_at'], 0, 10)); ?></span>
          <div class="flex flex-wrap gap-2 w-full sm:w-auto">
            <a href="cetak_kartu.php?no=<?= urlencode($pendaftar['no_pendaftaran']); ?>" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition shadow flex items-center justify-center gap-2">
              <span>🖨️ Cetak Kartu Peserta</span>
            </a>
            <?php if ($pendaftar['status_seleksi'] === 'lulus'): ?>
              <a href="cetak_skl.php?no=<?= urlencode($pendaftar['no_pendaftaran']); ?>" target="_blank" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow flex items-center justify-center gap-2">
                <span>📜 Cetak Surat Kelulusan (SKL)</span>
              </a>
            <?php endif; ?>
          </div>
        </div>

      </div>

    </div>
    <?php endif; ?>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
