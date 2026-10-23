<?php
require_once __DIR__ . '/../config/database.php';
checkMaintenanceMode($pdo);

$site = getSiteSettings($pdo);
$socials = getSocialMedia($pdo);

$nama_kampus = !empty($site['nama_kampus']) ? $site['nama_kampus'] : 'POLITEKNIK MULIA ROKAN';
$tagline = !empty($site['tagline']) ? $site['tagline'] : 'Penerimaan Mahasiswa Baru TA 2026/2027';
$logo_url = !empty($site['logo_url']) ? $site['logo_url'] : 'assets/images/logo-resmi.jpg';
$color_primary = !empty($site['color_primary']) ? $site['color_primary'] : '#0f766e';
$color_accent = !empty($site['color_accent']) ? $site['color_accent'] : '#f59e0b';
$running_text = !empty($site['running_text']) ? $site['running_text'] : '';

$site_title = isset($page_title) ? $page_title . ' - ' . $nama_kampus : (!empty($site['seo_meta_title']) ? $site['seo_meta_title'] : $nama_kampus . ' - ' . $tagline);
$meta_desc = !empty($site['seo_meta_description']) ? $site['seo_meta_description'] : ($site['sub_nama'] ?? $tagline);
$meta_keywords = !empty($site['seo_meta_keywords']) ? $site['seo_meta_keywords'] : 'pmb politeknik mulia rokan, vokasi sawit riau, pmb vokasi d4';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($site_title); ?></title>
  <meta name="description" content="<?= htmlspecialchars($meta_desc); ?>">
  <meta name="keywords" content="<?= htmlspecialchars($meta_keywords); ?>">
  
  <!-- Open Graph / Meta Medsos -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($site_title); ?>">
  <meta property="og:description" content="<?= htmlspecialchars($meta_desc); ?>">
  <meta property="og:image" content="<?= htmlspecialchars(base_url($logo_url)); ?>">
  <meta property="og:url" content="<?= htmlspecialchars(base_url($_SERVER['REQUEST_URI'] ?? '')); ?>">

  <link rel="icon" type="image/jpeg" href="<?= htmlspecialchars($logo_url); ?>">
  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: {
              DEFAULT: '<?= $color_primary ?>',
              dark: '<?= $site['color_primary_dark'] ?? "#108e9a" ?>',
              light: '#e6faf5'
            },
            accent: {
              DEFAULT: '<?= $color_accent ?>',
              dark: '<?= $site['color_accent_dark'] ?? "#f9ad3f" ?>',
              light: '#fffdf5'
            },
            gear: {
              mint: '#1bb28f',
              ocean: '#1b969d',
              left: '#1bb28f',
              right: '#1b969d',
              mid: '#1ba496'
            },
            logoGold: '#e9b35d'
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; }
  </style>

  <?php if (!empty($site['custom_head_scripts'])): ?>
    <!-- Custom Head Script Injection (Google Analytics / Pixel / Meta) -->
    <?= $site['custom_head_scripts']; ?>
  <?php endif; ?>
</head>
<body class="bg-white text-slate-700 antialiased flex flex-col min-h-screen">

  <!-- RUNNING ANNOUNCEMENT TICKER (Jika diaktifkan di CMS) -->
  <?php if (!empty($running_text)): ?>
  <div class="text-white text-xs py-2 px-4 overflow-hidden border-b border-white/20 shadow-sm" style="background: linear-gradient(90deg, #1bb28f 0%, #1b969d 100%);">
    <div class="max-w-7xl mx-auto flex items-center gap-3">
      <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-black bg-[#e9b35d] text-slate-950 uppercase tracking-wider shrink-0 shadow">
        INFO PMB
      </span>
      <marquee behavior="scroll" direction="left" class="text-xs font-semibold text-white tracking-wide" scrollamount="6">
        <?= htmlspecialchars($running_text); ?>
      </marquee>
    </div>
  </div>
  <?php endif; ?>

  <!-- TOP INFO BAR & SOCIAL MEDIA -->
  <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
    <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
      
      <div class="flex flex-wrap items-center gap-4 text-[11px] sm:text-xs">
        <span class="flex items-center gap-1.5 text-slate-300">
          <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
          <span>Bagansiapiapi, Kab. Rokan Hilir, Riau</span>
        </span>
        <span class="hidden md:inline text-slate-700">|</span>
        <span class="hidden md:flex items-center gap-1 text-slate-400">
          <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
          <span>Pendidikan Tinggi Vokasi Resmi SK Kemendiktisaintek RI</span>
        </span>
      </div>

      <div class="flex items-center gap-4">
        <!-- Social Media Icons Links -->
        <div class="flex items-center gap-2.5">
          <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold hidden sm:inline">Ikuti Kami:</span>
          <?php foreach ($socials as $soc): 
            $soc_icon = strtolower($soc['platform']);
            $soc_label = htmlspecialchars($soc['nama_label']);
            $soc_url = htmlspecialchars($soc['url']);
          ?>
            <a href="<?= $soc_url; ?>" target="_blank" title="<?= $soc_label; ?>" class="text-slate-400 hover:text-amber-400 transition-colors p-1 rounded hover:bg-slate-800">
              <?php if (strpos($soc_icon, 'instagram') !== false): ?>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
              <?php elseif (strpos($soc_icon, 'facebook') !== false): ?>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
              <?php elseif (strpos($soc_icon, 'youtube') !== false): ?>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
              <?php elseif (strpos($soc_icon, 'tiktok') !== false): ?>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.59-1v5.77c-.01 2.37-.8 4.74-2.31 6.57-2.35 2.87-6.4 3.84-9.82 2.34-3.54-1.55-5.59-5.46-4.85-9.3.62-3.23 3.15-5.77 6.36-6.4 1.1-.22 2.22-.17 3.33-.07v4.11c-.72-.25-1.5-.3-2.25-.13-1.63.37-2.84 1.76-2.97 3.44-.15 1.86 1.09 3.55 2.89 3.96 1.74.4 3.63-.44 4.33-2.07.2-.46.29-.96.29-1.47v-14.79z"/></svg>
              <?php elseif (strpos($soc_icon, 'whatsapp') !== false): ?>
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>

        <span class="text-slate-700">|</span>

        <!-- WhatsApp Helpdesk Link -->
        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $site['whatsapp_helpdesk'] ?? '6281234567890'); ?>" target="_blank" class="hover:text-amber-400 flex items-center gap-1.5 text-slate-300 font-semibold transition-colors">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
          <span>Helpdesk WA: <?= htmlspecialchars($site['whatsapp_helpdesk'] ?? '0812-3456-7890'); ?></span>
        </a>
      </div>

    </div>
  </div>
