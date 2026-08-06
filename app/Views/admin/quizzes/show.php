<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$statusMeta = [
    'ACTIVE'   => ['Aktif', 'bg-green-50 text-green-600 border-green-100', 'bg-green-500'],
    'DRAFT'    => ['Draft', 'bg-orange-50 text-orange-600 border-orange-100', 'bg-orange-500'],
    'INACTIVE' => ['Nonaktif', 'bg-slate-100 text-slate-500 border-slate-200', 'bg-slate-400'],
];
[$statusLabel, $statusClass, $statusDot] = $statusMeta[$quiz['status']] ?? [$quiz['status'], 'bg-slate-100 text-slate-500 border-slate-200', 'bg-slate-400'];

$difficultyMeta = [
    'EASY'   => ['Mudah', 'bg-green-50 text-green-600'],
    'MEDIUM' => ['Sedang', 'bg-orange-50 text-orange-600'],
    'HARD'   => ['Sulit', 'bg-red-50 text-red-600'],
];

$sessionStatusMeta = [
    'OPEN'    => 'bg-green-50 text-green-600',
    'WAITING' => 'bg-orange-50 text-orange-600',
    'CLOSED'  => 'bg-slate-100 text-slate-500',
    'DRAFT'   => 'bg-blue-50 text-blue-500',
];

$settings = [
    ['Acak urutan pertanyaan', (bool) $quiz['shuffle_questions']],
    ['Acak pilihan jawaban', (bool) $quiz['shuffle_options']],
    ['Tampilkan nilai', (bool) $quiz['show_score']],
    ['Tampilkan jawaban benar', (bool) $quiz['show_correct_answer']],
    ['Tampilkan penjelasan', (bool) $quiz['show_explanation']],
    ['Izinkan review jawaban', (bool) $quiz['allow_review']],
];
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <div class="flex flex-wrap items-center justify-between gap-3"><a href="<?= site_url('admin/quizzes') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Kembali ke Daftar Quiz</a><?php if (session('admin_role') !== 'PRESENTER' && $quiz['status'] === 'ACTIVE'): ?><a href="<?= site_url('admin/sessions/create?quiz_id=' . $quiz['id']) ?>" class="inline-flex items-center gap-2 rounded-xl bg-orange-500 px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>Buat Sesi</a><?php endif ?></div>

    <?php if (session('success')): ?><div class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status"><?= esc(session('success')) ?></div><?php endif ?>

    <section class="quiz-detail-hero mt-5" aria-labelledby="quiz-detail-title">
        <div class="relative z-1">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="grid size-14 shrink-0 place-items-center rounded-2xl bg-orange-500 text-white shadow-lg shadow-orange-500/20"><svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg></div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[.62rem] font-bold <?= esc($statusClass) ?>"><span class="size-1.5 rounded-full <?= esc($statusDot) ?>"></span><?= esc(strtoupper($statusLabel)) ?></span><span class="rounded-full border border-orange-100 bg-orange-50 px-2.5 py-1 text-[.62rem] font-bold text-orange-600"><?= esc($quiz['material_code'] ?: 'MATERIAL') ?></span></div>
                        <h2 id="quiz-detail-title" class="mt-3 max-w-3xl text-2xl font-extrabold tracking-tight text-slate-900"><?= esc($quiz['title']) ?></h2>
                        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-slate-500"><?= esc($quiz['description'] ?: 'Quiz ini belum memiliki deskripsi.') ?></p>
                    </div>
                </div>
                <div class="shrink-0 rounded-xl border border-orange-100 bg-white/80 px-4 py-3 text-xs"><p class="text-slate-400">Dibuat oleh</p><p class="mt-1 font-bold text-slate-700"><?= esc($quiz['creator_name'] ?? '-') ?></p><p class="mt-1 text-[.65rem] text-slate-400"><?= esc(date('d M Y, H:i', strtotime($quiz['created_at']))) ?></p></div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan detail quiz">
        <?php foreach ([
            ['Pertanyaan', (int) $quiz['question_count'], 'soal', 'bg-orange-50 text-orange-600', 'M12 18h.01M9.2 9a3 3 0 1 1 4.7 2.5c-1.2.8-1.9 1.2-1.9 2.5M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z'],
            ['Durasi', (int) $quiz['duration_minutes'], 'menit', 'bg-blue-50 text-blue-500', 'M12 7v5l3 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z'],
            ['Passing Grade', number_format((float) $quiz['passing_score'], 0, ',', '.'), 'nilai minimum', 'bg-violet-50 text-violet-500', 'm5 12 4 4L19 6'],
            ['Pengerjaan', (int) $performance['total'], 'attempt', 'bg-green-50 text-green-600', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
        ] as [$label, $value, $unit, $color, $icon]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5"><div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= esc((string) $value) ?></p><p class="mt-1 text-[.62rem] text-slate-400"><?= esc($unit) ?></p></div><div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="<?= esc($icon) ?>"/></svg></div></article>
        <?php endforeach ?>
    </section>

    <div class="mt-6 grid items-start gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(20rem,.7fr)]">
        <div class="space-y-6">
            <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-questions-title">
                <div class="flex items-center justify-between border-b border-orange-100 bg-gradient-to-r from-white to-orange-50 px-5 py-4"><div><h3 id="quiz-questions-title" class="text-sm font-bold text-slate-800">Daftar Pertanyaan</h3><p class="mt-1 text-[.68rem] text-slate-400">Urutan soal dan bobot nilai pada quiz</p></div><span class="rounded-full bg-orange-100 px-3 py-1.5 text-[.65rem] font-bold text-orange-600"><?= count($questions) ?> soal</span></div>
                <div class="divide-y divide-slate-100">
                    <?php if ($questions === []): ?><div class="px-5 py-12 text-center text-xs text-slate-400">Belum ada pertanyaan yang ditambahkan ke quiz.</div><?php endif ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <?php [$difficultyLabel, $difficultyClass] = $difficultyMeta[$question['difficulty']] ?? ['-', 'bg-slate-100 text-slate-500']; ?>
                        <article class="quiz-question-item">
                            <div class="grid size-9 shrink-0 place-items-center rounded-xl bg-orange-50 text-xs font-extrabold text-orange-600"><?= $index + 1 ?></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold leading-relaxed text-slate-700"><?= esc($question['question_text']) ?></p>
                                <div class="mt-2 flex flex-wrap items-center gap-2"><span class="rounded-full <?= esc($difficultyClass) ?> px-2 py-1 text-[.58rem] font-bold"><?= esc(strtoupper($difficultyLabel)) ?></span><span class="text-[.62rem] text-slate-400"><?= $question['question_type'] === 'TRUE_FALSE' ? 'Benar / Salah' : 'Pilihan Ganda' ?> • <?= (int) $question['option_count'] ?> opsi</span><?php if (! $question['is_active']): ?><span class="rounded-full bg-red-50 px-2 py-1 text-[.58rem] font-bold text-red-600">NONAKTIF</span><?php endif ?></div>
                                <div class="mt-3 rounded-lg border border-green-100 bg-green-50/70 px-3 py-2 text-[.68rem] text-green-700"><span class="font-bold">Jawaban:</span> <?= esc($question['correct_answer'] ?? '-') ?></div>
                            </div>
                            <div class="shrink-0 text-right"><p class="text-sm font-extrabold text-slate-700"><?= number_format((float) $question['score'], 0, ',', '.') ?></p><p class="text-[.58rem] uppercase text-slate-400">poin</p></div>
                        </article>
                    <?php endforeach ?>
                </div>
                <?php if ($questions !== []): ?><div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/60 px-5 py-4 text-xs"><span class="text-slate-400">Total bobot nilai</span><span class="font-extrabold text-orange-600"><?= number_format(array_sum(array_map(static fn ($item): float => (float) $item['score'], $questions)), 0, ',', '.') ?> poin</span></div><?php endif ?>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-sessions-title">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div><h3 id="quiz-sessions-title" class="text-sm font-bold text-slate-800">Riwayat Sesi</h3><p class="mt-1 text-[.68rem] text-slate-400">Sesi terbaru yang menggunakan quiz ini</p></div><span class="text-[.65rem] font-semibold text-slate-400"><?= (int) $quiz['session_count'] ?> total sesi</span></div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[38rem] text-left"><thead class="bg-slate-50/70 text-[.62rem] uppercase tracking-wider text-slate-400"><tr><th class="px-5 py-3 font-semibold">Nama Sesi</th><th class="px-5 py-3 font-semibold">Peserta</th><th class="px-5 py-3 font-semibold">Rata-rata</th><th class="px-5 py-3 font-semibold">Status</th></tr></thead><tbody class="divide-y divide-slate-100 text-xs">
                    <?php if ($sessions === []): ?><tr><td colspan="4" class="px-5 py-10 text-center text-slate-400">Belum ada sesi quiz.</td></tr><?php endif ?>
                    <?php foreach ($sessions as $quizSession): ?><tr><td class="px-5 py-4"><p class="font-bold text-slate-700"><?= esc($quizSession['session_name']) ?></p><p class="mt-1 font-mono text-[.62rem] text-orange-600">PIN <?= esc($quizSession['pin']) ?></p></td><td class="px-5 py-4 text-slate-500"><span class="font-bold text-slate-700"><?= (int) $quizSession['participant_count'] ?></span> peserta<p class="mt-1 text-[.6rem] text-slate-400"><?= (int) $quizSession['attempt_count'] ?> pengerjaan</p></td><td class="px-5 py-4 font-bold text-slate-600"><?= $quizSession['average_score'] !== null ? number_format((float) $quizSession['average_score'], 1, ',', '.') : '-' ?></td><td class="px-5 py-4"><span class="rounded-full <?= esc($sessionStatusMeta[$quizSession['status']] ?? 'bg-slate-100 text-slate-500') ?> px-2 py-1 text-[.58rem] font-bold"><?= esc($quizSession['status']) ?></span></td></tr><?php endforeach ?>
                    </tbody></table>
                </div>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="performance-title">
                <div class="flex items-center justify-between"><div><h3 id="performance-title" class="text-sm font-bold text-slate-800">Performa Peserta</h3><p class="mt-1 text-[.68rem] text-slate-400">Berdasarkan jawaban terkirim</p></div><div class="grid size-9 place-items-center rounded-xl bg-green-50 text-green-600"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V9m6 10V5m6 14v-7m4 7H2"/></svg></div></div>
                <div class="mt-5 rounded-xl bg-gradient-to-br from-orange-50 to-white p-4 text-center"><p class="text-[.65rem] font-semibold uppercase tracking-wider text-slate-400">Nilai Rata-rata</p><p class="mt-2 text-4xl font-extrabold text-orange-600"><?= number_format((float) $performance['average'], 1, ',', '.') ?></p><p class="mt-1 text-[.65rem] text-slate-400">dari <?= $performance['submitted'] ?> pengerjaan selesai</p></div>
                <div class="mt-4 grid grid-cols-3 divide-x divide-slate-100 text-center"><div class="px-2"><p class="text-lg font-extrabold text-green-600"><?= number_format((float) $performance['pass_rate'], 0, ',', '.') ?>%</p><p class="mt-1 text-[.58rem] text-slate-400">Kelulusan</p></div><div class="px-2"><p class="text-lg font-extrabold text-slate-700"><?= number_format((float) $performance['highest'], 0, ',', '.') ?></p><p class="mt-1 text-[.58rem] text-slate-400">Tertinggi</p></div><div class="px-2"><p class="text-lg font-extrabold text-slate-700"><?= number_format((float) $performance['lowest'], 0, ',', '.') ?></p><p class="mt-1 text-[.58rem] text-slate-400">Terendah</p></div></div>
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="settings-title">
                <div><h3 id="settings-title" class="text-sm font-bold text-slate-800">Pengaturan Quiz</h3><p class="mt-1 text-[.68rem] text-slate-400">Konfigurasi hasil dan pengerjaan</p></div>
                <div class="mt-4 divide-y divide-slate-100">
                    <?php foreach ($settings as [$settingLabel, $enabled]): ?><div class="flex items-center justify-between py-3"><span class="text-xs text-slate-500"><?= esc($settingLabel) ?></span><span class="grid size-6 place-items-center rounded-full <?= $enabled ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-400' ?>"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><?= $enabled ? '<path d="m5 12 4 4L19 6"/>' : '<path d="m6 6 12 12M18 6 6 18"/>' ?></svg></span></div><?php endforeach ?>
                </div>
            </section>

            <section class="rounded-2xl border border-orange-100 bg-orange-50/70 p-5"><p class="text-xs font-bold text-orange-700">Informasi Material</p><p class="mt-2 text-sm font-bold text-slate-700"><?= esc($quiz['material_title']) ?></p><p class="mt-1 font-mono text-[.65rem] text-orange-600"><?= esc($quiz['material_code'] ?: 'Tanpa kode') ?></p><p class="mt-3 text-[.65rem] leading-relaxed text-slate-500">Seluruh pertanyaan pada quiz ini berasal dari bank soal yang terhubung ke material tersebut.</p></section>
        </aside>
    </div>
</div>
</div>
<?= $this->endSection() ?>
