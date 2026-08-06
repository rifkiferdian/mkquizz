<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$pageUrl = static function (int $targetPage) use ($filters): string {
    $parameters = array_filter([
        'q'       => $filters['search'],
        'quiz_id' => $filters['quiz_id'] ?: '',
        'status'  => $filters['status'],
        'page'    => $targetPage,
    ], static fn ($value): bool => $value !== '');

    return site_url('admin/sessions') . '?' . http_build_query($parameters);
};

$statusUrl = static function (string $targetStatus) use ($filters): string {
    $parameters = array_filter([
        'q'       => $filters['search'],
        'quiz_id' => $filters['quiz_id'] ?: '',
        'status'  => $targetStatus,
    ], static fn ($value): bool => $value !== '');

    $query = http_build_query($parameters);

    return site_url('admin/sessions') . ($query !== '' ? '?' . $query : '');
};

$statusMeta = [
    'OPEN'    => ['Dibuka', 'bg-green-50 text-green-600 border-green-100', 'bg-green-500'],
    'WAITING' => ['Menunggu', 'bg-orange-50 text-orange-600 border-orange-100', 'bg-orange-500'],
    'CLOSED'  => ['Ditutup', 'bg-slate-100 text-slate-500 border-slate-200', 'bg-slate-400'],
    'DRAFT'   => ['Draft', 'bg-blue-50 text-blue-500 border-blue-100', 'bg-blue-500'],
];
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Live Quiz Management</p><h2 class="mt-1 text-lg font-bold text-slate-800">Daftar Sesi Quiz</h2><p class="mt-1 text-xs text-slate-400">Pantau akses PIN, peserta, dan aktivitas setiap sesi.</p></div>
        <div class="inline-flex items-center gap-2 rounded-xl border border-green-100 bg-white px-4 py-3 text-xs text-slate-500 shadow-sm"><span class="status-dot size-2 rounded-full bg-green-500"></span><span><strong class="text-slate-700"><?= $summary['open'] ?></strong> sesi sedang dibuka</span></div>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan sesi quiz">
        <?php foreach ([['Total Sesi', $summary['total'], 'bg-blue-50 text-blue-500'], ['Sedang Dibuka', $summary['open'], 'bg-green-50 text-green-600'], ['Menunggu', $summary['waiting'], 'bg-orange-50 text-orange-600'], ['Sudah Ditutup', $summary['closed'], 'bg-slate-100 text-slate-500']] as [$label, $value, $color]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5"><div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= number_format((int) $value, 0, ',', '.') ?></p></div><div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg></div></article>
        <?php endforeach ?>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="session-list-title">
        <div class="session-filter-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="grid size-9 place-items-center rounded-xl bg-orange-100 text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16M7 12h10M10 19h4"/></svg></div>
                    <div><p class="text-xs font-bold text-slate-700">Filter Sesi Quiz</p><p class="mt-0.5 text-[.65rem] text-slate-400">Pilih status lalu cari sesi atau quiz yang diperlukan</p></div>
                </div>
                <div class="flex items-center gap-2"><span class="rounded-full border border-orange-100 bg-white px-3 py-1.5 text-[.65rem] font-semibold text-slate-500"><strong class="text-orange-600"><?= $totalResult ?></strong> hasil ditemukan</span></div>
            </div>

            <nav class="session-status-tabs mt-4" aria-label="Filter status sesi">
                <?php foreach (['' => 'Semua', 'OPEN' => 'Dibuka', 'WAITING' => 'Menunggu', 'CLOSED' => 'Ditutup', 'DRAFT' => 'Draft'] as $statusValue => $statusName): ?>
                    <a href="<?= $statusUrl($statusValue) ?>" class="session-status-tab <?= $filters['status'] === $statusValue ? 'active' : '' ?>" <?= $filters['status'] === $statusValue ? 'aria-current="page"' : '' ?>>
                        <?php if ($statusValue !== ''): ?><span class="session-tab-dot <?= $statusValue === 'OPEN' ? 'bg-green-500' : ($statusValue === 'WAITING' ? 'bg-orange-500' : ($statusValue === 'DRAFT' ? 'bg-blue-500' : 'bg-slate-400')) ?>"></span><?php endif ?>
                        <?= esc($statusName) ?>
                    </a>
                <?php endforeach ?>
            </nav>

            <form action="<?= site_url('admin/sessions') ?>" method="get" class="session-filter-grid">
                <?php if ($filters['status'] !== ''): ?><input type="hidden" name="status" value="<?= esc($filters['status'], 'attr') ?>"><?php endif ?>
                <div><label for="session-search" class="material-filter-label">Cari Sesi</label><div class="relative"><svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input id="session-search" type="search" name="q" value="<?= esc($filters['search']) ?>" class="material-filter-input pl-10" placeholder="Nama sesi, PIN, atau token..."></div></div>
                <div><label for="session-quiz" class="material-filter-label">Quiz</label><select id="session-quiz" name="quiz_id" class="material-filter-input material-filter-select"><option value="">Semua quiz</option><?php foreach ($quizzes as $quiz): ?><option value="<?= $quiz['id'] ?>" <?= $filters['quiz_id'] === (int) $quiz['id'] ? 'selected' : '' ?>><?= esc($quiz['title']) ?></option><?php endforeach ?></select></div>
                <button type="submit" class="material-filter-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>Cari Sesi</button>
                <?php if ($filters['search'] !== '' || $filters['quiz_id'] > 0 || $filters['status'] !== ''): ?><a href="<?= site_url('admin/sessions') ?>" class="material-reset-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7L3 8m0-5v5h5"/></svg>Reset</a><?php endif ?>
            </form>
        </div>

        <h3 id="session-list-title" class="sr-only">Tabel daftar sesi quiz</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[68rem] text-left">
                <thead class="bg-slate-50/70 text-[.65rem] uppercase tracking-wider text-slate-400"><tr><th class="px-5 py-3 font-semibold">Sesi</th><th class="px-5 py-3 font-semibold">Quiz</th><th class="px-5 py-3 font-semibold">Akses</th><th class="px-5 py-3 font-semibold">Peserta</th><th class="px-5 py-3 font-semibold">Performa</th><th class="px-5 py-3 font-semibold">Status</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                <?php if ($sessions === []): ?><tr><td colspan="6" class="px-5 py-14 text-center"><div class="mx-auto grid size-12 place-items-center rounded-2xl bg-orange-50 text-orange-500"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg></div><p class="mt-3 font-semibold text-slate-500">Sesi tidak ditemukan</p><p class="mt-1 text-[.68rem] text-slate-400">Coba ubah kata kunci atau filter.</p></td></tr><?php endif ?>
                <?php foreach ($sessions as $quizSession): ?>
                    <?php [$statusLabel, $statusClass, $statusDot] = $statusMeta[$quizSession['status']] ?? [$quizSession['status'], 'bg-slate-100 text-slate-500 border-slate-200', 'bg-slate-400']; ?>
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-4"><div class="flex items-start gap-3"><div class="session-list-icon"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg></div><div><a href="<?= site_url('admin/sessions/' . $quizSession['id']) ?>" class="max-w-xs font-bold leading-relaxed text-slate-700 transition hover:text-orange-600"><?= esc($quizSession['session_name']) ?></a><p class="mt-1 font-mono text-[.62rem] text-slate-400"><?= esc($quizSession['session_token']) ?></p><p class="mt-1 text-[.6rem] text-slate-400">oleh <?= esc($quizSession['creator_name'] ?? '-') ?></p></div></div></td>
                        <td class="px-5 py-4"><a href="<?= site_url('admin/quizzes/' . $quizSession['quiz_id']) ?>" class="block max-w-44 truncate font-semibold text-slate-600 hover:text-orange-600"><?= esc($quizSession['quiz_title']) ?></a><p class="mt-1 max-w-40 truncate text-[.62rem] text-slate-400"><?= esc($quizSession['material_title']) ?></p><p class="mt-1 text-[.6rem] text-slate-400"><?= (int) $quizSession['duration_minutes'] ?> menit • lulus <?= number_format((float) $quizSession['passing_score'], 0, ',', '.') ?></p></td>
                        <td class="px-5 py-4"><div class="inline-flex items-center gap-2 rounded-xl border border-orange-100 bg-orange-50 px-3 py-2"><span class="text-[.58rem] font-bold uppercase text-orange-500">PIN</span><span class="font-mono text-sm font-extrabold tracking-[.16em] text-orange-700"><?= esc($quizSession['pin']) ?></span></div><p class="mt-2 text-[.6rem] <?= $quizSession['pin_expired'] ? 'font-semibold text-red-500' : 'text-slate-400' ?>"><?= $quizSession['pin_expired'] ? 'PIN kedaluwarsa' : 'Berlaku hingga ' . esc(date('d M, H:i', strtotime($quizSession['pin_valid_until']))) ?></p></td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-700"><?= (int) $quizSession['participant_count'] ?> peserta</p><p class="mt-1 text-[.65rem] text-slate-400">Kapasitas: <?= $quizSession['max_participants'] !== null ? (int) $quizSession['max_participants'] : '∞' ?></p><p class="mt-1 text-[.6rem] text-slate-400"><?= (int) $quizSession['attempt_count'] ?> pengerjaan</p></td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-700"><?= $quizSession['average_score'] !== null ? number_format((float) $quizSession['average_score'], 1, ',', '.') : '-' ?> <span class="font-normal text-slate-400">rata-rata</span></p><p class="mt-1 text-[.65rem] text-slate-400"><?= (int) $quizSession['submitted_count'] ?> selesai</p></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[.62rem] font-bold <?= esc($statusClass) ?>"><span class="size-1.5 rounded-full <?= esc($statusDot) ?>"></span><?= esc(strtoupper($statusLabel)) ?></span><?php if ($quizSession['opened_at']): ?><p class="mt-2 text-[.6rem] text-slate-400">Dibuka <?= esc(date('d M, H:i', strtotime($quizSession['opened_at']))) ?></p><?php endif ?></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 text-xs sm:flex-row sm:items-center sm:justify-between"><p class="text-slate-400">Menampilkan <span class="font-semibold text-slate-600"><?= count($sessions) ?></span> dari <span class="font-semibold text-slate-600"><?= $totalResult ?></span> sesi</p><?php if ($totalPages > 1): ?><nav class="flex items-center gap-2" aria-label="Pagination sesi"><a href="<?= $pageUrl(max(1, $page - 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === 1 ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Sebelumnya</a><span class="px-2 text-slate-400">Halaman <?= $page ?> / <?= $totalPages ?></span><a href="<?= $pageUrl(min($totalPages, $page + 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === $totalPages ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Berikutnya</a></nav><?php endif ?></div>
    </section>
</div>
</div>
<?= $this->endSection() ?>
