<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$pageUrl = static function (int $targetPage) use ($filters): string {
    $parameters = array_filter([
        'q'          => $filters['search'],
        'quiz_id'    => $filters['quiz_id'] ?: '',
        'session_id' => $filters['session_id'] ?: '',
        'activity'   => $filters['activity'],
        'page'       => $targetPage,
    ], static fn ($value): bool => $value !== '');

    return site_url('admin/participants') . '?' . http_build_query($parameters);
};

$activityUrl = static function (string $activity) use ($filters): string {
    $parameters = array_filter([
        'q'          => $filters['search'],
        'quiz_id'    => $filters['quiz_id'] ?: '',
        'session_id' => $filters['session_id'] ?: '',
        'activity'   => $activity,
    ], static fn ($value): bool => $value !== '');
    $query = http_build_query($parameters);

    return site_url('admin/participants') . ($query !== '' ? '?' . $query : '');
};

$attemptMeta = [
    'SUBMITTED'   => ['Selesai', 'bg-green-50 text-green-600'],
    'IN_PROGRESS' => ['Dikerjakan', 'bg-orange-50 text-orange-600'],
    'EXPIRED'     => ['Kedaluwarsa', 'bg-slate-100 text-slate-500'],
];
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Participant Management</p><h2 class="mt-1 text-lg font-bold text-slate-800">Daftar Peserta</h2><p class="mt-1 text-xs text-slate-400">Pantau peserta, sesi, aktivitas, dan hasil quiz.</p></div>
        <div class="inline-flex items-center gap-2 rounded-xl border border-orange-100 bg-white px-4 py-3 text-xs text-slate-500 shadow-sm"><svg class="size-4 text-orange-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg><span><strong class="text-slate-700"><?= $summary['today'] ?></strong> peserta bergabung hari ini</span></div>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan peserta">
        <?php foreach ([['Total Peserta', $summary['total'], 'bg-blue-50 text-blue-500'], ['Bergabung Hari Ini', $summary['today'], 'bg-orange-50 text-orange-600'], ['Sudah Mengerjakan', $summary['attempted'], 'bg-violet-50 text-violet-500'], ['Peserta Lulus', $summary['passed'], 'bg-green-50 text-green-600']] as [$label, $value, $color]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5"><div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= number_format((int) $value, 0, ',', '.') ?></p></div><div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg></div></article>
        <?php endforeach ?>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="participant-list-title">
        <div class="participant-filter-panel">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3"><div class="grid size-9 place-items-center rounded-xl bg-orange-100 text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16M7 12h10M10 19h4"/></svg></div><div><p class="text-xs font-bold text-slate-700">Filter Peserta</p><p class="mt-0.5 text-[.65rem] text-slate-400">Cari peserta berdasarkan identitas dan aktivitas pengerjaan</p></div></div>
                <span class="rounded-full border border-orange-100 bg-white px-3 py-1.5 text-[.65rem] font-semibold text-slate-500"><strong class="text-orange-600"><?= $totalResult ?></strong> hasil ditemukan</span>
            </div>
            <nav class="session-status-tabs mt-4" aria-label="Filter aktivitas peserta">
                <?php foreach (['' => 'Semua', 'NOT_STARTED' => 'Belum Mulai', 'IN_PROGRESS' => 'Dikerjakan', 'COMPLETED' => 'Selesai'] as $activityValue => $activityLabel): ?>
                    <a href="<?= $activityUrl($activityValue) ?>" class="session-status-tab <?= $filters['activity'] === $activityValue ? 'active' : '' ?>" <?= $filters['activity'] === $activityValue ? 'aria-current="page"' : '' ?>><?php if ($activityValue !== ''): ?><span class="session-tab-dot <?= $activityValue === 'COMPLETED' ? 'bg-green-500' : ($activityValue === 'IN_PROGRESS' ? 'bg-orange-500' : 'bg-slate-400') ?>"></span><?php endif ?><?= esc($activityLabel) ?></a>
                <?php endforeach ?>
            </nav>
            <form action="<?= site_url('admin/participants') ?>" method="get" class="participant-filter-grid">
                <?php if ($filters['activity'] !== ''): ?><input type="hidden" name="activity" value="<?= esc($filters['activity'], 'attr') ?>"><?php endif ?>
                <div><label for="participant-search" class="material-filter-label">Cari Peserta</label><div class="relative"><svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input id="participant-search" type="search" name="q" value="<?= esc($filters['search']) ?>" class="material-filter-input pl-10" placeholder="Nama, token, atau alamat IP..."></div></div>
                <div><label for="participant-quiz" class="material-filter-label">Quiz</label><select id="participant-quiz" name="quiz_id" class="material-filter-input material-filter-select"><option value="">Semua quiz</option><?php foreach ($quizzes as $quiz): ?><option value="<?= $quiz['id'] ?>" <?= $filters['quiz_id'] === (int) $quiz['id'] ? 'selected' : '' ?>><?= esc($quiz['title']) ?></option><?php endforeach ?></select></div>
                <div><label for="participant-session" class="material-filter-label">Sesi</label><select id="participant-session" name="session_id" class="material-filter-input material-filter-select"><option value="">Semua sesi</option><?php foreach ($sessions as $quizSession): ?><option value="<?= $quizSession['id'] ?>" <?= $filters['session_id'] === (int) $quizSession['id'] ? 'selected' : '' ?>><?= esc($quizSession['session_name']) ?></option><?php endforeach ?></select></div>
                <button type="submit" class="material-filter-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>Cari</button>
                <?php if ($filters['search'] !== '' || $filters['quiz_id'] > 0 || $filters['session_id'] > 0 || $filters['activity'] !== ''): ?><a href="<?= site_url('admin/participants') ?>" class="material-reset-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7L3 8m0-5v5h5"/></svg>Reset</a><?php endif ?>
            </form>
        </div>

        <h3 id="participant-list-title" class="sr-only">Tabel daftar peserta</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[68rem] text-left"><thead class="bg-slate-50/70 text-[.65rem] uppercase tracking-wider text-slate-400"><tr><th class="px-5 py-3 font-semibold">Peserta</th><th class="px-5 py-3 font-semibold">Quiz & Sesi</th><th class="px-5 py-3 font-semibold">Bergabung</th><th class="px-5 py-3 font-semibold">Aktivitas</th><th class="px-5 py-3 font-semibold">Nilai Terbaik</th><th class="px-5 py-3 font-semibold">Hasil Terakhir</th></tr></thead>
            <tbody class="divide-y divide-slate-100 text-xs">
            <?php if ($participants === []): ?><tr><td colspan="6" class="px-5 py-14 text-center"><div class="mx-auto grid size-12 place-items-center rounded-2xl bg-orange-50 text-orange-500"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg></div><p class="mt-3 font-semibold text-slate-500">Peserta tidak ditemukan</p><p class="mt-1 text-[.68rem] text-slate-400">Coba ubah pencarian atau filter aktivitas.</p></td></tr><?php endif ?>
            <?php foreach ($participants as $participant): ?>
                <?php [$attemptLabel, $attemptClass] = $attemptMeta[$participant['latest_attempt_status']] ?? ['Belum mulai', 'bg-slate-100 text-slate-500']; ?>
                <tr class="transition hover:bg-orange-50/30">
                    <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="participant-avatar"><?= esc(strtoupper(substr($participant['name'], 0, 2))) ?></div><div><a href="<?= site_url('admin/participants/' . $participant['id']) ?>" class="font-bold text-slate-700 transition hover:text-orange-600"><?= esc($participant['name']) ?></a><p class="mt-1 font-mono text-[.58rem] text-slate-400"><?= esc($participant['participant_token']) ?></p><p class="mt-1 text-[.58rem] text-slate-400"><?= esc($participant['ip_address'] ?: 'IP tidak tersedia') ?></p></div></div></td>
                    <td class="px-5 py-4"><a href="<?= site_url('admin/quizzes/' . $participant['quiz_id']) ?>" class="block max-w-44 truncate font-semibold text-slate-600 hover:text-orange-600"><?= esc($participant['quiz_title']) ?></a><a href="<?= site_url('admin/sessions/' . $participant['session_id']) ?>" class="mt-1 block max-w-44 truncate text-[.65rem] text-orange-600 hover:underline"><?= esc($participant['session_name']) ?></a><p class="mt-1 font-mono text-[.58rem] text-slate-400">PIN <?= esc($participant['session_pin']) ?></p></td>
                    <td class="px-5 py-4 text-slate-500"><?= esc(date('d M Y', strtotime($participant['joined_at']))) ?><p class="mt-1 text-[.62rem] text-slate-400"><?= esc(date('H:i', strtotime($participant['joined_at']))) ?></p></td>
                    <td class="px-5 py-4"><p class="font-bold text-slate-700"><?= (int) $participant['attempt_count'] ?> attempt</p><span class="mt-2 inline-block rounded-full <?= esc($attemptClass) ?> px-2 py-1 text-[.58rem] font-bold"><?= esc(strtoupper($attemptLabel)) ?></span></td>
                    <td class="px-5 py-4"><p class="text-lg font-extrabold <?= $participant['best_score'] !== null ? 'text-orange-600' : 'text-slate-400' ?>"><?= $participant['best_score'] !== null ? number_format((float) $participant['best_score'], 1, ',', '.') : '-' ?></p><p class="mt-1 text-[.58rem] text-slate-400">Passing <?= number_format((float) $participant['passing_score'], 0, ',', '.') ?></p></td>
                    <td class="px-5 py-4"><?php if ($participant['latest_passed'] !== null): ?><span class="rounded-full <?= $participant['latest_passed'] ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?> px-2.5 py-1 text-[.6rem] font-bold"><?= $participant['latest_passed'] ? 'LULUS' : 'TIDAK LULUS' ?></span><p class="mt-2 text-[.62rem] text-slate-400">Nilai <?= number_format((float) $participant['latest_score'], 1, ',', '.') ?></p><?php else: ?><span class="text-[.65rem] text-slate-400">Belum ada hasil</span><?php endif ?></td>
                </tr>
            <?php endforeach ?>
            </tbody></table>
        </div>
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 text-xs sm:flex-row sm:items-center sm:justify-between"><p class="text-slate-400">Menampilkan <span class="font-semibold text-slate-600"><?= count($participants) ?></span> dari <span class="font-semibold text-slate-600"><?= $totalResult ?></span> peserta</p><?php if ($totalPages > 1): ?><nav class="flex items-center gap-2" aria-label="Pagination peserta"><a href="<?= $pageUrl(max(1, $page - 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === 1 ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Sebelumnya</a><span class="px-2 text-slate-400">Halaman <?= $page ?> / <?= $totalPages ?></span><a href="<?= $pageUrl(min($totalPages, $page + 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === $totalPages ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Berikutnya</a></nav><?php endif ?></div>
    </section>
</div>
</div>
<?= $this->endSection() ?>
