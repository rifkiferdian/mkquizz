<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$pageUrl = static function (int $targetPage) use ($filters): string {
    $parameters = array_filter([
        'q'           => $filters['search'],
        'material_id' => $filters['material_id'] ?: '',
        'status'      => $filters['status'],
        'page'        => $targetPage,
    ], static fn ($value): bool => $value !== '');

    return site_url('admin/quizzes') . '?' . http_build_query($parameters);
};

$statusMeta = [
    'ACTIVE'   => ['Aktif', 'bg-green-50 text-green-600 border-green-100'],
    'DRAFT'    => ['Draft', 'bg-orange-50 text-orange-600 border-orange-100'],
    'INACTIVE' => ['Nonaktif', 'bg-slate-100 text-slate-500 border-slate-200'],
];
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Quiz Management</p>
            <h2 class="mt-1 text-lg font-bold text-slate-800">Daftar Quiz</h2>
            <p class="mt-1 text-xs text-slate-400">Pantau konfigurasi, konten, sesi, dan aktivitas setiap quiz.</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-xl border border-orange-100 bg-white px-4 py-3 text-xs text-slate-500 shadow-sm">
            <span class="status-dot size-2 rounded-full bg-green-500"></span>
            <span><strong class="text-slate-700"><?= $summary['active'] ?></strong> quiz sedang aktif</span>
        </div>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan quiz">
        <?php foreach ([['Total Quiz', $summary['total'], 'bg-blue-50 text-blue-500'], ['Quiz Aktif', $summary['active'], 'bg-green-50 text-green-600'], ['Draft', $summary['draft'], 'bg-orange-50 text-orange-600'], ['Nonaktif', $summary['inactive'], 'bg-slate-100 text-slate-500']] as [$label, $value, $color]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5">
                <div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= number_format((int) $value, 0, ',', '.') ?></p></div>
                <div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg></div>
            </article>
        <?php endforeach ?>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-list-title">
        <div class="question-filter-panel">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="grid size-8 place-items-center rounded-lg bg-orange-100 text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16M7 12h10M10 19h4"/></svg></div>
                    <div><p class="text-xs font-bold text-slate-700">Filter Quiz</p><p class="mt-0.5 text-[.65rem] text-slate-400">Cari quiz berdasarkan nama, material, atau status</p></div>
                </div>
                <span class="rounded-full border border-orange-100 bg-white px-3 py-1.5 text-[.65rem] font-semibold text-slate-500"><strong class="text-orange-600"><?= $totalResult ?></strong> hasil</span>
            </div>

            <form action="<?= site_url('admin/quizzes') ?>" method="get" class="quiz-filter-grid">
                <div>
                    <label for="quiz-search" class="material-filter-label">Cari Quiz</label>
                    <div class="relative"><svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input id="quiz-search" type="search" name="q" value="<?= esc($filters['search']) ?>" class="material-filter-input pl-10" placeholder="Cari nama atau deskripsi quiz..."></div>
                </div>
                <div>
                    <label for="quiz-material" class="material-filter-label">Material</label>
                    <select id="quiz-material" name="material_id" class="material-filter-input material-filter-select"><option value="">Semua material</option><?php foreach ($materials as $material): ?><option value="<?= $material['id'] ?>" <?= $filters['material_id'] === (int) $material['id'] ? 'selected' : '' ?>><?= esc($material['title']) ?></option><?php endforeach ?></select>
                </div>
                <div>
                    <label for="quiz-status" class="material-filter-label">Status</label>
                    <select id="quiz-status" name="status" class="material-filter-input material-filter-select"><option value="">Semua status</option><option value="ACTIVE" <?= $filters['status'] === 'ACTIVE' ? 'selected' : '' ?>>Aktif</option><option value="DRAFT" <?= $filters['status'] === 'DRAFT' ? 'selected' : '' ?>>Draft</option><option value="INACTIVE" <?= $filters['status'] === 'INACTIVE' ? 'selected' : '' ?>>Nonaktif</option></select>
                </div>
                <button type="submit" class="material-filter-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z"/></svg>Terapkan</button>
                <?php if ($filters['search'] !== '' || $filters['material_id'] > 0 || $filters['status'] !== ''): ?><a href="<?= site_url('admin/quizzes') ?>" class="material-reset-button">Reset</a><?php endif ?>
            </form>
        </div>

        <h3 id="quiz-list-title" class="sr-only">Tabel daftar quiz</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[68rem] text-left">
                <thead class="bg-slate-50/70 text-[.65rem] uppercase tracking-wider text-slate-400">
                    <tr><th class="px-5 py-3 font-semibold">Quiz</th><th class="px-5 py-3 font-semibold">Material</th><th class="px-5 py-3 font-semibold">Konten</th><th class="px-5 py-3 font-semibold">Pengaturan</th><th class="px-5 py-3 font-semibold">Aktivitas</th><th class="px-5 py-3 font-semibold">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                <?php if ($quizzes === []): ?>
                    <tr><td colspan="6" class="px-5 py-14 text-center"><div class="mx-auto grid size-12 place-items-center rounded-2xl bg-orange-50 text-orange-500"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg></div><p class="mt-3 font-semibold text-slate-500">Quiz tidak ditemukan</p><p class="mt-1 text-[.68rem] text-slate-400">Coba ubah kata kunci atau filter yang digunakan.</p></td></tr>
                <?php endif ?>
                <?php foreach ($quizzes as $quiz): ?>
                    <?php [$statusLabel, $statusClass] = $statusMeta[$quiz['status']] ?? [$quiz['status'], 'bg-slate-100 text-slate-500 border-slate-200']; ?>
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <div class="quiz-list-icon"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg></div>
                                <div><a href="<?= site_url('admin/quizzes/' . $quiz['id']) ?>" class="max-w-xs font-bold leading-relaxed text-slate-700 transition hover:text-orange-600"><?= esc($quiz['title']) ?></a><p class="mt-1 max-w-xs truncate text-[.65rem] text-slate-400"><?= esc($quiz['description'] ?: 'Tidak ada deskripsi') ?></p><p class="mt-1 text-[.6rem] text-slate-400">Diperbarui <?= esc(date('d M Y', strtotime($quiz['updated_at']))) ?></p></div>
                            </div>
                        </td>
                        <td class="px-5 py-4"><p class="max-w-40 truncate font-medium text-slate-600"><?= esc($quiz['material_title']) ?></p><p class="mt-1 font-mono text-[.62rem] text-slate-400"><?= esc($quiz['material_code'] ?: '-') ?></p></td>
                        <td class="px-5 py-4"><div class="space-y-2"><p class="flex items-center gap-2 text-slate-500"><span class="grid size-6 place-items-center rounded-md bg-orange-50 font-bold text-orange-600"><?= (int) $quiz['question_count'] ?></span> pertanyaan</p><p class="flex items-center gap-2 text-slate-500"><span class="grid size-6 place-items-center rounded-md bg-blue-50 font-bold text-blue-500"><?= (int) $quiz['session_count'] ?></span> sesi</p></div></td>
                        <td class="px-5 py-4"><p class="font-semibold text-slate-600"><?= (int) $quiz['duration_minutes'] ?> menit</p><p class="mt-1 text-[.65rem] text-slate-400">Lulus ≥ <?= number_format((float) $quiz['passing_score'], 0, ',', '.') ?></p><div class="mt-2 flex gap-1"><?php if ($quiz['shuffle_questions']): ?><span class="rounded bg-violet-50 px-1.5 py-1 text-[.55rem] font-bold text-violet-500">ACAK SOAL</span><?php endif ?><?php if ($quiz['shuffle_options']): ?><span class="rounded bg-violet-50 px-1.5 py-1 text-[.55rem] font-bold text-violet-500">ACAK OPSI</span><?php endif ?></div></td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-700"><?= (int) $quiz['attempt_count'] ?> pengerjaan</p><p class="mt-1 text-[.65rem] text-slate-400">Rata-rata: <span class="font-semibold text-slate-600"><?= $quiz['average_score'] !== null ? number_format((float) $quiz['average_score'], 1, ',', '.') : '-' ?></span></p><?php if ((int) $quiz['active_session_count'] > 0): ?><p class="mt-2 inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2 py-1 text-[.58rem] font-bold text-green-600"><span class="size-1.5 rounded-full bg-green-500"></span><?= (int) $quiz['active_session_count'] ?> sesi aktif</p><?php endif ?></td>
                        <td class="px-5 py-4"><span class="inline-flex rounded-full border px-2.5 py-1 text-[.62rem] font-bold <?= esc($statusClass) ?>"><?= esc(strtoupper($statusLabel)) ?></span><p class="mt-2 max-w-28 truncate text-[.6rem] text-slate-400">oleh <?= esc($quiz['creator_name'] ?? '-') ?></p></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 text-xs sm:flex-row sm:items-center sm:justify-between">
            <p class="text-slate-400">Menampilkan <span class="font-semibold text-slate-600"><?= count($quizzes) ?></span> dari <span class="font-semibold text-slate-600"><?= $totalResult ?></span> quiz</p>
            <?php if ($totalPages > 1): ?><nav class="flex items-center gap-2" aria-label="Pagination quiz"><a href="<?= $pageUrl(max(1, $page - 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === 1 ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Sebelumnya</a><span class="px-2 text-slate-400">Halaman <?= $page ?> / <?= $totalPages ?></span><a href="<?= $pageUrl(min($totalPages, $page + 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === $totalPages ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Berikutnya</a></nav><?php endif ?>
        </div>
    </section>
</div>
</div>
<?= $this->endSection() ?>
