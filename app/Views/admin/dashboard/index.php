<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <?php if (session('success')): ?>
        <div class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
            <span><?= esc(session('success')) ?></span>
        </div>
    <?php endif ?>

    <section aria-labelledby="summary-title">
        <div class="mb-5 flex items-end justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Overview</p>
                <h2 id="summary-title" class="mt-1 text-lg font-bold text-slate-800">Ringkasan hari ini</h2>
            </div>
            <p class="hidden text-xs text-slate-400 sm:block"><?= esc(date('d M Y')) ?></p>
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

    <div class="mt-7 grid gap-6 xl:grid-cols-[1.45fr_1fr]">
        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-title">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 id="quiz-title" class="text-sm font-bold text-slate-800">Quiz Terbaru</h2>
                    <p class="mt-1 text-[.68rem] text-slate-400">Daftar quiz yang baru dibuat</p>
                </div>
                <span class="rounded-lg bg-orange-50 px-3 py-1.5 text-[.68rem] font-bold text-orange-600">Lihat semua</span>
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
                            <td class="px-5 py-4"><p class="font-bold text-slate-700"><?= esc($quiz['title']) ?></p><p class="mt-1 text-[.68rem] text-slate-400"><?= esc($quiz['material_title']) ?></p></td>
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
                            <div class="min-w-0"><p class="truncate text-xs font-bold text-slate-700"><?= esc($quizSession['session_name']) ?></p><p class="mt-1 truncate text-[.68rem] text-slate-400"><?= esc($quizSession['quiz_title']) ?></p></div>
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
</div>
</div>
<?= $this->endSection() ?>
