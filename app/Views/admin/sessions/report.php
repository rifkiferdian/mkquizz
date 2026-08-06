<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$difficultyLabels = ['EASY' => 'Mudah', 'MEDIUM' => 'Sedang', 'HARD' => 'Sulit'];
$submittedAttempts = (int) $summary['submitted_attempts'];
$totalResponses = (int) $summary['total_answers'];
$overallWrongRate = $totalResponses > 0 ? round(((int) $summary['wrong_answers'] / $totalResponses) * 100, 1) : 0.0;
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="<?= site_url('admin/sessions/' . $quizSession['id']) ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Kembali ke Detail Sesi</a>
        <span class="rounded-full border border-orange-100 bg-orange-50 px-3 py-1.5 text-[.65rem] font-bold text-orange-600"><?= $submittedAttempts ?> attempt dianalisis</span>
    </div>

    <section class="question-report-hero mt-5" aria-labelledby="question-report-title">
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-200">Question Evaluation Report</p><h2 id="question-report-title" class="mt-2 text-2xl font-black tracking-tight text-white md:text-3xl">Evaluasi Jawaban Peserta</h2><p class="mt-2 max-w-3xl text-sm leading-relaxed text-orange-100">Analisis jawaban benar dan salah untuk menemukan soal yang perlu diperjelas atau materi yang perlu disampaikan kembali.</p><div class="mt-4 flex flex-wrap gap-2"><span class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[.62rem] font-bold text-white"><?= esc($quizSession['session_name']) ?></span><span class="rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[.62rem] font-bold text-white"><?= esc($quizSession['quiz_title']) ?></span></div></div>
            <div class="grid size-20 shrink-0 place-items-center rounded-2xl border border-white/20 bg-white/10 text-orange-100"><svg class="size-10" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/><path d="M2 21h20"/></svg></div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan report soal">
        <?php foreach ([
            ['Attempt Selesai', $submittedAttempts, 'data pengerjaan', 'bg-blue-50 text-blue-600'],
            ['Total Jawaban', $summary['total_answers'], 'jawaban terisi', 'bg-violet-50 text-violet-600'],
            ['Jawaban Benar', $summary['correct_answers'], number_format((float) $summary['correct_rate'], 1, ',', '.') . '% akurasi', 'bg-green-50 text-green-600'],
            ['Jawaban Salah', $summary['wrong_answers'], number_format($overallWrongRate, 1, ',', '.') . '% kesalahan', 'bg-red-50 text-red-600'],
        ] as [$label, $value, $unit, $color]): ?>
            <article class="stat-card flex items-center gap-4 rounded-2xl border border-slate-100 bg-white p-5"><div class="grid size-11 shrink-0 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9m5 10V5m5 14v-7m5 7V3"/></svg></div><div><p class="text-[.65rem] font-medium text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold leading-none text-slate-800"><?= number_format((int) $value, 0, ',', '.') ?></p><p class="mt-1 text-[.58rem] text-slate-400"><?= esc($unit) ?></p></div></article>
        <?php endforeach ?>
    </section>

    <div class="mt-6 grid items-stretch gap-6 lg:grid-cols-[minmax(18rem,.65fr)_minmax(0,1.35fr)]">
        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="answer-composition-title">
            <div><p class="text-[.62rem] font-bold uppercase tracking-[.15em] text-orange-500">Overall Accuracy</p><h3 id="answer-composition-title" class="mt-1 text-base font-extrabold text-slate-800">Komposisi Jawaban</h3></div>
            <div class="question-report-donut" style="--correct-rate: <?= esc((string) $summary['correct_rate'], 'attr') ?>; --wrong-rate: <?= esc((string) $overallWrongRate, 'attr') ?>"><div><strong><?= number_format((float) $summary['correct_rate'], 1, ',', '.') ?>%</strong><span>jawaban benar</span></div></div>
            <div class="mt-5 grid grid-cols-2 gap-3"><div class="rounded-xl bg-green-50 p-3"><p class="flex items-center gap-2 text-[.62rem] font-bold text-green-700"><span class="size-2 rounded-full bg-green-500"></span>Benar</p><p class="mt-2 text-lg font-black text-green-700"><?= number_format((int) $summary['correct_answers'], 0, ',', '.') ?></p></div><div class="rounded-xl bg-red-50 p-3"><p class="flex items-center gap-2 text-[.62rem] font-bold text-red-700"><span class="size-2 rounded-full bg-red-500"></span>Salah</p><p class="mt-2 text-lg font-black text-red-700"><?= number_format((int) $summary['wrong_answers'], 0, ',', '.') ?></p></div></div>
        </section>

        <section class="question-report-highlight" aria-labelledby="hardest-question-title">
            <div class="flex items-start justify-between gap-4"><div><p class="text-[.62rem] font-bold uppercase tracking-[.15em] text-red-500">Perlu Perhatian</p><h3 id="hardest-question-title" class="mt-1 text-base font-extrabold text-slate-800">Soal Paling Banyak Salah</h3></div><div class="grid size-10 shrink-0 place-items-center rounded-xl bg-red-50 text-red-500"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 8v5m0 3h.01"/><circle cx="12" cy="12" r="9"/></svg></div></div>
            <?php if ($hardestQuestion !== null && (int) $hardestQuestion['wrong_count'] > 0): ?>
                <div class="mt-5 rounded-2xl border border-red-100 bg-red-50/60 p-5"><div class="flex flex-wrap items-center gap-2"><span class="rounded-full bg-red-100 px-2.5 py-1 text-[.58rem] font-bold text-red-600">SOAL <?= (int) $hardestQuestion['sort_order'] ?></span><span class="rounded-full bg-white px-2.5 py-1 text-[.58rem] font-bold text-slate-500"><?= esc($difficultyLabels[$hardestQuestion['difficulty']] ?? $hardestQuestion['difficulty']) ?></span></div><p class="mt-3 text-sm font-semibold leading-relaxed text-slate-700"><?= esc($hardestQuestion['question_text']) ?></p><div class="mt-4 flex flex-wrap gap-4 text-xs"><span><strong class="text-2xl font-black text-red-600"><?= number_format((float) $hardestQuestion['wrong_rate'], 1, ',', '.') ?>%</strong><span class="ml-1 text-slate-400">jawaban salah</span></span><span><strong class="text-slate-700"><?= (int) $hardestQuestion['wrong_count'] ?></strong><span class="ml-1 text-slate-400">dari <?= (int) $hardestQuestion['answered_count'] ?> jawaban</span></span></div><div class="mt-4 rounded-xl border border-green-100 bg-white px-4 py-3"><p class="text-[.6rem] font-bold uppercase tracking-wider text-green-600">Jawaban yang benar</p><p class="mt-1 text-xs font-semibold leading-relaxed text-slate-700"><?= esc($hardestQuestion['correct_answer'] ?? '-') ?></p></div></div>
            <?php else: ?>
                <div class="mt-5 grid min-h-52 place-items-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center"><div><p class="text-sm font-bold text-slate-600">Belum ada jawaban salah</p><p class="mt-1 text-xs text-slate-400">Data evaluasi akan muncul setelah peserta mengirim jawaban.</p></div></div>
            <?php endif ?>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="question-chart-title">
        <div class="border-b border-orange-100 bg-gradient-to-r from-white to-orange-50 px-5 py-4"><div class="flex flex-wrap items-start justify-between gap-4"><div><h3 id="question-chart-title" class="text-base font-extrabold text-slate-800">Analisis per Pertanyaan</h3><p class="mt-1 text-[.68rem] text-slate-400">Leaderboard soal berdasarkan jumlah jawaban benar atau salah.</p></div><form action="<?= site_url('admin/sessions/' . $quizSession['id'] . '/report') ?>" method="get" class="question-report-filter"><label for="question-report-sort" class="sr-only">Urutkan soal</label><select id="question-report-sort" name="sort" class="question-report-select"><option value="wrong" <?= $questionSort === 'wrong' ? 'selected' : '' ?>>Paling Banyak Salah</option><option value="correct" <?= $questionSort === 'correct' ? 'selected' : '' ?>>Paling Banyak Benar</option><option value="order" <?= $questionSort === 'order' ? 'selected' : '' ?>>Urutan Soal Asli</option></select><button type="submit" class="question-report-filter-button"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z"/></svg>Terapkan</button></form></div><div class="mt-4 flex flex-wrap gap-3 text-[.62rem] font-semibold"><span class="flex items-center gap-1.5 text-green-600"><i class="size-2 rounded-full bg-green-500"></i>Benar</span><span class="flex items-center gap-1.5 text-red-600"><i class="size-2 rounded-full bg-red-500"></i>Salah</span><span class="flex items-center gap-1.5 text-slate-400"><i class="size-2 rounded-full bg-slate-300"></i>Tidak dijawab</span></div></div>

        <div class="divide-y divide-slate-100">
            <?php if ($questions === []): ?><div class="px-5 py-14 text-center text-sm text-slate-400">Quiz belum memiliki pertanyaan.</div><?php endif ?>
            <?php foreach ($questions as $index => $question): ?>
                <?php
                $correctWidth = $submittedAttempts > 0 ? ((int) $question['correct_count'] / $submittedAttempts) * 100 : 0;
                $wrongWidth = $submittedAttempts > 0 ? ((int) $question['wrong_count'] / $submittedAttempts) * 100 : 0;
                $unansweredWidth = max(0, 100 - $correctWidth - $wrongWidth);
                [$evaluationLabel, $evaluationClass] = match (true) {
                    (int) $question['answered_count'] === 0 => ['Belum ada data', 'bg-slate-100 text-slate-500'],
                    (float) $question['wrong_rate'] >= 50  => ['Prioritas evaluasi', 'bg-red-50 text-red-600'],
                    (float) $question['wrong_rate'] >= 25  => ['Perlu ditinjau', 'bg-orange-50 text-orange-600'],
                    default                                => ['Dipahami baik', 'bg-green-50 text-green-600'],
                };
                ?>
                <article class="question-analysis-row">
                    <div class="flex items-start gap-3"><div class="question-report-rank"><span>#<?= $index + 1 ?></span><small>Soal <?= (int) $question['sort_order'] ?></small></div><div class="min-w-0 flex-1"><div class="flex flex-wrap items-start justify-between gap-2"><div><p class="max-w-3xl text-xs font-semibold leading-relaxed text-slate-700"><?= esc($question['question_text']) ?></p><p class="mt-1 text-[.6rem] text-slate-400"><?= esc($difficultyLabels[$question['difficulty']] ?? $question['difficulty']) ?> &middot; <?= $question['question_type'] === 'TRUE_FALSE' ? 'Benar / Salah' : 'Pilihan Ganda' ?></p></div><span class="rounded-full px-2.5 py-1 text-[.56rem] font-bold <?= esc($evaluationClass) ?>"><?= esc(strtoupper($evaluationLabel)) ?></span></div>
                        <div class="question-answer-chart mt-4" role="img" aria-label="<?= number_format($correctWidth, 1, ',', '.') ?> persen benar, <?= number_format($wrongWidth, 1, ',', '.') ?> persen salah, <?= number_format($unansweredWidth, 1, ',', '.') ?> persen tidak dijawab"><span class="is-correct" style="width: <?= esc((string) $correctWidth, 'attr') ?>%"></span><span class="is-wrong" style="width: <?= esc((string) $wrongWidth, 'attr') ?>%"></span><span class="is-empty" style="width: <?= esc((string) $unansweredWidth, 'attr') ?>%"></span></div>
                        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-[.65rem]"><span class="font-bold text-green-600"><?= (int) $question['correct_count'] ?> benar</span><span class="font-bold text-red-600"><?= (int) $question['wrong_count'] ?> salah</span><span class="font-bold text-slate-400"><?= (int) $question['unanswered_count'] ?> kosong</span><span class="ml-auto text-slate-400">Kesalahan <strong class="text-slate-700"><?= number_format((float) $question['wrong_rate'], 1, ',', '.') ?>%</strong></span></div>
                        <div class="mt-3 rounded-lg bg-green-50/70 px-3 py-2 text-[.65rem]"><span class="font-bold text-green-600">Jawaban benar:</span> <span class="font-medium text-slate-600"><?= esc($question['correct_answer'] ?? '-') ?></span></div>
                    </div></div>
                </article>
            <?php endforeach ?>
        </div>
    </section>

    <p class="mt-4 text-center text-[.62rem] leading-relaxed text-slate-400">Persentase dihitung dari seluruh attempt berstatus selesai. Satu peserta yang mengerjakan lebih dari sekali akan dihitung sesuai jumlah attempt.</p>
</div>
</div>
<?= $this->endSection() ?>
