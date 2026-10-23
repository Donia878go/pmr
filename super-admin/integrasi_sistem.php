<?php
$page_title = "Konfigurasi Sistem, SEO & Integrasi API";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Simpan Pengaturan Sistem & Integrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_system_config') {
    // 1. Script & SEO
    $custom_head_scripts = trim($_POST['custom_head_scripts'] ?? '');
    $custom_body_scripts = trim($_POST['custom_body_scripts'] ?? '');
    $seo_meta_title = trim($_POST['seo_meta_title'] ?? '');
    $seo_meta_keywords = trim($_POST['seo_meta_keywords'] ?? '');
    $seo_meta_description = trim($_POST['seo_meta_description'] ?? '');

    // 2. SMTP Mail Server
    $smtp_host = trim($_POST['smtp_host'] ?? '');
    $smtp_port = (int)($_POST['smtp_port'] ?? 587);
    $smtp_user = trim($_POST['smtp_user'] ?? '');
    $smtp_pass = trim($_POST['smtp_pass'] ?? '');
    $smtp_encryption = trim($_POST['smtp_encryption'] ?? 'tls');

    // 3. Payment Gateway
    $payment_gateway_mode = in_array($_POST['payment_gateway_mode'] ?? '', ['sandbox', 'production']) ? $_POST['payment_gateway_mode'] : 'sandbox';
    $payment_server_key = trim($_POST['payment_server_key'] ?? '');
    $payment_client_key = trim($_POST['payment_client_key'] ?? '');

    // 4. Maintenance Mode Switch
    $is_maintenance = isset($_POST['is_maintenance']) ? 1 : 0;
    $maintenance_message = trim($_POST['maintenance_message'] ?? '');

    try {
        $stmt_up = $pdo->prepare("UPDATE site_settings SET 
            custom_head_scripts = ?,
            custom_body_scripts = ?,
            seo_meta_title = ?,
            seo_meta_keywords = ?,
            seo_meta_description = ?,
            smtp_host = ?,
            smtp_port = ?,
            smtp_user = ?,
            smtp_pass = ?,
            smtp_encryption = ?,
            payment_gateway_mode = ?,
            payment_server_key = ?,
            payment_client_key = ?,
            is_maintenance = ?,
            maintenance_message = ?,
            updated_at = NOW()
            WHERE id = 1");
        
        $stmt_up->execute([
            $custom_head_scripts,
            $custom_body_scripts,
            $seo_meta_title,
            $seo_meta_keywords,
            $seo_meta_description,
            $smtp_host,
            $smtp_port,
            $smtp_user,
            $smtp_pass,
            $smtp_encryption,
            $payment_gateway_mode,
            $payment_server_key,
            $payment_client_key,
            $is_maintenance,
            $maintenance_message
        ]);

        logActivity($pdo, $_SESSION['admin_id'], 'UPDATE_SYSTEM_SETTINGS', 'System Integration', 1, "Memperbarui konfigurasi sistem, SEO, SMTP, dan status maintenance ($is_maintenance)");
        $message = "Konfigurasi sistem, integrasi API, dan status pemeliharaan berhasil disimpan!";
        $site = getSiteSettings($pdo); // Refresh
    } catch (Exception $e) {
        $error = "Gagal memperbarui konfigurasi: " . $e->getMessage();
    }
}
?>

<div class="space-y-6">

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
      <span><?= htmlspecialchars($message); ?></span>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
      <i class="fa-solid fa-triangle-exclamation text-rose-600 text-base"></i>
      <span><?= htmlspecialchars($error); ?></span>
    </div>
  <?php endif; ?>

  <!-- Top Hero Banner -->
  <div class="bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-xl border border-teal-800/40 relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full text-[11px] font-bold uppercase tracking-wider mb-3">
        <i class="fa-solid fa-sliders"></i>
        <span>Backend Controller & Third-Party Connectors</span>
      </div>
      <h2 class="text-xl sm:text-2xl font-black text-white mb-2">Konfigurasi Sistem, SEO & Integrasi API</h2>
      <p class="text-xs sm:text-sm text-teal-100/90 leading-relaxed">
        Pusat kendali integrasi eksternal institusi: Script Injector (Google Analytics/Pixel/Chatbot), Dynamic SEO & Meta Data, Kredensial SMTP Notifikasi Email, Payment Gateway Keys, dan Saklar Darurat Maintenance Mode.
      </p>
    </div>
    <i class="fa-solid fa-microchip text-8xl text-teal-600/10 absolute -right-4 -bottom-4"></i>
  </div>

  <form action="integrasi_sistem.php" method="POST" class="space-y-8">
    <input type="hidden" name="action" value="save_system_config">

    <!-- 1. Emergency Maintenance Mode Switch -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg shrink-0">
            <i class="fa-solid fa-power-off"></i>
          </div>
          <div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Maintenance Mode Switch (Saklar Darurat)</h3>
            <p class="text-xs text-slate-500">Tutup akses publik frontend secara instan saat migrasi database. Super Admin tetap memiliki hak bypass.</p>
          </div>
        </div>

        <label class="relative inline-flex items-center cursor-pointer select-none">
          <input type="checkbox" name="is_maintenance" value="1" <?= !empty($site['is_maintenance']) ? 'checked' : ''; ?> class="sr-only peer">
          <div class="w-14 h-7 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-rose-600"></div>
          <span class="ml-3 text-xs font-bold text-slate-700 uppercase">
            <?= !empty($site['is_maintenance']) ? '<span class="text-rose-600 font-black">SEDANG AKTIF (UNDER MAINTENANCE)</span>' : '<span class="text-emerald-600">OFF (SITUS NORMAL)</span>'; ?>
          </span>
        </label>
      </div>

      <div>
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pesan Pemeliharaan untuk Pengunjung Publik</label>
        <textarea name="maintenance_message" rows="2" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none"><?= htmlspecialchars($site['maintenance_message'] ?? 'Website Kami Sedang Dalam Pemeliharaan Berkala untuk Peningkatan Layanan.'); ?></textarea>
        <p class="text-[11px] text-slate-400 mt-1">Pesan ini akan dimunculkan di halaman darurat <code>maintenance.php</code> lengkap dengan tombol kontak WhatsApp Helpdesk PMB.</p>
      </div>
    </div>

    <!-- 2. Dynamic SEO & Meta Data -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
      <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="w-10 h-10 rounded-2xl bg-teal-100 text-teal-800 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Dynamic SEO & Open Graph Meta Tags</h3>
          <p class="text-xs text-slate-500">Optimasi visibilitas portal PMB di mesin pencari (Google, Bing) dan tampilan thumbnail saat dibagikan ke WhatsApp & Facebook.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Situs Global (SEO Meta Title)</label>
          <input type="text" name="seo_meta_title" value="<?= htmlspecialchars($site['seo_meta_title'] ?? ''); ?>" placeholder="Contoh: Politeknik Mulia Rokan - PMB TA 2026/2027" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none font-semibold">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kata Kunci Pencarian (SEO Keywords)</label>
          <textarea name="seo_meta_keywords" rows="3" placeholder="pmb politeknik mulia rokan, vokasi sawit riau, kuliah d4 rokan hilir..." class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none"><?= htmlspecialchars($site['seo_meta_keywords'] ?? ''); ?></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Pisahkan kata kunci dengan tanda koma (,).</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Deskripsi Ringkasan (SEO Meta Description)</label>
          <textarea name="seo_meta_description" rows="3" placeholder="Penerimaan Mahasiswa Baru Politeknik Mulia Rokan TA 2026/2027..." class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none"><?= htmlspecialchars($site['seo_meta_description'] ?? ''); ?></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Disarankan antara 120-160 karakter agar optimal di hasil pencarian Google.</p>
        </div>
      </div>
    </div>

    <!-- 3. Script & Third-Party Injector -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
      <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
        <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-800 flex items-center justify-center text-lg shrink-0">
          <i class="fa-solid fa-code"></i>
        </div>
        <div>
          <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Script & Third-Party Injector (&lt;head&gt; / &lt;body&gt;)</h3>
          <p class="text-xs text-slate-500">Suntikkan kode pelacak (Google Tag Manager, Meta Pixel, Hotjar) atau widget chatbot langsung tanpa menyentuh file server.</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Suntikan &lt;head&gt; (Head Scripts)</label>
          <textarea name="custom_head_scripts" rows="6" placeholder="<!-- Google Analytics / Meta Pixel -->&#10;<script>...</script>" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none bg-slate-900 text-teal-300"><?= htmlspecialchars($site['custom_head_scripts'] ?? ''); ?></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Dieksekusi di dalam tag <code>&lt;head&gt;</code> sebelum halaman selesai dimuat.</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Suntikan &lt;body&gt; (Body / Footer Scripts)</label>
          <textarea name="custom_body_scripts" rows="6" placeholder="<!-- Chatbot Script / Widget Crisp / Tawk.to -->&#10;<script>...</script>" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 focus:outline-none bg-slate-900 text-teal-300"><?= htmlspecialchars($site['custom_body_scripts'] ?? ''); ?></textarea>
          <p class="text-[11px] text-slate-400 mt-1">Dieksekusi di akhir tag <code>&lt;body&gt;</code> (cocok untuk live chat & asynchronous widget).</p>
        </div>
      </div>
    </div>

    <!-- 4. Integrasi SMTP & Payment Gateway -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- SMTP Mail Server -->
      <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
          <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-sm shrink-0">
            <i class="fa-solid fa-envelope"></i>
          </div>
          <div>
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Integrasi SMTP Notifikasi Email</h3>
            <p class="text-[11px] text-slate-500">Pengiriman bukti pendaftaran & SKL otomatis.</p>
          </div>
        </div>

        <div class="space-y-3">
          <div class="grid grid-cols-3 gap-2">
            <div class="col-span-2">
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">SMTP Host</label>
              <input type="text" name="smtp_host" value="<?= htmlspecialchars($site['smtp_host'] ?? 'smtp.gmail.com'); ?>" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Port</label>
              <input type="number" name="smtp_port" value="<?= htmlspecialchars($site['smtp_port'] ?? '587'); ?>" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
            </div>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">SMTP Username / Email Pengirim</label>
            <input type="text" name="smtp_user" value="<?= htmlspecialchars($site['smtp_user'] ?? ''); ?>" placeholder="notifikasi@politeknikmuliarokan.ac.id" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          </div>

          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">SMTP Password</label>
              <input type="password" name="smtp_pass" value="<?= htmlspecialchars($site['smtp_pass'] ?? ''); ?>" placeholder="App Password" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Enkripsi</label>
              <select name="smtp_encryption" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
                <option value="tls" <?= ($site['smtp_encryption'] ?? '') === 'tls' ? 'selected' : ''; ?>>TLS (Rekomendasi Port 587)</option>
                <option value="ssl" <?= ($site['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL (Port 465)</option>
                <option value="none" <?= ($site['smtp_encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment Gateway (Midtrans / Xendit) -->
      <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
          <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-sm shrink-0">
            <i class="fa-solid fa-credit-card"></i>
          </div>
          <div>
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider">Payment Gateway (VA & QRIS PMB)</h3>
            <p class="text-[11px] text-slate-500">Integrasi pembayaran biaya registrasi online.</p>
          </div>
        </div>

        <div class="space-y-3">
          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Environment Mode</label>
            <select name="payment_gateway_mode" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white font-semibold">
              <option value="sandbox" <?= ($site['payment_gateway_mode'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>Sandbox (Mode Simulasi Pengujian)</option>
              <option value="production" <?= ($site['payment_gateway_mode'] ?? '') === 'production' ? 'selected' : ''; ?>>Production (Transaksi Nyata Langsung)</option>
            </select>
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Server Key (Secret Key)</label>
            <input type="password" name="payment_server_key" value="<?= htmlspecialchars($site['payment_server_key'] ?? ''); ?>" placeholder="SB-Mid-server-..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 font-mono">
          </div>

          <div>
            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Client Key (Public Key)</label>
            <input type="text" name="payment_client_key" value="<?= htmlspecialchars($site['payment_client_key'] ?? ''); ?>" placeholder="SB-Mid-client-..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 font-mono">
          </div>
        </div>
      </div>

    </div>

    <!-- Submit Button Floating Bar -->
    <div class="sticky bottom-6 z-20 bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl border border-slate-800 shadow-2xl flex items-center justify-between text-white">
      <div class="text-xs text-slate-300">
        <i class="fa-solid fa-circle-info text-amber-400 mr-1"></i>
        Perubahan konfigurasi sistem akan segera berlaku secara real-time.
      </div>
      <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-slate-950 font-black text-xs rounded-xl shadow-lg transition flex items-center gap-2">
        <i class="fa-solid fa-floppy-disk"></i>
        <span>Simpan Seluruh Pengaturan Sistem</span>
      </button>
    </div>

  </form>

</div>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
