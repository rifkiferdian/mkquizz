<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Pengerjaan quiz <?= esc($quizSession['quiz_title'], 'attr') ?>">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="participant-quiz-page">
<header class="participant-header participant-quiz-header">
    <div class="participant-brand" aria-label="MKQuizz"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div>
    <div class="min-w-0"><p class="truncate text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="truncate text-[.62rem] text-slate-400"><?= esc($quizSession['session_name']) ?></p></div>
    <div class="participant-quiz-identity"><p class="truncate text-xs font-bold text-slate-700"><?= esc($participant['name']) ?></p><p class="text-[.58rem] text-slate-400">Peserta</p></div>
</header>

<main class="participant-quiz-main">
    <form id="participant-quiz-form" action="<?= site_url('quiz/' . rawurlencode($quizSession['session_token']) . '/attempt/' . $attempt['id'] . '/submit') ?>" method="post" data-remaining-seconds="<?= (int) $remainingSeconds ?>" data-total-questions="<?= count($questions) ?>">
        <?= csrf_field() ?>
        <section class="google-form-intro" aria-labelledby="quiz-title">
            <div class="google-form-accent"></div>
            <div class="p-5 md:p-7">
                <span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-[.62rem] font-bold uppercase tracking-[.14em] text-orange-600"><?= esc($quizSession['material_code'] ?: 'QUIZ') ?></span>
                <h1 id="quiz-title" class="mt-3 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl"><?= esc($quizSession['quiz_title']) ?></h1>
                <?php if ($quizSession['quiz_description']): ?><p class="mt-3 text-sm leading-relaxed text-slate-500"><?= nl2br(esc($quizSession['quiz_description'])) ?></p><?php endif ?>
                <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 border-t border-slate-100 pt-4 text-xs text-slate-500"><span><strong class="text-slate-700"><?= count($questions) ?></strong> pertanyaan</span><span><strong class="text-slate-700"><?= (int) $quizSession['duration_minutes'] ?></strong> menit</span><span>Nilai lulus <strong class="text-slate-700"><?= number_format((float) $quizSession['passing_score'], 0, ',', '.') ?></strong></span></div>
                <p class="mt-4 text-xs font-medium text-red-500">* Periksa semua pertanyaan sebelum mengirim jawaban.</p>
            </div>
        </section>

        <?php if (session('error')): ?><div class="participant-alert participant-alert-error my-4" role="alert"><?= esc(session('error')) ?></div><?php endif ?>

        <div class="participant-quiz-layout">
            <div class="space-y-4">
                <?php foreach ($questions as $index => $question): ?>
                    <fieldset class="google-question-card" data-question-card>
                        <legend class="w-full px-5 pt-5 md:px-7 md:pt-6"><span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1.5 text-[.74rem] font-extrabold uppercase tracking-[.1em] text-orange-600">Pertanyaan <?= $index + 1 ?></span><span class="mt-3 block text-[.95rem] font-medium leading-relaxed text-slate-800 md:text-base"><?= nl2br(esc($question['question_text'])) ?> <span class="text-red-500">*</span></span></legend>
                        <div class="space-y-2 px-5 pb-5 pt-4 md:px-7 md:pb-6">
                            <?php foreach ($question['options'] as $optionIndex => $option): ?>
                                <label class="google-option"><input type="radio" name="answers[<?= (int) $question['question_id'] ?>]" value="<?= (int) $option['id'] ?>" data-answer-input><span class="google-option-marker"><?= chr(65 + $optionIndex) ?></span><span class="min-w-0 flex-1 text-sm leading-relaxed text-slate-700"><?= nl2br(esc($option['option_text'])) ?></span></label>
                            <?php endforeach ?>
                        </div>
                    </fieldset>
                <?php endforeach ?>

                <section class="google-submit-card"><div><p class="text-sm font-bold text-slate-800">Sudah yakin dengan jawaban Anda?</p><p class="mt-1 text-xs text-slate-500">Jawaban tidak dapat diubah setelah dikirim.</p></div><button id="quiz-submit-button" type="submit" class="google-submit-button"><span>Kirim Jawaban</span><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m4 4 17 8-17 8 4-8-4-8Zm4 8h13"/></svg></button></section>
            </div>

            <aside class="participant-quiz-status" aria-label="Status pengerjaan">
                <div class="participant-timer"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="13" r="8"/><path d="M12 9v4l2.5 2M9 2h6"/></svg><div><p class="text-[.58rem] font-bold uppercase tracking-wider text-orange-500">Sisa waktu</p><p id="quiz-timer" class="mt-1 text-xl font-black tabular-nums text-slate-800">--:--</p></div></div>
                <div class="mt-5"><div class="flex items-center justify-between text-[.68rem]"><span class="font-medium text-slate-500">Progress jawaban</span><strong id="quiz-progress-text" class="text-slate-700">0/<?= count($questions) ?></strong></div><div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100"><div id="quiz-progress-bar" class="h-full w-0 rounded-full bg-orange-500 transition-all"></div></div></div>
                <p class="mt-4 text-[.66rem] leading-relaxed text-slate-400">Quiz akan dikirim otomatis ketika waktu habis.</p>
            </aside>
        </div>
    </form>
</main>
<script src="<?= base_url('assets/js/participant-quiz.js') ?>" defer></script>
</body>
</html>
