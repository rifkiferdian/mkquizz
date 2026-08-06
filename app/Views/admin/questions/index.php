<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$pageUrl = static function (int $targetPage) use ($filters): string {
    $parameters = array_filter([
        'q'           => $filters['search'],
        'material_id' => $filters['material_id'] ?: '',
        'difficulty'  => $filters['difficulty'],
        'type'        => $filters['type'],
        'status'      => $filters['status'],
        'page'        => $targetPage,
    ], static fn ($value): bool => $value !== '');

    return site_url('admin/questions') . '?' . http_build_query($parameters);
};

$difficultyLabels = ['EASY' => 'Mudah', 'MEDIUM' => 'Sedang', 'HARD' => 'Sulit'];
$difficultyColors = ['EASY' => 'bg-green-50 text-green-600', 'MEDIUM' => 'bg-orange-50 text-orange-600', 'HARD' => 'bg-red-50 text-red-600'];
$createQuestionUrl = site_url('admin/questions/create') . ($filters['material_id'] > 0 ? '?' . http_build_query(['material_id' => $filters['material_id']]) : '');
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <?php if (session('success')): ?>
        <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status"><?= esc(session('success')) ?></div>
    <?php endif ?>
    <?php if (session('error')): ?>
        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"><?= esc(session('error')) ?></div>
    <?php endif ?>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Question Bank</p>
            <h2 class="mt-1 text-lg font-bold text-slate-800">Bank Pertanyaan</h2>
            <p class="mt-1 text-xs text-slate-400">Kelola soal, pilihan jawaban, dan kunci jawaban.</p>
        </div>
        <a href="<?= $createQuestionUrl ?>" class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pertanyaan
        </a>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan pertanyaan">
        <?php foreach ([['Total Pertanyaan', $summary['total'], 'bg-orange-50 text-orange-600'], ['Pertanyaan Aktif', $summary['active'], 'bg-green-50 text-green-600'], ['Pilihan Ganda', $summary['multiple_choice'], 'bg-blue-50 text-blue-500'], ['Benar / Salah', $summary['true_false'], 'bg-violet-50 text-violet-500']] as [$label, $value, $color]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5">
                <div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= number_format((int) $value, 0, ',', '.') ?></p></div>
                <div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>">
                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 18h.01M9.2 9a3 3 0 1 1 4.7 2.5c-1.2.8-1.9 1.2-1.9 2.5M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg>
                </div>
            </article>
        <?php endforeach ?>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="questions-table-title">
        <div class="question-filter-panel">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="grid size-8 place-items-center rounded-lg bg-orange-100 text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16M7 12h10M10 19h4"/></svg></div>
                    <div><p class="text-xs font-bold text-slate-700">Filter Pertanyaan</p><p class="mt-0.5 text-[.65rem] text-slate-400">Saring bank soal berdasarkan kebutuhan</p></div>
                </div>
                <span class="rounded-full border border-orange-100 bg-white px-3 py-1.5 text-[.65rem] font-semibold text-slate-500"><strong class="text-orange-600"><?= $totalResult ?></strong> hasil</span>
            </div>
            <form action="<?= site_url('admin/questions') ?>" method="get" class="question-filter-grid">
                <div class="question-filter-search">
                    <label for="question-search" class="material-filter-label">Cari Pertanyaan</label>
                    <div class="relative"><svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input id="question-search" type="search" name="q" value="<?= esc($filters['search']) ?>" class="material-filter-input pl-10" placeholder="Cari isi pertanyaan..."></div>
                </div>
                <div><label for="question-material" class="material-filter-label">Material</label><select id="question-material" name="material_id" class="material-filter-input material-filter-select"><option value="">Semua material</option><?php foreach ($materials as $material): ?><option value="<?= $material['id'] ?>" <?= $filters['material_id'] === (int) $material['id'] ? 'selected' : '' ?>><?= esc($material['title']) ?></option><?php endforeach ?></select></div>
                <div><label for="question-type" class="material-filter-label">Tipe</label><select id="question-type" name="type" class="material-filter-input material-filter-select"><option value="">Semua tipe</option><option value="MULTIPLE_CHOICE" <?= $filters['type'] === 'MULTIPLE_CHOICE' ? 'selected' : '' ?>>Pilihan Ganda</option><option value="TRUE_FALSE" <?= $filters['type'] === 'TRUE_FALSE' ? 'selected' : '' ?>>Benar / Salah</option></select></div>
                <div><label for="question-difficulty" class="material-filter-label">Kesulitan</label><select id="question-difficulty" name="difficulty" class="material-filter-input material-filter-select"><option value="">Semua level</option><option value="EASY" <?= $filters['difficulty'] === 'EASY' ? 'selected' : '' ?>>Mudah</option><option value="MEDIUM" <?= $filters['difficulty'] === 'MEDIUM' ? 'selected' : '' ?>>Sedang</option><option value="HARD" <?= $filters['difficulty'] === 'HARD' ? 'selected' : '' ?>>Sulit</option></select></div>
                <div><label for="question-status" class="material-filter-label">Status</label><select id="question-status" name="status" class="material-filter-input material-filter-select"><option value="">Semua</option><option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Aktif</option><option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option></select></div>
                <div class="question-filter-actions">
                    <button type="submit" class="material-filter-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z"/></svg>Filter</button>
                    <?php if (array_filter($filters, static fn ($value): bool => $value !== '' && $value !== 0) !== []): ?><a href="<?= site_url('admin/questions') ?>" class="material-reset-button">Reset</a><?php endif ?>
                </div>
            </form>
        </div>

        <h3 id="questions-table-title" class="sr-only">Tabel pertanyaan</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[64rem] text-left">
                <thead class="bg-slate-50/70 text-[.65rem] uppercase tracking-wider text-slate-400"><tr><th class="px-5 py-3 font-semibold">Pertanyaan</th><th class="px-5 py-3 font-semibold">Material</th><th class="px-5 py-3 font-semibold">Tipe & Level</th><th class="px-5 py-3 font-semibold">Jawaban Benar</th><th class="px-5 py-3 font-semibold">Skor</th><th class="px-5 py-3 text-right font-semibold">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                <?php if ($questions === []): ?><tr><td colspan="6" class="px-5 py-14 text-center"><p class="font-semibold text-slate-500">Pertanyaan tidak ditemukan</p><p class="mt-1 text-[.68rem] text-slate-400">Ubah filter atau tambahkan pertanyaan baru.</p></td></tr><?php endif ?>
                <?php foreach ($questions as $question): ?>
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-4"><div class="flex items-start gap-3"><div class="grid size-9 shrink-0 place-items-center rounded-xl bg-orange-50 font-bold text-orange-600">Q</div><div><p class="max-w-sm font-semibold leading-relaxed text-slate-700"><?= esc($question['question_text']) ?></p><p class="mt-1 text-[.65rem] text-slate-400"><?= (int) $question['option_count'] ?> pilihan • <?= esc(date('d M Y', strtotime($question['created_at']))) ?></p></div></div></td>
                        <td class="px-5 py-4"><p class="max-w-36 truncate font-medium text-slate-600"><?= esc($question['material_title']) ?></p><p class="mt-1 font-mono text-[.62rem] text-slate-400"><?= esc($question['material_code'] ?: '-') ?></p></td>
                        <td class="px-5 py-4"><span class="block text-[.65rem] font-bold text-slate-600"><?= $question['question_type'] === 'TRUE_FALSE' ? 'BENAR / SALAH' : 'PILIHAN GANDA' ?></span><span class="mt-1 inline-block rounded-full <?= $difficultyColors[$question['difficulty']] ?? 'bg-slate-100 text-slate-500' ?> px-2 py-1 text-[.58rem] font-bold"><?= esc($difficultyLabels[$question['difficulty']] ?? '-') ?></span></td>
                        <td class="px-5 py-4"><div class="max-w-48 rounded-lg border border-green-100 bg-green-50 px-3 py-2 text-[.68rem] font-medium leading-relaxed text-green-700"><?= esc($question['correct_answer'] ?? '-') ?></div></td>
                        <td class="px-5 py-4"><p class="font-bold text-slate-700"><?= number_format((float) $question['default_score'], 0, ',', '.') ?> poin</p><form class="mt-2" action="<?= site_url('admin/questions/' . $question['id'] . '/toggle') ?>" method="post"><?= csrf_field() ?><button type="submit" class="cursor-pointer rounded-full <?= $question['is_active'] ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' ?> px-2 py-1 text-[.58rem] font-bold"><?= $question['is_active'] ? 'AKTIF' : 'NONAKTIF' ?></button></form></td>
                        <td class="px-5 py-4"><div class="flex items-center justify-end gap-2"><a href="<?= site_url('admin/questions/' . $question['id'] . '/edit') ?>" class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600" aria-label="Edit pertanyaan"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m14 4 6 6L8 22H2v-6L14 4Z"/><path d="m12 6 6 6"/></svg></a><form action="<?= site_url('admin/questions/' . $question['id'] . '/delete') ?>" method="post" class="delete-question-form" data-question-text="<?= esc(mb_strimwidth($question['question_text'], 0, 70, '...'), 'attr') ?>"><?= csrf_field() ?><button type="submit" class="grid size-8 cursor-pointer place-items-center rounded-lg border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Hapus pertanyaan"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M8 6V3h8v3m3 0-1 15H6L5 6m4 4v7m6-7v7"/></svg></button></form></div></td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 text-xs sm:flex-row sm:items-center sm:justify-between"><p class="text-slate-400">Menampilkan <span class="font-semibold text-slate-600"><?= count($questions) ?></span> dari <span class="font-semibold text-slate-600"><?= $totalResult ?></span> pertanyaan</p><?php if ($totalPages > 1): ?><nav class="flex items-center gap-2" aria-label="Pagination pertanyaan"><a href="<?= $pageUrl(max(1, $page - 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === 1 ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Sebelumnya</a><span class="px-2 text-slate-400">Halaman <?= $page ?> / <?= $totalPages ?></span><a href="<?= $pageUrl(min($totalPages, $page + 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === $totalPages ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Berikutnya</a></nav><?php endif ?></div>
    </section>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/questions.js') ?>" defer></script>
<?= $this->endSection() ?>
