<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="participant-page">
<header class="participant-header"><div class="participant-brand"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div><div><p class="text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="text-[.62rem] text-slate-400">Akses Peserta</p></div></header>
<main class="participant-main">
    <section class="participant-join-card text-center">
        <div class="mx-auto grid size-16 place-items-center rounded-full bg-green-50 text-green-600"><svg class="size-8" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg></div>
        <p class="mt-5 text-xs font-bold uppercase tracking-[.16em] text-orange-500">Berhasil Bergabung</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Halo, <?= esc($participant['name']) ?>!</h1>
        <p class="mt-2 text-sm leading-relaxed text-slate-500">Data Anda sudah terdaftar pada sesi <strong class="text-slate-700"><?= esc($quizSession['session_name']) ?></strong>.</p>
        <div class="mt-6 rounded-2xl border border-orange-100 bg-orange-50/70 p-5"><p class="text-xs text-orange-600">Quiz yang akan dikerjakan</p><p class="mt-2 text-base font-extrabold text-slate-800"><?= esc($quizSession['quiz_title']) ?></p><p class="mt-2 text-[.68rem] text-slate-500"><?= (int) $quizSession['question_count'] ?> pertanyaan &middot; <?= (int) $quizSession['duration_minutes'] ?> menit</p></div>
        <?php if (session('error')): ?><div class="participant-alert participant-alert-error mt-5 text-left" role="alert"><?= esc(session('error')) ?></div><?php endif ?>
        <form action="<?= site_url('quiz/' . rawurlencode($quizSession['session_token']) . '/start') ?>" method="post"><?= csrf_field() ?><button type="submit" class="participant-start-button"><span>Mulai Mengerjakan</span><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14m-5-5 5 5-5 5"/></svg></button></form>
        <div class="participant-info mt-5 text-left"><svg class="mt-0.5 size-4 shrink-0 text-orange-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg><p>Waktu akan langsung berjalan setelah quiz dimulai. Pastikan koneksi internet Anda stabil.</p></div>
    </section>
</main>
</body>
</html>
