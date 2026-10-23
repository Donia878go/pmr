<?php
$page_title = "Pengaturan Logo, Brand & Background Kampus";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kampus       = trim($_POST['nama_kampus'] ?? '');
    $sub_nama          = trim($_POST['sub_nama'] ?? '');
    $tagline           = trim($_POST['tagline'] ?? '');
    $color_primary     = trim($_POST['color_primary'] ?? '#1bb28f');
    $color_accent      = trim($_POST['color_accent'] ?? '#e9b35d');
    $running_text      = trim($_POST['running_text'] ?? '');
    $alamat            = trim($_POST['alamat'] ?? '');
    $email             = trim($_POST['email'] ?? '');
    $telepon           = trim($_POST['telepon'] ?? '');
    $whatsapp_helpdesk = trim($_POST['whatsapp_helpdesk'] ?? '');
    $jam_operasional   = trim($_POST['jam_operasional'] ?? '');
    $google_maps       = trim($_POST['google_maps_iframe'] ?? '');
    $hero_bg_opacity   = (int)($_POST['hero_bg_opacity'] ?? 85);

    $logo_url         = $site['logo_url'] ?? 'assets/images/logo-politeknik.png';
    $logo_yayasan_url = $site['logo_yayasan_url'] ?? 'assets/images/logo-yayasan.png';
    $hero_bg_url      = $site['hero_bg_image'] ?? 'assets/images/gedung-kampus.svg';

    // Upload Logo Politeknik Baru jika ada
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['logo_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        if (in_array($ext, $allowed)) {
            $new_logo_name = 'logo_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/' . $new_logo_name;
            if (move_uploaded_file($_FILES['logo_file']['tmp_name'], $dest)) {
                $logo_url = 'assets/images/' . $new_logo_name;
            }
        } else {
            $error = 'Format file logo kampus tidak didukung. Gunakan JPG, PNG, atau SVG.';
        }
    }

    // Upload Logo Yayasan Baru jika ada
    if (isset($_FILES['logo_yayasan_file']) && $_FILES['logo_yayasan_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['logo_yayasan_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
        if (in_array($ext, $allowed)) {
            $new_yayasan_name = 'logo_yayasan_' . time() . '.' . $ext;
            $dest = __DIR__ . '/../assets/images/' . $new_yayasan_name;
            if (move_uploaded_file($_FILES['logo_yayasan_file']['tmp_name'], $dest)) {
                $logo_yayasan_url = 'assets/images/' . $new_yayasan_name;
            }
        } else {
            $error = 'Format file logo yayasan tidak didukung. Gunakan JPG, PNG, atau SVG.';
        }
    }

    // Upload Foto Background Kampus Baru jika ada
    if (isset($_FILES['hero_bg_file']) && $_FILES['hero_bg_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['hero_bg_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
        if (in_array($ext, $allowed)) {
            $new_bg_name = 'bg_kampus_' . time() . '.' . $ext;
            $dest_bg = __DIR__ . '/../assets/images/' . $new_bg_name;
            if (move_uploaded_file($_FILES['hero_bg_file']['tmp_name'], $dest_bg)) {
                $hero_bg_url = 'assets/images/' . $new_bg_name;
            }
        } else {
            $error = 'Format file background tidak didukung. Gunakan JPG, PNG, atau WebP.';
        }
    }

    if (empty($error)) {
        $stmt_up = $pdo->prepare("UPDATE site_settings SET 
            nama_kampus = ?, sub_nama = ?, tagline = ?, logo_url = ?, logo_yayasan_url = ?,
            hero_bg_image = ?, hero_bg_opacity = ?,
            color_primary = ?, color_accent = ?, running_text = ?,
            alamat = ?, email = ?, telepon = ?, whatsapp_helpdesk = ?,
            jam_operasional = ?, google_maps_iframe = ?
            WHERE id = 1");

        $res = $stmt_up->execute([
            $nama_kampus, $sub_nama, $tagline, $logo_url, $logo_yayasan_url,
            $hero_bg_url, $hero_bg_opacity,
            $color_primary, $color_accent, $running_text,
            $alamat, $email, $telepon, $whatsapp_helpdesk,
            $jam_operasional, $google_maps
        ]);

        if ($res) {
            $message = "Pengaturan identitas, logo Politeknik & Yayasan, serta tema berhasil disimpan!";
            $site = getSiteSettings($pdo);
        } else {
            $error = "Gagal memperbarui pengaturan website.";
        }
    }
}
?>

<div class="max-w-5xl mx-auto space-y-6">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Identitas Resmi, Logo & Background Kampus</h2>
      <p class="text-xs text-slate-500">Ubah logo lambang kampus, foto background gedung Politeknik Mulia Rokan, warna tema, teks berjalan, dan kontak.</p>
    </div>
    <a href="../index.php" target="_blank" class="px-4 py-2 bg-teal-800 text-white rounded-xl text-xs font-bold shadow hover:bg-teal-900 transition flex items-center gap-1.5">
      <span>🌐 Cek Tampilan Web Depan</span>
    </a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= $message; ?></span>
      <a href="../index.php" target="_blank" class="underline font-bold text-emerald-900">Lihat Hasil Langsung &rarr;</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= $error; ?>
    </div>
  <?php endif; ?>

  <form action="pengaturan_web.php" method="POST" enctype="multipart/form-data" class="space-y-6">

    <!-- Card 1: Logo & Warna Brand (Politeknik & Yayasan dengan Background Putih) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-[#1bb28f] uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>🎨</span>
        <span>1. Lambang Logo Politeknik & Yayasan (Latar Putih) & Skema Warna Brand</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        
        <!-- Box 1: Logo Politeknik Mulia Rokan -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 text-center">
          <label class="block text-xs font-bold text-slate-800">🏛️ Logo Politeknik Mulia Rokan (Bulat & Background Putih)</label>
          <div class="w-28 h-28 rounded-full bg-white p-3 border-2 border-slate-200 text-center shadow-md mx-auto flex items-center justify-center overflow-hidden">
            <img src="../<?= htmlspecialchars($site['logo_url'] ?? 'assets/images/logo-politeknik.png'); ?>" alt="Logo Politeknik" class="w-full h-full object-contain">
          </div>
          <span class="block text-[10px] text-slate-400 truncate font-mono"><?= htmlspecialchars($site['logo_url'] ?? ''); ?></span>
          <div class="text-left">
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Unggah Ganti Logo Politeknik:</label>
            <input type="file" name="logo_file" accept=".jpg,.jpeg,.png,.svg,.webp" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1bb28f]/10 file:text-[#1bb28f] hover:file:bg-[#1bb28f]/20 cursor-pointer">
          </div>
        </div>

        <!-- Box 2: Logo Yayasan Rokan Jaya Bermarwah -->
        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-200 space-y-3 text-center">
          <label class="block text-xs font-bold text-slate-800">🛡️ Logo Yayasan Rokan Jaya Bermarwah (Bulat & Background Putih)</label>
          <div class="w-28 h-28 rounded-full bg-white p-3 border-2 border-slate-200 text-center shadow-md mx-auto flex items-center justify-center overflow-hidden">
            <img src="../<?= htmlspecialchars($site['logo_yayasan_url'] ?? 'assets/images/logo-yayasan.png'); ?>" alt="Logo Yayasan" class="w-full h-full object-contain">
          </div>
          <span class="block text-[10px] text-slate-400 truncate font-mono"><?= htmlspecialchars($site['logo_yayasan_url'] ?? ''); ?></span>
          <div class="text-left">
            <label class="block text-[11px] font-bold text-slate-700 mb-1">Unggah Ganti Logo Yayasan:</label>
            <input type="file" name="logo_yayasan_file" accept=".jpg,.jpeg,.png,.svg,.webp" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#1bb28f]/10 file:text-[#1bb28f] hover:file:bg-[#1bb28f]/20 cursor-pointer">
          </div>
        </div>

      </div>

      <!-- Skema Warna Brand -->
      <div class="pt-4 border-t border-slate-100">
        <label class="block text-xs font-bold text-slate-800 mb-3">🎨 Skema Warna Utama & Aksen Brand</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Warna Utama (Primary: #1bb28f)</label>
            <div class="flex items-center gap-3">
              <input type="color" name="color_primary" value="<?= htmlspecialchars($site['color_primary'] ?? '#1bb28f'); ?>" class="w-12 h-10 p-1 rounded-xl border border-slate-300 cursor-pointer">
              <input type="text" value="<?= htmlspecialchars($site['color_primary'] ?? '#1bb28f'); ?>" readonly class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl">
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">Warna Aksen (Secondary / Emas Yayasan: #e9b35d)</label>
            <div class="flex items-center gap-3">
              <input type="color" name="color_accent" value="<?= htmlspecialchars($site['color_accent'] ?? '#e9b35d'); ?>" class="w-12 h-10 p-1 rounded-xl border border-slate-300 cursor-pointer">
              <input type="text" value="<?= htmlspecialchars($site['color_accent'] ?? '#e9b35d'); ?>" readonly class="w-full px-3 py-2 text-xs font-mono bg-slate-50 border border-slate-200 rounded-xl">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 2: Foto Latar Belakang Kampus (Customizable Background) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>🏛️</span>
        <span>2. Foto Latar Belakang Gedung Politeknik Mulia Rokan (Custom Hero Background)</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
        <!-- Preview Background Saat Ini -->
        <div class="md:col-span-5 text-center">
          <label class="block text-xs font-bold text-slate-700 mb-2">Pratinjau Background Saat Ini</label>
          <div class="relative rounded-2xl overflow-hidden border-2 border-slate-300 shadow-md h-36 w-full">
            <img src="../<?= htmlspecialchars($site['hero_bg_image'] ?? 'assets/images/gedung-kampus.svg'); ?>" alt="Background Kampus" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-slate-950/70 flex items-center justify-center text-xs font-bold text-amber-300">
              Overlay Teal Aktif
            </div>
          </div>
          <span class="block text-[10px] text-slate-400 mt-1 truncate font-mono"><?= htmlspecialchars($site['hero_bg_image'] ?? ''); ?></span>
        </div>

        <div class="md:col-span-7 space-y-4">
          <div>
            <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Foto Gedung / Kampus Politeknik Mulia Rokan Baru</label>
            <input type="file" name="hero_bg_file" accept=".jpg,.jpeg,.png,.webp,.svg" class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 cursor-pointer">
            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, atau WebP (Disarankan resolusi 1920x1080 atau minimal 1280x720 untuk hasil jernih).</p>
          </div>

          <div>
            <div class="flex justify-between items-center mb-1">
              <label class="text-xs font-bold text-slate-700">Kepekatan Warna Overlay Gelap / Teal (Darkness Intensity)</label>
              <span class="text-xs font-black text-teal-800" id="opacity_display"><?= htmlspecialchars($site['hero_bg_opacity'] ?? 85); ?>%</span>
            </div>
            <input type="range" name="hero_bg_opacity" min="30" max="95" value="<?= htmlspecialchars($site['hero_bg_opacity'] ?? 85); ?>" oninput="document.getElementById('opacity_display').innerText = this.value + '%'" class="w-full accent-teal-700 cursor-pointer">
            <p class="text-[11px] text-slate-400 mt-1">Geser ke kanan untuk overlay yang lebih pekat agar teks terbaca sangat jelas, atau ke kiri untuk gambar lebih terang.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Card 3: Identitas Nama Kampus & Teks Header -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>🏫</span>
        <span>3. Nama Perguruan Tinggi & Teks Berjalan (Ticker)</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nama Resmi Perguruan Tinggi *</label>
          <input type="text" name="nama_kampus" value="<?= htmlspecialchars($site['nama_kampus'] ?? ''); ?>" required class="w-full px-4 py-2.5 text-xs font-bold border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Tagline PMB / Tahun Akademik</label>
          <input type="text" name="tagline" value="<?= htmlspecialchars($site['tagline'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Sub-Nama / Identitas Vokasi</label>
          <input type="text" name="sub_nama" value="<?= htmlspecialchars($site['sub_nama'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Teks Berjalan Pengumuman (Running Text / Announcement Ticker)</label>
          <textarea name="running_text" rows="2" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700" placeholder="Contoh: 📢 PENERIMAAN MAHASISWA BARU TA 2026/2027 TELAH DIBUKA! Beasiswa 100% Bebas SPP..."><?= htmlspecialchars($site['running_text'] ?? ''); ?></textarea>
        </div>
      </div>
    </div>

    <!-- Card 4: Kontak Sekretariat & Google Maps -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
      <h3 class="text-sm font-black text-teal-800 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
        <span>📍</span>
        <span>4. Kontak Sekretariat & Peta Lokasi</span>
      </h3>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Kampus Utama</label>
          <textarea name="alamat" rows="2" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['alamat'] ?? ''); ?></textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Helpdesk PMB *</label>
          <input type="text" name="whatsapp_helpdesk" value="<?= htmlspecialchars($site['whatsapp_helpdesk'] ?? ''); ?>" required class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email Resmi</label>
          <input type="email" name="email" value="<?= htmlspecialchars($site['email'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Telepon Kantor / Sekretariat</label>
          <input type="text" name="telepon" value="<?= htmlspecialchars($site['telepon'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Jam Pelayanan & Operasional</label>
          <input type="text" name="jam_operasional" value="<?= htmlspecialchars($site['jam_operasional'] ?? ''); ?>" class="w-full px-4 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-slate-700 mb-1">Kode Embed Google Maps (Iframe)</label>
          <textarea name="google_maps_iframe" rows="3" class="w-full px-4 py-2 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"><?= htmlspecialchars($site['google_maps_iframe'] ?? ''); ?></textarea>
        </div>
      </div>
    </div>

    <!-- Submit Button -->
    <div class="flex items-center justify-end gap-4 pt-2">
      <a href="index.php" class="px-5 py-3 text-xs font-bold text-slate-600 hover:text-slate-900">
        Batal
      </a>
      <button type="submit" class="px-8 py-3.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
        Simpan Seluruh Pengaturan Web Depan &rarr;
      </button>
    </div>

  </form>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
