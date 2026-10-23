<?php
$page_title = "Dynamic Form Builder";
require_once __DIR__ . '/layout_top.php';

$message = '';
$error = '';

// Buat Form Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_form') {
    $judul_form   = trim($_POST['judul_form'] ?? '');
    $slug         = trim($_POST['slug'] ?? '');
    $deskripsi    = trim($_POST['deskripsi'] ?? '');
    $pesan_sukses = trim($_POST['pesan_sukses'] ?? '');

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul_form)));
    } else {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
    }

    if (empty($judul_form) || empty($slug)) {
        $error = "Judul form dan slug wajib diisi!";
    } else {
        $cek = $pdo->prepare("SELECT id FROM dynamic_forms WHERE slug = ?");
        $cek->execute([$slug]);
        if ($cek->fetch()) {
            $error = "Slug URL tersebut sudah digunakan. Silakan gunakan slug lain.";
        } else {
            $stmt_ins = $pdo->prepare("INSERT INTO dynamic_forms (judul_form, slug, deskripsi, pesan_sukses, is_active) VALUES (?, ?, ?, ?, 1)");
            if ($stmt_ins->execute([$judul_form, $slug, $deskripsi, $pesan_sukses])) {
                $new_form_id = $pdo->lastInsertId();
                $message = "Formulir '{$judul_form}' berhasil dibuat! Sekarang silakan tambahkan field/kolom input.";
                header("Location: form_builder.php?manage_fields={$new_form_id}&msg=created");
                exit;
            } else {
                $error = "Gagal membuat formulir baru.";
            }
        }
    }
}

// Tambah Field ke Form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_field') {
    $form_id     = (int)$_POST['form_id'];
    $label       = trim($_POST['label'] ?? '');
    $field_name  = trim($_POST['field_name'] ?? '');
    $tipe        = $_POST['tipe'] ?? 'text';
    $pilihan     = trim($_POST['pilihan'] ?? '');
    $placeholder = trim($_POST['placeholder'] ?? '');
    $is_required = isset($_POST['is_required']) ? 1 : 0;
    $urutan      = (int)($_POST['urutan'] ?? 1);

    if (empty($field_name)) {
        $field_name = strtolower(preg_replace('/[^a-z0-9_]/', '_', $label));
    }

    if (empty($label)) {
        $error = "Label field tidak boleh kosong!";
    } else {
        $stmt_ins_f = $pdo->prepare("INSERT INTO dynamic_form_fields (form_id, field_name, label, tipe, pilihan, placeholder, is_required, urutan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt_ins_f->execute([$form_id, $field_name, $label, $tipe, $pilihan, $placeholder, $is_required, $urutan])) {
            $message = "Kolom input '{$label}' berhasil ditambahkan ke formulir!";
        } else {
            $error = "Gagal menambahkan field.";
        }
    }
}

// Hapus Field
if (isset($_GET['delete_field'])) {
    $del_f_id = (int)$_GET['delete_field'];
    $f_form_id = (int)($_GET['form_id'] ?? 0);
    $pdo->prepare("DELETE FROM dynamic_form_fields WHERE id = ?")->execute([$del_f_id]);
    header("Location: form_builder.php?manage_fields={$f_form_id}");
    exit;
}

// Hapus Form Keseluruhan
if (isset($_GET['delete_form'])) {
    $del_form_id = (int)$_GET['delete_form'];
    $pdo->prepare("DELETE FROM dynamic_forms WHERE id = ?")->execute([$del_form_id]);
    $message = "Formulir beserta seluruh field dan responnya berhasil dihapus.";
}

// Ambil Daftar Semua Form
$forms_stmt = $pdo->query("SELECT f.*, 
    (SELECT COUNT(*) FROM dynamic_form_fields WHERE form_id = f.id) as total_fields,
    (SELECT COUNT(*) FROM dynamic_form_submissions WHERE form_id = f.id) as total_submissions
    FROM dynamic_forms f ORDER BY f.id DESC");
$forms_list = $forms_stmt->fetchAll();

// Jika sedang kelola fields spesifik form
$manage_form_id = isset($_GET['manage_fields']) ? (int)$_GET['manage_fields'] : 0;
$active_form = null;
$active_fields = [];
if ($manage_form_id > 0) {
    $stmt_af = $pdo->prepare("SELECT * FROM dynamic_forms WHERE id = ?");
    $stmt_af->execute([$manage_form_id]);
    $active_form = $stmt_af->fetch();

    if ($active_form) {
        $stmt_ff = $pdo->prepare("SELECT * FROM dynamic_form_fields WHERE form_id = ? ORDER BY urutan ASC");
        $stmt_ff->execute([$manage_form_id]);
        $active_fields = $stmt_ff->fetchAll();
    }
}
?>

<div class="max-w-6xl mx-auto space-y-8">

  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
      <h2 class="text-xl font-black text-slate-900">Dynamic Form Builder (Pembuat Formulir Kustom)</h2>
      <p class="text-xs text-slate-500">Buat formulir baru tanpa batasan: beasiswa khusus, survei mahasiswa, formulir saran/kontak, atau pendaftaran kegiatan.</p>
    </div>
    <div class="flex gap-2">
      <a href="form_responses.php" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold hover:bg-slate-900 transition flex items-center gap-1.5">
        <span>📥 Lihat Seluruh Respon Masuk</span>
      </a>
    </div>
  </div>

  <?php if (!empty($message)): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between">
      <span>✓ <?= htmlspecialchars($message); ?></span>
      <a href="form_builder.php" class="underline font-bold text-emerald-900">Tutup</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
      ⚠️ <?= htmlspecialchars($error); ?>
    </div>
  <?php endif; ?>

  <!-- MODE KELOLA FIELDS DARI FORM TERTENTU -->
  <?php if ($active_form): ?>
    
    <div class="bg-white rounded-3xl border-2 border-teal-700 shadow-xl p-6 sm:p-8 space-y-6">
      
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-slate-100 pb-4">
        <div>
          <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-teal-100 text-teal-800 uppercase">
              Kelola Kolom Input Form
            </span>
            <span class="text-xs text-slate-400 font-mono">/form.php?slug=<?= htmlspecialchars($active_form['slug']); ?></span>
          </div>
          <h3 class="text-lg font-black text-slate-900 mt-1"><?= htmlspecialchars($active_form['judul_form']); ?></h3>
        </div>

        <div class="flex items-center gap-2">
          <a href="../form.php?slug=<?= urlencode($active_form['slug']); ?>" target="_blank" class="px-3.5 py-1.5 bg-amber-400 hover:bg-amber-500 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-1">
            <span>👁️ Buka Form Live Publik</span>
          </a>
          <a href="form_builder.php" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition">
            &larr; Selesai / Kembali
          </a>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Form Tambah Field Baru -->
        <div class="lg:col-span-5 bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-4">
          <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">
            ➕ Tambah Kolom Input Baru
          </h4>

          <form action="form_builder.php?manage_fields=<?= $active_form['id']; ?>" method="POST" class="space-y-3.5">
            <input type="hidden" name="action" value="add_field">
            <input type="hidden" name="form_id" value="<?= $active_form['id']; ?>">

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Label Pertanyaan / Field *</label>
              <input type="text" name="label" required placeholder="Contoh: Pilihan Konsentrasi Magang" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Input / Kolom *</label>
              <select name="tipe" id="field_type_select" onchange="toggleOptionBox()" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white font-semibold">
                <option value="text">Teks Singkat (Text Input)</option>
                <option value="number">Angka / Nilai (Number)</option>
                <option value="email">Alamat Email (Email)</option>
                <option value="phone">Nomor Telepon / WhatsApp (Phone)</option>
                <option value="date">Pilihan Tanggal (Date Picker)</option>
                <option value="textarea">Teks Paragraf Panjang (Textarea)</option>
                <option value="select">Pilihan Dropdown (Select Box)</option>
                <option value="radio">Pilihan Radio (Pilih Salah Satu)</option>
                <option value="checkbox">Pilihan Checkbox (Pilih Banyak)</option>
                <option value="file">Unggah File / Dokumen / Foto (Upload File)</option>
              </select>
            </div>

            <div id="option_box" class="hidden">
              <label class="block text-xs font-bold text-slate-700 mb-1">Pilihan Opsi (Pisahkan dengan Tanda Koma)</label>
              <textarea name="pilihan" rows="2" placeholder="Contoh: Otomasi Pabrik, Desain Mekanik, Software App" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white"></textarea>
              <p class="text-[10px] text-slate-400 mt-0.5">Wajib diisi jika memilih dropdown, radio, atau checkbox.</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1">Teks Petunjuk (Placeholder)</label>
              <input type="text" name="placeholder" placeholder="Teks bayangan di dalam kotak isian" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="<?= count($active_fields) + 1; ?>" min="1" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700 bg-white">
              </div>

              <div class="flex items-center pt-5">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                  <input type="checkbox" name="is_required" value="1" checked class="rounded text-teal-700 focus:ring-teal-700">
                  <span>Wajib Diisi</span>
                </label>
              </div>
            </div>

            <button type="submit" class="w-full py-2.5 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
              + Tambahkan Kolom Input Ini
            </button>
          </form>
        </div>

        <!-- Tabel Field Terpasang -->
        <div class="lg:col-span-7 space-y-3">
          <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">
            Daftar Kolom Terpasang Pada Form Ini (<?= count($active_fields); ?> Kolom)
          </h4>

          <?php if (empty($active_fields)): ?>
            <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300 text-xs text-slate-500">
              Belum ada kolom input pada formulir ini. Silakan buat kolom baru di sebelah kiri.
            </div>
          <?php else: ?>
            <div class="space-y-2">
              <?php foreach ($active_fields as $f): ?>
                <div class="p-3.5 bg-white rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-lg bg-teal-50 text-teal-800 font-black text-xs flex items-center justify-center border border-teal-100">
                      <?= $f['urutan']; ?>
                    </span>
                    <div>
                      <h5 class="text-xs font-bold text-slate-900">
                        <?= htmlspecialchars($f['label']); ?>
                        <?php if ($f['is_required']): ?>
                          <span class="text-rose-500 text-[10px]">*Wajib</span>
                        <?php endif; ?>
                      </h5>
                      <span class="text-[10px] text-slate-400">
                        Tipe: <strong class="uppercase text-teal-700"><?= $f['tipe']; ?></strong>
                        <?php if (!empty($f['pilihan'])): ?>
                          • Opsi: <?= htmlspecialchars($f['pilihan']); ?>
                        <?php endif; ?>
                      </span>
                    </div>
                  </div>

                  <a href="form_builder.php?delete_field=<?= $f['id']; ?>&form_id=<?= $active_form['id']; ?>" onclick="return confirm('Hapus kolom input ini?')" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg text-xs" title="Hapus Field">
                    🗑️
                  </a>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

      </div>

    </div>

  <?php endif; ?>

  <!-- FORM PEMBUATAN FORMULIR BARU -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Kolom Kiri: Form Baru -->
    <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-4">
      <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-black text-slate-900">➕ Buat Formulir Dinamis Baru</h3>
        <p class="text-[11px] text-slate-500">Tentukan nama, URL slug, dan pesan sukses formulir.</p>
      </div>

      <form action="form_builder.php" method="POST" class="space-y-3.5">
        <input type="hidden" name="action" value="create_form">

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Judul Formulir *</label>
          <input type="text" name="judul_form" required placeholder="Contoh: Pendaftaran Beasiswa Industri Sawit 2026" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Slug URL (Kustom URL Bersih)</label>
          <input type="text" name="slug" placeholder="beasiswa-sawit-2026 (otomatis jika dikosongkan)" class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700">
          <p class="text-[10px] text-slate-400 mt-1">Formulir nantinya dapat diakses di: `form.php?slug=nama-slug`</p>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi / Petunjuk Pengisian</label>
          <textarea name="deskripsi" rows="2" placeholder="Jelaskan tujuan formulir dan instruksi kepada pengisi..." class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 mb-1">Pesan Sukses Setelah Pengiriman</label>
          <textarea name="pesan_sukses" rows="2" placeholder="Terima kasih, formulir Anda telah kami terima!" class="w-full px-3.5 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-teal-700"></textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-teal-800 hover:bg-teal-900 text-white font-bold text-xs rounded-xl shadow transition">
          Lanjut ke Pembuatan Kolom Input &rarr;
        </button>
      </form>
    </div>

    <!-- Kolom Kanan: Daftar Formulir yang Sudah Ada -->
    <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-7 space-y-4">
      <div class="border-b border-slate-100 pb-3 flex justify-between items-center">
        <div>
          <h3 class="text-sm font-black text-slate-900">Daftar Formulir Aktif (CMS)</h3>
          <p class="text-[11px] text-slate-500">Semua formulir kustom yang telah dibuat oleh Super Admin.</p>
        </div>
        <span class="text-xs font-bold text-teal-800 bg-teal-50 px-2.5 py-1 rounded-lg">
          <?= count($forms_list); ?> Formulir
        </span>
      </div>

      <div class="space-y-4">
        <?php foreach ($forms_list as $f): ?>
          <div class="p-5 rounded-2xl border border-slate-200 hover:border-teal-400 transition bg-slate-50/50 space-y-3">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
              <div>
                <h4 class="text-sm font-black text-slate-900"><?= htmlspecialchars($f['judul_form']); ?></h4>
                <div class="flex items-center gap-2 text-[11px] text-slate-500 mt-0.5">
                  <span class="font-mono text-teal-700">form.php?slug=<?= htmlspecialchars($f['slug']); ?></span>
                  <span>•</span>
                  <span><strong><?= $f['total_fields']; ?></strong> kolom input</span>
                  <span>•</span>
                  <span class="text-emerald-700 font-bold"><strong><?= $f['total_submissions']; ?></strong> respon masuk</span>
                </div>
              </div>

              <div class="flex items-center gap-1.5 shrink-0">
                <a href="../form.php?slug=<?= urlencode($f['slug']); ?>" target="_blank" class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-700" title="Pratinjau Live">
                  👁️ Buka Form
                </a>
                <a href="form_builder.php?manage_fields=<?= $f['id']; ?>" class="px-2.5 py-1 bg-teal-800 text-white hover:bg-teal-900 rounded-lg text-[11px] font-bold" title="Kelola Kolom Input">
                  ⚙️ Kelola Field
                </a>
                <a href="form_responses.php?form_id=<?= $f['id']; ?>" class="px-2.5 py-1 bg-emerald-700 text-white hover:bg-emerald-800 rounded-lg text-[11px] font-bold" title="Lihat Respon">
                  📥 Respon
                </a>
                <a href="form_builder.php?delete_form=<?= $f['id']; ?>" onclick="return confirm('Hapus formulir ini beserta seluruh respon yang sudah masuk?')" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-[11px] font-bold" title="Hapus Form">
                  🗑️
                </a>
              </div>
            </div>

            <?php if (!empty($f['deskripsi'])): ?>
              <p class="text-xs text-slate-600 line-clamp-2">
                <?= htmlspecialchars($f['deskripsi']); ?>
              </p>
            <?php endif; ?>

          </div>
        <?php endforeach; ?>
      </div>

    </div>

  </div>

</div>

<script>
function toggleOptionBox() {
  var sel = document.getElementById('field_type_select').value;
  var box = document.getElementById('option_box');
  if (sel === 'select' || sel === 'radio' || sel === 'checkbox') {
    box.classList.remove('hidden');
  } else {
    box.classList.add('hidden');
  }
}
</script>

<?php require_once __DIR__ . '/layout_bottom.php'; ?>
