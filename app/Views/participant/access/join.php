<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Masuk ke sesi quiz <?= esc($quizSession['quiz_title'], 'attr') ?>">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="participant-page">
<?php
$errors = session('errors') ?? [];
$timezone = new DateTimeZone('Asia/Jakarta');
$now = new DateTimeImmutable('now', $timezone);
$validFrom = new DateTimeImmutable($quizSession['pin_valid_from'], $timezone);
$validUntil = new DateTimeImmutable($quizSession['pin_valid_until'], $timezone);
$canJoin = $quizSession['status'] === 'OPEN' && $now >= $validFrom && $now <= $validUntil;
$accessMessage = match (true) {
    $quizSession['status'] === 'CLOSED' => 'Sesi quiz sudah ditutup.',
    $quizSession['status'] !== 'OPEN'   => 'Sesi quiz belum dibuka oleh presenter.',
    $now < $validFrom                   => 'PIN belum berlaku. Sesi dimulai ' . date('d M Y, H:i', strtotime($quizSession['pin_valid_from'])) . ' WIB.',
    $now > $validUntil                  => 'PIN sudah kedaluwarsa. Silakan hubungi presenter.',
    default                             => '',
};
?>
<header class="participant-header">
    <div class="participant-brand" aria-label="MKQuizz"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div>
    <div><p class="text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="text-[.62rem] text-slate-400">Akses Peserta</p></div>
</header>

<main class="participant-main">
    <section class="participant-join-card" aria-labelledby="quiz-access-title">
        <div class="text-center">
            <div class="participant-quiz-icon"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9"><path d="m3 9 9-5 9 5-9 5-9-5Z"/><path d="M7 12v5c3 2 7 2 10 0v-5M21 9v6"/></svg></div>
            <span class="mt-4 inline-flex rounded-full bg-orange-50 px-3 py-1 text-[.62rem] font-bold uppercase tracking-[.14em] text-orange-600"><?= esc($quizSession['material_code'] ?: 'QUIZ') ?></span>
            <p class="mt-3 text-[.68rem] font-semibold text-slate-400"><?= esc($quizSession['session_name']) ?></p>
            <h1 id="quiz-access-title" class="mt-1 text-xl font-extrabold tracking-tight text-slate-900"><?= esc($quizSession['quiz_title']) ?></h1>
            <div class="mt-4 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-[.7rem] text-slate-500">
                <span class="inline-flex items-center gap-1.5"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg><?= (int) $quizSession['question_count'] ?> Pertanyaan</span>
                <span class="inline-flex items-center gap-1.5"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2M9 2h6"/></svg><?= (int) $quizSession['duration_minutes'] ?> Menit</span>
                <span class="inline-flex items-center gap-1.5"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3 2.6 5.3 5.9.8-4.3 4.2 1 5.9-5.2-2.8-5.2 2.8 1-5.9-4.3-4.2 5.9-.8L12 3Z"/></svg>Nilai lulus <?= number_format((float) $quizSession['passing_score'], 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="my-6 border-t border-slate-100"></div>

        <?php if (session('error')): ?><div class="participant-alert participant-alert-error" role="alert"><svg class="mt-0.5 size-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5m0 3h.01"/></svg><span><?= esc(session('error')) ?></span></div><?php endif ?>
        <?php if (! $canJoin): ?><div class="participant-alert participant-alert-warning" role="status"><svg class="mt-0.5 size-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5m0 3h.01"/></svg><span><?= esc($accessMessage) ?></span></div><?php endif ?>

        <form action="<?= site_url('quiz/' . rawurlencode($quizSession['session_token']) . '/join') ?>" method="post" class="<?= session('error') || ! $canJoin ? 'mt-5' : '' ?>">
            <?= csrf_field() ?>
            <div><label for="participant-name" class="participant-label">Nama Lengkap</label><div class="relative"><svg class="participant-input-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="3"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg><input id="participant-name" name="name" type="text" value="<?= esc(old('name'), 'attr') ?>" maxlength="150" autocomplete="name" class="participant-input <?= isset($errors['name']) ? 'has-error' : '' ?>" placeholder="Masukkan nama lengkap Anda" required <?= ! $canJoin ? 'disabled' : '' ?>></div><?php if (isset($errors['name'])): ?><p class="participant-error"><?= esc($errors['name']) ?></p><?php endif ?></div>
            <div class="mt-4"><label for="quiz-pin" class="participant-label">PIN Quiz</label><div class="relative"><svg class="participant-input-icon" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M7 9h.01M11 9h.01M15 9h2M7 13h2m2 0h2m2 0h2"/></svg><input id="quiz-pin" name="pin" type="text" value="<?= esc(old('pin'), 'attr') ?>" minlength="6" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" autocomplete="one-time-code" class="participant-input <?= isset($errors['pin']) ? 'has-error' : '' ?>" placeholder="Masukkan PIN 6 digit" required <?= ! $canJoin ? 'disabled' : '' ?>></div><?php if (isset($errors['pin'])): ?><p class="participant-error"><?= esc($errors['pin']) ?></p><?php endif ?></div>
            <button type="submit" class="participant-start-button" <?= ! $canJoin ? 'disabled' : '' ?>><span>Mulai Quiz</span><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-5-5 5 5-5 5"/></svg></button>
        </form>

        <div class="participant-info"><svg class="mt-0.5 size-4 shrink-0 text-orange-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg><p>Pastikan nama yang dimasukkan benar karena akan digunakan pada hasil quiz.</p></div>

        <footer class="mt-5 border-t border-slate-100 pt-4 text-center text-[.62rem] leading-relaxed text-slate-400">
            Aplikasi ini dibuat oleh <span class="font-semibold text-slate-600">Divisi Software Engineer Manna Kampus</span><br>
            by <span class="font-bold text-orange-600">Ahmad Rifki</span>
        </footer>
    </section>
</main>
<script src="<?= base_url('assets/js/participant-access.js') ?>" defer></script>
</body>
</html>
