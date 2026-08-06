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
<?php
$participantAccessUrl = site_url('quiz/' . rawurlencode($quizSession['session_token']));
$timezone = new DateTimeZone('Asia/Jakarta');
$validFrom = new DateTimeImmutable($quizSession['pin_valid_from'], $timezone);
$validUntil = new DateTimeImmutable($quizSession['pin_valid_until'], $timezone);
$now = new DateTimeImmutable('now', $timezone);
$pinState = match (true) {
    $now < $validFrom  => ['Belum berlaku', 'is-waiting'],
    $now > $validUntil => ['Kedaluwarsa', 'is-expired'],
    default            => ['Aktif', 'is-active'],
};
?>
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
                    <div class="qr-pin-access-grid mt-4">
                        <article class="qr-pin-card">
                            <div><p class="qr-pin-label">PIN Quiz</p><div class="mt-2 flex items-center gap-3"><p id="participant-pin-value" class="qr-pin-value" data-pin="<?= esc($quizSession['pin'], 'attr') ?>">&bull;&bull;&bull;&bull;&bull;&bull;</p><button id="toggle-participant-pin" type="button" class="qr-pin-toggle" aria-label="Tampilkan PIN quiz" aria-controls="participant-pin-value" aria-pressed="false"><svg data-eye-open class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg><svg data-eye-closed class="hidden size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m3 3 18 18M10.6 10.7a2 2 0 0 0 2.7 2.7M9.9 5.2A11.5 11.5 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-2.1 3M6.6 6.6C3.6 8.5 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4-.8"/></svg></button></div></div>
                            <p class="mt-2 text-[.58rem] text-slate-400">Klik ikon mata untuk melihat PIN.</p>
                        </article>
                        <article id="pin-validity-card" class="qr-pin-card <?= esc($pinState[1]) ?>" data-valid-from="<?= esc($validFrom->format(DateTimeInterface::ATOM), 'attr') ?>" data-valid-until="<?= esc($validUntil->format(DateTimeInterface::ATOM), 'attr') ?>">
                            <div class="flex items-center justify-between gap-2"><p class="qr-pin-label">Masa Berlaku PIN</p><span id="pin-validity-status" class="qr-pin-status"><?= esc($pinState[0]) ?></span></div>
                            <p id="pin-validity-countdown" class="qr-pin-countdown">00:00:00</p>
                            <p id="pin-validity-message" class="mt-1 text-[.62rem] font-bold text-orange-600">Menghitung sisa waktu...</p>
                            <p class="mt-2 text-[.58rem] leading-relaxed text-slate-400">Durasi akses <?= (int) $quizSession['pin_valid_minutes'] ?> menit &middot; <?= esc($validFrom->format('d M Y, H:i')) ?> &ndash; <?= esc($validUntil->format('d M Y, H:i')) ?> WIB</p>
                        </article>
                    </div>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 border-t border-slate-100 pt-5 text-xs text-slate-500"><span><strong class="text-slate-700"><?= (int) $quizSession['duration_minutes'] ?></strong> menit</span><span><strong class="text-slate-700"><?= number_format((float) $quizSession['passing_score'], 0, ',', '.') ?></strong> nilai lulus</span><span>Status sesi <strong class="text-slate-700"><?= esc($quizSession['status']) ?></strong></span></div>
        </section>
    </main>
    <script src="<?= base_url('assets/js/session-qrcode.js') ?>" defer></script>
</body>
</html>
