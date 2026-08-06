<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="QR Code akses peserta <?= esc($quizSession['session_name'], 'attr') ?>">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<?php $participantAccessUrl = site_url('quiz/' . rawurlencode($quizSession['session_token'])); ?>
<body class="qr-share-page">
    <header class="qr-share-header"><div class="participant-brand" aria-label="MKQuizz"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div><div><p class="text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="text-[.62rem] text-slate-400">Akses Peserta</p></div></header>

    <main class="qr-share-main">
        <section id="participant-qr-card" class="qr-share-card" data-url="<?= esc($participantAccessUrl, 'attr') ?>" aria-labelledby="qr-share-title">
            <div class="text-center"><span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-[.62rem] font-bold uppercase tracking-[.14em] text-orange-600"><?= esc($quizSession['material_code'] ?: 'QUIZ') ?></span><h1 id="qr-share-title" class="mt-3 text-2xl font-extrabold tracking-tight text-slate-900"><?= esc($quizSession['quiz_title']) ?></h1><p class="mt-2 text-sm text-slate-500"><?= esc($quizSession['session_name']) ?></p></div>

            <div class="qr-share-content mt-7">
                <div class="qr-share-frame"><canvas id="participant-qr-code" class="block max-w-full" aria-label="QR Code halaman peserta"></canvas><p id="participant-qr-error" class="hidden px-5 py-16 text-center text-sm text-red-500">QR Code gagal dibuat. Gunakan URL di samping.</p></div>
                <div class="flex flex-col justify-center">
                    <p class="text-xs font-bold uppercase tracking-[.16em] text-orange-500">Scan untuk Bergabung</p><h2 class="mt-2 text-xl font-extrabold text-slate-800">Arahkan kamera ke QR Code</h2><p class="mt-2 text-sm leading-relaxed text-slate-500">Peserta akan langsung diarahkan ke halaman pengisian nama lengkap dan PIN quiz.</p>
                    <div class="mt-5 rounded-2xl border border-orange-100 bg-orange-50/60 p-4"><p class="text-[.65rem] font-bold uppercase tracking-wider text-orange-600">Tidak punya kamera?</p><p class="mt-2 text-xs leading-relaxed text-slate-500">Ketik alamat berikut pada browser:</p><p class="mt-2 break-all font-mono text-sm font-bold leading-relaxed text-slate-800"><?= esc($participantAccessUrl) ?></p></div>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 border-t border-slate-100 pt-5 text-xs text-slate-500"><span><strong class="text-slate-700"><?= (int) $quizSession['duration_minutes'] ?></strong> menit</span><span><strong class="text-slate-700"><?= number_format((float) $quizSession['passing_score'], 0, ',', '.') ?></strong> nilai lulus</span><span>PIN berlaku hingga <strong class="text-slate-700"><?= esc(date('d M Y, H:i', strtotime($quizSession['pin_valid_until']))) ?> WIB</strong></span></div>
        </section>
    </main>
    <script src="<?= base_url('assets/js/session-qrcode.js') ?>" defer></script>
</body>
</html>
