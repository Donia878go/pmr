<?php
require_once __DIR__ . '/config/database.php';
$site = getSiteSettings($pdo);

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: index.php');
    exit;
}

$stmt_form = $pdo->prepare("SELECT * FROM dynamic_forms WHERE slug = ? AND is_active = 1");
$stmt_form->execute([$slug]);
$form = $stmt_form->fetch();

if (!$form) {
    die("<div style='font-family: sans-serif; padding: 40px; text-align: center;'><h2>Formulir Tidak Ditemukan atau Dinonaktifkan</h2><p><a href='index.php'>Kembali ke Beranda</a></p></div>");
}

// Ambil field form
$stmt_fields = $pdo->prepare("SELECT * FROM dynamic_form_fields WHERE form_id = ? ORDER BY urutan ASC");
$stmt_fields->execute([$form['id']]);
$fields = $stmt_fields->fetchAll();

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submitted_data = [];
    $uploaded_file = null;

    // Handle File Upload jika ada
    if (!empty($_FILES)) {
        foreach ($_FILES as $input_name => $file_info) {
            if ($file_info['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file_info['name'], PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'];
                if (in_array($ext, $allowed)) {
                    $new_name = 'doc_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $dest_dir = __DIR__ . '/uploads/form_files/';
                    if (!is_dir($dest_dir)) {
                        mkdir($dest_dir, 0777, true);
                    }
                    if (move_uploaded_file($file_info['tmp_name'], $dest_dir . $new_name)) {
                        $uploaded_file = 'uploads/form_files/' . $new_name;
                        $submitted_data[$input_name] = $uploaded_file;
                    }
                }
            }
        }
    }

    // Ambil data text / inputs
    foreach ($fields as $f) {
        $name = $f['field_name'];
        if ($f['tipe'] === 'file') {
            // Sudah dihandle di atas
            if ($f['is_required'] && empty($uploaded_file)) {
                $error_msg = "Field " . htmlspecialchars($f['label']) . " wajib diunggah.";
                break;
            }
        } elseif ($f['tipe'] === 'checkbox') {
            $vals = $_POST[$name] ?? [];
            if ($f['is_required'] && empty($vals)) {
                $error_msg = "Field " . htmlspecialchars($f['label']) . " wajib dipilih.";
                break;
            }
            $submitted_data[$f['label']] = is_array($vals) ? implode(', ', $vals) : $vals;
        } else {
            $val = trim($_POST[$name] ?? '');
            if ($f['is_required'] && $val === '') {
                $error_msg = "Field " . htmlspecialchars($f['label']) . " wajib diisi.";
                break;
            }
            $submitted_data[$f['label']] = $val;
        }
    }

    if (empty($error_msg)) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $data_json = json_encode($submitted_data, JSON_UNESCAPED_UNICODE);

        $stmt_ins = $pdo->prepare("INSERT INTO dynamic_form_submissions (form_id, data_json, file_attachment, ip_address) VALUES (?, ?, ?, ?)");
        if ($stmt_ins->execute([$form['id'], $data_json, $uploaded_file, $ip])) {
            $success_msg = !empty($form['pesan_sukses']) ? $form['pesan_sukses'] : 'Terima kasih, data formulir berhasil dikirim!';
        } else {
            $error_msg = "Terjadi kendala saat menyimpan formulir. Silakan coba beberapa saat lagi.";
        }
    }
}

$page_title = $form['judul_form'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="theme-bg-canvas min-h-screen py-12">
  <div class="max-w-3xl mx-auto px-4 sm:px-6">

    <!-- Card Formulir -->
    <div class="bg-white rounded-3xl shadow-2xl border border-white/50 overflow-hidden">
      
      <!-- Header Banner with Gear Gradient #1bb28f & #1b969d -->
      <div class="p-8 text-white relative shadow-md" style="background: linear-gradient(135deg, #1bb28f 0%, #1b969d 100%);">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#e9b35d] text-slate-950 font-black text-[10px] uppercase tracking-wider rounded-full mb-2 shadow-xs">
          <span class="w-1.5 h-1.5 rounded-full bg-slate-950"></span>
          <span>Layanan Interaktif Kampus</span>
        </span>
        <h1 class="text-2xl sm:text-3xl font-black leading-tight drop-shadow-xs"><?= htmlspecialchars($form['judul_form']); ?></h1>
        <?php if (!empty($form['deskripsi'])): ?>
          <p class="text-xs sm:text-sm text-teal-50 mt-2 leading-relaxed">
            <?= nl2br(htmlspecialchars($form['deskripsi'])); ?>
          </p>
        <?php endif; ?>
      </div>

      <div class="p-8">
        
        <?php if (!empty($success_msg)): ?>
          <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-4">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 mx-auto flex items-center justify-center text-3xl font-bold">
              ✓
            </div>
            <h3 class="text-lg font-black text-emerald-900">Formulir Berhasil Dikirim!</h3>
            <p class="text-xs sm:text-sm text-emerald-800 leading-relaxed max-w-md mx-auto">
              <?= nl2br(htmlspecialchars($success_msg)); ?>
            </p>
            <div class="pt-2">
              <a href="index.php" class="inline-block px-6 py-2.5 bg-teal-800 text-white font-bold text-xs rounded-xl shadow hover:bg-teal-900 transition">
                &larr; Kembali ke Beranda
              </a>
            </div>
          </div>
        <?php else: ?>

          <?php if (!empty($error_msg)): ?>
            <div class="p-4 mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
              <span>⚠️</span>
              <span><?= htmlspecialchars($error_msg); ?></span>
            </div>
          <?php endif; ?>

          <form action="form.php?slug=<?= urlencode($slug); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            
            <?php foreach ($fields as $field): 
              $fname = htmlspecialchars($field['field_name']);
              $flabel = htmlspecialchars($field['label']);
              $freq = $field['is_required'] ? true : false;
              $ftype = $field['tipe'];
              $fplaceholder = htmlspecialchars($field['placeholder'] ?? '');
              $opts = !empty($field['pilihan']) ? array_map('trim', explode(',', $field['pilihan'])) : [];
            ?>

              <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-800">
                  <?= $flabel; ?>
                  <?php if ($freq): ?>
                    <span class="text-rose-500">*</span>
                  <?php else: ?>
                    <span class="text-slate-400 font-normal">(Opsional)</span>
                  <?php endif; ?>
                </label>

                <?php if ($ftype === 'text'): ?>
                  <input type="text" name="<?= $fname; ?>" placeholder="<?= $fplaceholder; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition">

                <?php elseif ($ftype === 'number'): ?>
                  <input type="number" name="<?= $fname; ?>" placeholder="<?= $fplaceholder; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition">

                <?php elseif ($ftype === 'email'): ?>
                  <input type="email" name="<?= $fname; ?>" placeholder="<?= $fplaceholder ?: 'contoh@email.com'; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition">

                <?php elseif ($ftype === 'phone'): ?>
                  <input type="tel" name="<?= $fname; ?>" placeholder="<?= $fplaceholder ?: '0812xxxxxxxx'; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition">

                <?php elseif ($ftype === 'date'): ?>
                  <input type="date" name="<?= $fname; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition">

                <?php elseif ($ftype === 'textarea'): ?>
                  <textarea name="<?= $fname; ?>" rows="4" placeholder="<?= $fplaceholder; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition"></textarea>

                <?php elseif ($ftype === 'select'): ?>
                  <select name="<?= $fname; ?>" <?= $freq ? 'required' : ''; ?> class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-600 focus:border-teal-600 outline-none transition bg-white">
                    <option value="">-- Pilih Salah Satu --</option>
                    <?php foreach ($opts as $opt): ?>
                      <option value="<?= htmlspecialchars($opt); ?>"><?= htmlspecialchars($opt); ?></option>
                    <?php endforeach; ?>
                  </select>

                <?php elseif ($ftype === 'radio'): ?>
                  <div class="space-y-2 pt-1">
                    <?php foreach ($opts as $o_idx => $opt): ?>
                      <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer">
                        <input type="radio" name="<?= $fname; ?>" value="<?= htmlspecialchars($opt); ?>" <?= ($o_idx === 0 && $freq) ? 'checked' : ''; ?> class="text-teal-700 focus:ring-teal-600">
                        <span><?= htmlspecialchars($opt); ?></span>
                      </label>
                    <?php endforeach; ?>
                  </div>

                <?php elseif ($ftype === 'checkbox'): ?>
                  <div class="space-y-2 pt-1">
                    <?php foreach ($opts as $opt): ?>
                      <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer">
                        <input type="checkbox" name="<?= $fname; ?>[]" value="<?= htmlspecialchars($opt); ?>" class="rounded text-teal-700 focus:ring-teal-600">
                        <span><?= htmlspecialchars($opt); ?></span>
                      </label>
                    <?php endforeach; ?>
                  </div>

                <?php elseif ($ftype === 'file'): ?>
                  <input type="file" name="<?= $fname; ?>" <?= $freq ? 'required' : ''; ?> class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-teal-50 file:text-teal-800 hover:file:bg-teal-100 cursor-pointer">
                  <p class="text-[11px] text-slate-400">Format diizinkan: JPG, PNG, PDF, DOCX (Maks 5 MB)</p>
                <?php endif; ?>

              </div>

            <?php endforeach; ?>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
              <a href="index.php" class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                &larr; Batal & Kembali
              </a>
              <button type="submit" class="px-8 py-3.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow-lg transition transform hover:-translate-y-0.5">
                Kirim Formulir Sekarang &rarr;
              </button>
            </div>

          </form>

        <?php endif; ?>

      </div>

    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
