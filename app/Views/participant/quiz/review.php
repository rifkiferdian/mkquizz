<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Review jawaban <?= esc($quizSession['quiz_title'], 'attr') ?>">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="participant-quiz-page participant-review-page">
<header class="participant-header participant-quiz-header">
    <div class="participant-brand" aria-label="MKQuizz"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div>
    <div class="min-w-0"><p class="truncate text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="truncate text-[.62rem] text-slate-400">Review Jawaban</p></div>
    <div class="participant-quiz-identity"><p class="truncate text-xs font-bold text-slate-700"><?= esc($participant['name']) ?></p><p class="text-[.58rem] text-slate-400">Peserta</p></div>
</header>

<main class="participant-review-main">
    <section class="google-form-intro" aria-labelledby="review-title">
        <div class="google-form-accent"></div>
        <div class="p-5 md:p-7">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div><span class="inline-flex rounded-full bg-orange-50 px-3 py-1 text-[.62rem] font-bold uppercase tracking-[.14em] text-orange-600">Review Quiz</span><h1 id="review-title" class="mt-3 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl"><?= esc($quizSession['quiz_title']) ?></h1><p class="mt-2 text-sm text-slate-500">Periksa kembali jawaban Anda dan pelajari jawaban yang benar.</p></div>
                <?php if ((bool) $attempt['show_score']): ?><div class="review-score"><p>Nilai</p><strong><?= number_format((float) $attempt['final_score'], 0, ',', '.') ?></strong></div><?php endif ?>
            </div>
            <div class="review-legend"><span><i class="is-correct"></i>Jawaban benar</span><span><i class="is-wrong"></i>Jawaban Anda yang salah</span><span><i class="is-selected"></i>Jawaban Anda</span></div>
        </div>
    </section>

    <div class="mt-4 space-y-4">
        <?php foreach ($questions as $index => $question): ?>
            <?php
            $answer = $question['answer'];
            $selectedOptionId = $answer === null ? null : (int) $answer['selected_option_id'];
            $answerState = $answer === null ? 'empty' : ((bool) $answer['is_correct'] ? 'correct' : 'wrong');
            $answerLabel = match ($answerState) {
                'correct' => 'Jawaban Benar',
                'wrong'   => 'Jawaban Salah',
                default   => 'Tidak Dijawab',
            };
            ?>
            <article class="google-question-card review-question-card <?= 'is-' . esc($answerState) ?>">
                <div class="review-question-heading">
                    <div><span class="inline-flex items-center rounded-full bg-orange-50 px-3 py-1.5 text-[.74rem] font-extrabold uppercase tracking-[.1em] text-orange-600">Pertanyaan <?= $index + 1 ?></span><h2 class="mt-3 text-[.95rem] font-medium leading-relaxed text-slate-800 md:text-base"><?= nl2br(esc($question['question_text'])) ?></h2></div>
                    <span class="review-answer-status <?= 'is-' . esc($answerState) ?>"><?= esc($answerLabel) ?></span>
                </div>

                <div class="space-y-2 px-5 pb-5 md:px-7 md:pb-6">
                    <?php foreach ($question['options'] as $optionIndex => $option): ?>
                        <?php
                        $isSelected = $selectedOptionId === (int) $option['id'];
                        $isCorrect = (bool) $option['is_correct'];
                        $optionClasses = trim(($isCorrect ? ' is-correct' : '') . ($isSelected ? ' is-selected' : '') . ($isSelected && ! $isCorrect ? ' is-wrong' : ''));
                        ?>
                        <div class="review-option <?= esc($optionClasses, 'attr') ?>">
                            <span class="google-option-marker"><?= chr(65 + $optionIndex) ?></span>
                            <span class="min-w-0 flex-1 text-sm leading-relaxed text-slate-700"><?= nl2br(esc($option['option_text'])) ?></span>
                            <?php if ($isCorrect && $isSelected): ?><span class="review-option-label is-correct">Jawaban Anda &middot; Benar</span><?php elseif ($isCorrect): ?><span class="review-option-label is-correct">Jawaban Benar</span><?php elseif ($isSelected): ?><span class="review-option-label is-wrong">Jawaban Anda</span><?php endif ?>
                        </div>
                    <?php endforeach ?>
                </div>

                <?php if ((bool) $attempt['show_explanation'] && trim((string) $question['explanation']) !== ''): ?><div class="review-explanation"><div class="grid size-8 shrink-0 place-items-center rounded-full bg-blue-50 text-blue-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg></div><div><p class="text-[.62rem] font-bold uppercase tracking-wider text-blue-600">Penjelasan</p><p class="mt-1 text-xs leading-relaxed text-slate-600"><?= nl2br(esc($question['explanation'])) ?></p></div></div><?php endif ?>
            </article>
        <?php endforeach ?>
    </div>

    <div class="review-footer-actions"><a class="result-secondary-action" href="<?= site_url('quiz/' . rawurlencode($quizSession['session_token']) . '/result/' . $attempt['id']) ?>">Kembali ke Hasil</a><a class="result-primary-action" href="<?= site_url('quiz/' . rawurlencode($quizSession['session_token']) . '/leaderboard') ?>" target="_blank" rel="noopener">Lihat Leaderboard</a></div>
</main>
</body>
</html>
