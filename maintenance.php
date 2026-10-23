<?php
require_once __DIR__ . '/config/database.php';
$settings = getSiteSettings($pdo);
$socials = getSocialMedia($pdo);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Pemeliharaan - <?= htmlspecialchars($settings['nama_kampus'] ?? 'Politeknik Mulia Rokan') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: <?= htmlspecialchars($settings['color_primary'] ?? '#0f766e') ?>;
            --accent: <?= htmlspecialchars($settings['color_accent'] ?? '#f59e0b') ?>;
        }
        .bg-gradient-pmr {
            background: linear-gradient(135deg, #042f2e 0%, #0f766e 50%, #115e59 100%);
        }
    </style>
</head>
<body class="bg-gradient-pmr min-h-screen text-white flex flex-col justify-between antialiased">
    <!-- Header -->
    <header class="py-6 border-b border-teal-500/20 backdrop-blur-md">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 shrink-0">
                    <div class="w-10 h-10 rounded-full bg-white p-1 shadow-sm border border-slate-200 flex items-center justify-center overflow-hidden">
                        <img src="<?= htmlspecialchars($settings['logo_yayasan_url'] ?? 'assets/images/logo-yayasan.png') ?>" alt="Logo Yayasan" class="w-full h-full object-contain">
                    </div>
                    <div class="w-10 h-10 rounded-full bg-white p-1 shadow-sm border border-slate-200 flex items-center justify-center overflow-hidden">
                        <img src="<?= htmlspecialchars($settings['logo_url'] ?? 'assets/images/logo-politeknik.png') ?>" alt="Logo Politeknik" class="w-full h-full object-contain">
                    </div>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-[#e9b35d] uppercase tracking-wider leading-none">YAYASAN ROKAN JAYA BERMARWAH</span>
                    <h1 class="font-extrabold tracking-wide text-lg text-white mt-0.5"><?= htmlspecialchars($settings['nama_kampus'] ?? 'POLITEKNIK MULIA ROKAN') ?></h1>
                    <p class="text-xs text-teal-200">Portal Resmi Penerimaan Mahasiswa Baru</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-400/30 rounded-full text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                Status: Pemeliharaan Server
            </span>
        </div>
    </header>

    <!-- Main Hero -->
    <main class="max-w-3xl mx-auto px-4 py-16 text-center my-auto">
        <div class="w-24 h-24 mx-auto mb-8 rounded-3xl bg-amber-400/20 border-2 border-amber-400/50 flex items-center justify-center text-amber-300 shadow-2xl shadow-teal-950/50">
            <i class="fa-solid fa-screwdriver-wrench text-4xl animate-bounce"></i>
        </div>
        
        <h2 class="text-3xl md:text-5xl font-black mb-4 tracking-tight">Website Sedang Ditingkatkan</h2>
        <p class="text-lg md:text-xl text-teal-100/90 leading-relaxed max-w-2xl mx-auto mb-8">
            <?= nl2br(htmlspecialchars($settings['maintenance_message'] ?? 'Kami sedang melakukan pembaruan berkala pada infrastruktur sistem server dan basis data untuk meningkatkan keandalan layanan PMB.')) ?>
        </p>

        <!-- Helpdesk card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 md:p-8 max-w-xl mx-auto shadow-2xl mb-10">
            <h3 class="text-amber-300 font-bold text-sm tracking-widest uppercase mb-4">Butuh Bantuan Mendesak?</h3>
            <p class="text-sm text-teal-100 mb-6">Layanan konsultasi PMB tetap beroperasi penuh melalui kanal resmi WhatsApp Helpdesk PMB kami.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $settings['whatsapp_helpdesk'] ?? '081234567890') ?>" target="_blank" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl transition shadow-lg shadow-emerald-950/30 text-sm">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                    Chat WhatsApp PMB (<?= htmlspecialchars($settings['whatsapp_helpdesk'] ?? '0812-3456-7890') ?>)
                </a>
                <a href="mailto:<?= htmlspecialchars($settings['email'] ?? 'pmb@politeknikmuliarokan.ac.id') ?>" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-white/20 hover:bg-white/30 text-white font-bold rounded-xl transition border border-white/30 text-sm">
                    <i class="fa-solid fa-envelope"></i>
                    Kirim Email
                </a>
            </div>
        </div>

        <!-- Socials -->
        <?php if (!empty($socials)): ?>
            <div class="flex items-center justify-center gap-4">
                <?php foreach ($socials as $soc): ?>
                    <a href="<?= htmlspecialchars($soc['url']) ?>" target="_blank" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 border border-white/20 flex items-center justify-center text-teal-200 hover:text-white transition" title="<?= htmlspecialchars($soc['platform']) ?>">
                        <i class="<?= htmlspecialchars($soc['icon_class']) ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="py-6 border-t border-teal-500/20 text-center text-xs text-teal-300/80">
        <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['nama_kampus'] ?? 'Politeknik Mulia Rokan') ?>. Tim Infrastruktur & Teknologi Informasi.</p>
    </footer>
</body>
</html>
