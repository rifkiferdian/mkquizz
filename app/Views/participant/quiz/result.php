<?php
$showScore = (bool) $attempt['show_score'];
$isPassed = (bool) $attempt['passed'];
$unanswered = max(0, (int) $attempt['total_questions'] - (int) $attempt['total_answered']);
$durationSeconds = max(0, (int) $attempt['duration_seconds']);
$durationText = intdiv($durationSeconds, 60) . ' menit ' . ($durationSeconds % 60) . ' detik';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Hasil quiz <?= esc($quizSession['quiz_title'], 'attr') ?>">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="participant-result-page">
<header class="participant-header"><div class="participant-brand" aria-label="MKQuizz"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div><div><p class="text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="text-[.62rem] text-slate-400">Hasil Peserta</p></div></header>
<main class="participant-result-main">
    <section class="participant-result-card">
        <div class="result-confetti" aria-hidden="true"></div>
        <div class="relative z-10 text-center">
            <div class="result-status-icon <?= $isPassed ? 'is-passed' : 'is-failed' ?>"><?php if ($isPassed): ?><svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg><?php else: ?><svg class="size-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8v5m0 3h.01"/><circle cx="12" cy="12" r="9"/></svg><?php endif ?></div>
            <p class="mt-5 text-xs font-bold uppercase tracking-[.18em] <?= $isPassed ? 'text-green-600' : 'text-orange-600' ?>"><?= $isPassed ? 'Selamat, Anda Lulus!' : 'Tetap Semangat!' ?></p>
            <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900"><?= esc($participant['name']) ?></h1>
            <p class="mt-2 text-sm text-slate-500">Anda telah menyelesaikan <strong class="text-slate-700"><?= esc($quizSession['quiz_title']) ?></strong>.</p>
            <?php if ($showScore): ?><div class="result-score-ring"><div><p class="text-[.62rem] font-bold uppercase tracking-wider text-slate-400">Nilai Anda</p><p class="mt-1 text-5xl font-black text-orange-600"><?= number_format((float) $attempt['final_score'], 0, ',', '.') ?></p><p class="mt-1 text-[.65rem] text-slate-400">dari 100</p></div></div><?php else: ?><div class="mx-auto mt-7 max-w-md rounded-2xl bg-orange-50 p-5 text-sm text-orange-700">Jawaban berhasil direkam. Nilai disembunyikan oleh presenter.</div><?php endif ?>
            <div class="result-summary-grid"><article><span class="text-green-600"><?= (int) $attempt['total_correct'] ?></span><p>Jawaban benar</p></article><article><span class="text-red-500"><?= (int) $attempt['total_wrong'] ?></span><p>Jawaban salah</p></article><article><span class="text-slate-500"><?= $unanswered ?></span><p>Tidak dijawab</p></article><article><span class="text-blue-600"><?= esc($durationText) ?></span><p>Waktu pengerjaan</p></article></div>
            <div class="mt-7 flex flex-wrap justify-center gap-3"><a class="result-primary-action" href="<?= site_url('quiz/' . rawurlencode($quizSession['session_token']) . '/leaderboard') ?>" target="_blank" rel="noopener">Lihat Leaderboard</a><a class="result-secondary-action" href="<?= site_url('quiz/' . rawurlencode($quizSession['session_token'])) ?>">Kembali ke Halaman Quiz</a></div>
        </div>
    </section>
</main>
</body>
</html>
