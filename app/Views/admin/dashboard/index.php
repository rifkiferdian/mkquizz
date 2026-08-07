<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <?php if (session('success')): ?>
        <div class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
            <span><?= esc(session('success')) ?></span>
        </div>
    <?php endif ?>

    <?php if (session('error')): ?>
        <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
            <?= esc(session('error')) ?>
        </div>
    <?php endif ?>

    <section aria-labelledby="summary-title">
        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Overview</p>
                <h2 id="summary-title" class="mt-1 text-lg font-bold text-slate-800">Ringkasan hari ini</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="mr-1 hidden text-xs text-slate-400 lg:inline"><?= esc(date('d M Y')) ?></span>
                <?php if (session('admin_role') !== 'PRESENTER'): ?>
                    <a href="<?= site_url('admin/questions/create') ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[.68rem] font-bold text-slate-600 transition hover:border-orange-200 hover:text-orange-600"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>Pertanyaan</a>
                    <a href="<?= site_url('admin/quizzes/create') ?>" class="inline-flex items-center gap-1.5 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-[.68rem] font-bold text-orange-600 transition hover:bg-orange-100"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>Quiz</a>
                    <a href="<?= site_url('admin/sessions/create') ?>" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-500 px-3 py-2 text-[.68rem] font-bold text-white shadow-md shadow-orange-500/20 transition hover:bg-orange-600"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>Sesi Quiz</a>
                <?php endif ?>
            </div>
        </div>

        <?php
        $cards = [
            ['Quiz', $summary['quizzes'], 'M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z', 'bg-orange-50 text-orange-500'],
            ['Materi Aktif', $summary['materials'], 'M4 19V5m-3 3 3-3 3 3M5 5h10a2 2 0 0 1 2 2v12H7a2 2 0 0 1-2-2V5Z', 'bg-blue-50 text-blue-500'],
            ['Pertanyaan', $summary['questions'], 'M12 18h.01M9.2 9a3 3 0 1 1 4.7 2.5c-1.2.8-1.9 1.2-1.9 2.5M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z', 'bg-violet-50 text-violet-500'],
            ['Total Peserta', $summary['participants'], 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z', 'bg-emerald-50 text-emerald-500'],
        ];
        ?>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <?php foreach ($cards as [$label, $value, $icon, $color]): ?>
                <article class="stat-card rounded-2xl border border-slate-100 bg-white p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-slate-500"><?= esc($label) ?></p>
                            <p class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900"><?= number_format((int) $value, 0, ',', '.') ?></p>
                        </div>
                        <div class="grid size-11 place-items-center rounded-xl <?= esc($color) ?>">
                            <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="<?= esc($icon) ?>"/></svg>
                        </div>
                    </div>
                    <p class="mt-4 text-[.68rem] text-slate-400">Data tersinkronisasi dari sistem</p>
                </article>
            <?php endforeach ?>
        </div>
    </section>

    <section class="mt-7 overflow-hidden rounded-2xl border border-orange-100 bg-gradient-to-br from-white via-white to-orange-50/70 shadow-sm" aria-labelledby="performance-title">
        <div class="flex flex-col gap-2 border-b border-orange-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div><p class="text-[.65rem] font-bold uppercase tracking-[.18em] text-orange-500">Performance</p><h2 id="performance-title" class="mt-1 text-sm font-bold text-slate-800">Insight Pengerjaan</h2></div>
            <p class="text-[.68rem] text-slate-400">Ringkasan dari seluruh hasil yang sudah dikumpulkan</p>
        </div>
        <?php
        $failedAttempts = max(0, $performance['submitted'] - $performance['passed']);
        $performanceCards = [
            ['Attempt Selesai', number_format($performance['submitted'], 0, ',', '.'), 'hasil terkumpul', 'text-blue-600'],
            ['Nilai Rata-rata', number_format($performance['average'], 1, ',', '.'), 'dari skala 100', 'text-violet-600'],
            ['Tingkat Kelulusan', number_format($performance['pass_rate'], 1, ',', '.') . '%', number_format($performance['passed'], 0, ',', '.') . ' peserta lulus', 'text-emerald-600'],
            ['Sesi Dibuka', number_format($performance['open_sessions'], 0, ',', '.'), 'siap menerima peserta', 'text-orange-600'],
        ];
        ?>
        <div class="grid lg:grid-cols-[17rem_minmax(0,1fr)]">
            <div class="flex flex-col items-center justify-center border-b border-orange-100 px-5 py-6 lg:border-b-0 lg:border-r">
                <div class="relative size-36">
                    <svg class="size-full -rotate-90" viewBox="0 0 100 100" role="img" aria-label="Tingkat kelulusan <?= number_format($performance['pass_rate'], 1, ',', '.') ?> persen">
                        <circle cx="50" cy="50" r="39" fill="none" stroke="#ffedd5" stroke-width="11"/>
                        <circle cx="50" cy="50" r="39" fill="none" stroke="#22c55e" stroke-width="11" stroke-linecap="round" pathLength="100" stroke-dasharray="<?= min(100, max(0, $performance['pass_rate'])) ?> 100"/>
                    </svg>
                    <div class="absolute inset-0 grid place-items-center text-center"><div><p class="text-2xl font-black tracking-tight text-slate-800"><?= number_format($performance['pass_rate'], 1, ',', '.') ?>%</p><p class="mt-0.5 text-[.58rem] font-semibold uppercase tracking-wider text-slate-400">Kelulusan</p></div></div>
                </div>
                <div class="mt-4 flex items-center justify-center gap-5 text-[.65rem]"><span class="inline-flex items-center gap-1.5 text-slate-500"><span class="size-2 rounded-full bg-green-500"></span><?= number_format($performance['passed'], 0, ',', '.') ?> Lulus</span><span class="inline-flex items-center gap-1.5 text-slate-500"><span class="size-2 rounded-full bg-orange-200"></span><?= number_format($failedAttempts, 0, ',', '.') ?> Belum</span></div>
            </div>
            <div class="grid divide-y divide-orange-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                <?php foreach ($performanceCards as $index => [$label, $value, $caption, $color]): ?>
                    <article class="px-5 py-5 <?= $index > 1 ? 'sm:border-t sm:border-orange-100' : '' ?>"><p class="text-[.68rem] font-medium text-slate-400"><?= esc($label) ?></p><p class="mt-2 text-2xl font-extrabold tracking-tight <?= esc($color) ?>"><?= esc($value) ?></p><p class="mt-1 text-[.62rem] text-slate-400"><?= esc($caption) ?></p></article>
                <?php endforeach ?>
            </div>
        </div>
    </section>

    <div class="mt-7 grid gap-6 xl:grid-cols-[1.45fr_1fr]">
        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-title">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 id="quiz-title" class="text-sm font-bold text-slate-800">Quiz Terbaru</h2>
                    <p class="mt-1 text-[.68rem] text-slate-400">Daftar quiz yang baru dibuat</p>
                </div>
                <a href="<?= site_url('admin/quizzes') ?>" class="rounded-lg bg-orange-50 px-3 py-1.5 text-[.68rem] font-bold text-orange-600 transition hover:bg-orange-100">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[36rem] text-left">
                    <thead class="bg-slate-50/70 text-[.65rem] uppercase tracking-wider text-slate-400">
                        <tr><th class="px-5 py-3 font-semibold">Nama Quiz</th><th class="px-5 py-3 font-semibold">Durasi</th><th class="px-5 py-3 font-semibold">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                    <?php if ($recentQuizzes === []): ?>
                        <tr><td colspan="3" class="px-5 py-10 text-center text-slate-400">Belum ada quiz.</td></tr>
                    <?php endif ?>
                    <?php foreach ($recentQuizzes as $quiz): ?>
                        <tr class="hover:bg-orange-50/30">
                            <td class="px-5 py-4"><a href="<?= site_url('admin/quizzes/' . $quiz['id']) ?>" class="font-bold text-slate-700 transition hover:text-orange-600"><?= esc($quiz['title']) ?></a><p class="mt-1 text-[.68rem] text-slate-400"><?= esc($quiz['material_title']) ?></p></td>
                            <td class="px-5 py-4 text-slate-500"><?= (int) $quiz['duration_minutes'] ?> menit</td>
                            <td class="px-5 py-4"><span class="rounded-full <?= $quiz['status'] === 'ACTIVE' ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' ?> px-2.5 py-1 text-[.62rem] font-bold"><?= esc($quiz['status']) ?></span></td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="session-title">
            <div class="flex items-center justify-between">
                <div><h2 id="session-title" class="text-sm font-bold text-slate-800">Sesi Berlangsung</h2><p class="mt-1 text-[.68rem] text-slate-400">Sesi yang siap menerima peserta</p></div>
                <span class="status-dot size-2 rounded-full bg-green-500"></span>
            </div>

            <div class="mt-5 space-y-3">
                <?php if ($activeSessions === []): ?>
                    <div class="rounded-xl border border-dashed border-slate-200 p-8 text-center text-xs text-slate-400">Tidak ada sesi aktif.</div>
                <?php endif ?>
                <?php foreach ($activeSessions as $quizSession): ?>
                    <article class="rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0"><a href="<?= site_url('admin/sessions/' . $quizSession['id']) ?>" class="truncate text-xs font-bold text-slate-700 transition hover:text-orange-600"><?= esc($quizSession['session_name']) ?></a><p class="mt-1 truncate text-[.68rem] text-slate-400"><?= esc($quizSession['quiz_title']) ?></p></div>
                            <span class="rounded-full bg-green-50 px-2 py-1 text-[.58rem] font-bold text-green-600"><?= esc($quizSession['status']) ?></span>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-200/70 pt-3">
                            <p class="text-[.68rem] text-slate-400"><span class="font-bold text-slate-600"><?= (int) $quizSession['participant_count'] ?></span> peserta</p>
                            <p class="font-mono text-xs font-bold tracking-[.18em] text-orange-600">PIN <?= esc($quizSession['pin']) ?></p>
                        </div>
                    </article>
                <?php endforeach ?>
            </div>
        </section>
    </div>

    <section class="mt-7 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="evaluation-title">
        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"><div><h2 id="evaluation-title" class="text-sm font-bold text-slate-800">Soal Perlu Evaluasi</h2><p class="mt-1 text-[.68rem] text-slate-400">Pertanyaan dengan persentase jawaban benar paling rendah</p></div><a href="<?= site_url('admin/questions') ?>" class="w-fit rounded-lg bg-red-50 px-3 py-1.5 text-[.65rem] font-bold text-red-500 transition hover:bg-red-100">Buka Bank Soal</a></div>
        <div class="grid divide-y divide-slate-100 lg:grid-cols-2 lg:divide-x lg:divide-y-0">
            <?php if ($difficultQuestions === []): ?><div class="col-span-2 px-5 py-10 text-center text-xs text-slate-400">Data jawaban belum cukup untuk evaluasi soal.</div><?php endif ?>
            <?php foreach ($difficultQuestions as $index => $question): ?>
                <article class="flex items-start gap-3 px-5 py-4 <?= $index > 1 ? 'border-t border-slate-100' : '' ?>"><span class="grid size-8 shrink-0 place-items-center rounded-lg bg-red-50 text-[.68rem] font-extrabold text-red-500"><?= $index + 1 ?></span><div class="min-w-0 flex-1"><p class="line-clamp-2 text-xs font-semibold leading-relaxed text-slate-700"><?= esc($question['question_text']) ?></p><p class="mt-1 text-[.62rem] text-slate-400"><?= esc($question['material_title']) ?> · <?= (int) $question['response_count'] ?> jawaban</p></div><div class="shrink-0 text-right"><p class="text-lg font-extrabold text-red-500"><?= number_format((float) $question['accuracy'], 0, ',', '.') ?>%</p><p class="text-[.55rem] uppercase text-slate-400">benar</p></div></article>
            <?php endforeach ?>
        </div>
    </section>
</div>
</div>
<?= $this->endSection() ?>
