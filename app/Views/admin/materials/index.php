<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$pageUrl = static function (int $targetPage) use ($search, $status): string {
    $parameters = array_filter([
        'q'      => $search,
        'status' => $status,
        'page'   => $targetPage,
    ], static fn ($value): bool => $value !== '');

    return site_url('admin/materials') . '?' . http_build_query($parameters);
};
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
            <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Content Library</p>
            <h2 class="mt-1 text-lg font-bold text-slate-800">Daftar Material</h2>
            <p class="mt-1 text-xs text-slate-400">Atur materi yang digunakan pada pertanyaan dan quiz.</p>
        </div>
        <a href="<?= site_url('admin/materials/create') ?>" class="inline-flex items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Material
        </a>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-3" aria-label="Ringkasan material">
        <?php foreach ([['Total Material', $summary['total'], 'text-orange-600 bg-orange-50'], ['Material Aktif', $summary['active'], 'text-green-600 bg-green-50'], ['Nonaktif', $summary['inactive'], 'text-slate-600 bg-slate-100']] as [$label, $value, $color]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5">
                <div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= number_format((int) $value, 0, ',', '.') ?></p></div>
                <div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>">
                    <svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5m-3 3 3-3 3 3M5 5h10a2 2 0 0 1 2 2v12H7a2 2 0 0 1-2-2V5Z"/></svg>
                </div>
            </article>
        <?php endforeach ?>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="materials-table-title">
        <div class="material-filter-panel">
            <div class="mb-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <div class="grid size-8 place-items-center rounded-lg bg-orange-100 text-orange-600">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16M7 12h10M10 19h4"/></svg>
                    </div>
                    <div><p class="text-xs font-bold text-slate-700">Filter & Pencarian</p><p class="mt-0.5 text-[.65rem] text-slate-400">Temukan material dengan lebih cepat</p></div>
                </div>
                <span class="rounded-full border border-orange-100 bg-white px-3 py-1.5 text-[.65rem] font-semibold text-slate-500"><strong class="text-orange-600"><?= $totalResult ?></strong> hasil</span>
            </div>

            <form action="<?= site_url('admin/materials') ?>" method="get" class="material-filter-grid">
                <div>
                    <label for="material-search" class="material-filter-label">Cari Material</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg>
                        <input id="material-search" type="search" name="q" value="<?= esc($search) ?>" class="material-filter-input pl-10" placeholder="Masukkan kode atau nama material...">
                    </div>
                </div>
                <div>
                    <label for="material-status" class="material-filter-label">Status</label>
                    <div class="relative">
                        <select id="material-status" name="status" class="material-filter-input material-filter-select">
                            <option value="">Semua status</option>
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Material aktif</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Material nonaktif</option>
                        </select>
                        <svg class="pointer-events-none absolute right-3.5 top-1/2 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m6 9 6 6 6-6"/></svg>
                    </div>
                </div>
                <button type="submit" class="material-filter-button">
                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16l-6 7v5l-4 2v-7L4 5Z"/></svg>
                    Terapkan Filter
                </button>
                <?php if ($search !== '' || $status !== ''): ?>
                    <a href="<?= site_url('admin/materials') ?>" class="material-reset-button" aria-label="Reset filter">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 12a9 9 0 1 0 3-6.7L3 8m0-5v5h5"/></svg>
                        Reset
                    </a>
                <?php endif ?>
            </form>
        </div>

        <h3 id="materials-table-title" class="sr-only">Tabel material</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[48rem] text-left">
                <thead class="bg-slate-50/70 text-[.65rem] uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-5 py-3 font-semibold">Material</th>
                        <th class="px-5 py-3 font-semibold">Konten</th>
                        <th class="px-5 py-3 font-semibold">Dibuat oleh</th>
                        <th class="px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                <?php if ($materials === []): ?>
                    <tr><td colspan="5" class="px-5 py-14 text-center"><p class="font-semibold text-slate-500">Material tidak ditemukan</p><p class="mt-1 text-[.68rem] text-slate-400">Coba ubah pencarian atau tambahkan material baru.</p></td></tr>
                <?php endif ?>
                <?php foreach ($materials as $material): ?>
                    <tr class="transition hover:bg-orange-50/30">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-orange-50 font-bold text-orange-600"><?= esc(strtoupper(substr($material['title'], 0, 1))) ?></div>
                                <div class="min-w-0"><p class="max-w-xs truncate font-bold text-slate-700"><?= esc($material['title']) ?></p><p class="mt-1 font-mono text-[.65rem] text-slate-400"><?= esc($material['code'] ?: 'TANPA KODE') ?></p></div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-500"><span class="font-bold text-slate-700"><?= (int) $material['question_count'] ?></span> pertanyaan <span class="mx-1 text-slate-300">•</span> <span class="font-bold text-slate-700"><?= (int) $material['quiz_count'] ?></span> quiz</td>
                        <td class="px-5 py-4"><p class="font-medium text-slate-600"><?= esc($material['creator_name'] ?? '-') ?></p><p class="mt-1 text-[.65rem] text-slate-400"><?= esc(date('d M Y', strtotime($material['created_at']))) ?></p></td>
                        <td class="px-5 py-4">
                            <form action="<?= site_url('admin/materials/' . $material['id'] . '/toggle') ?>" method="post">
                                <?= csrf_field() ?>
                                <button type="submit" class="cursor-pointer rounded-full <?= $material['is_active'] ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' ?> px-2.5 py-1 text-[.62rem] font-bold"><?= $material['is_active'] ? 'AKTIF' : 'NONAKTIF' ?></button>
                            </form>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="<?= site_url('admin/materials/' . $material['id'] . '/edit') ?>" class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-orange-200 hover:bg-orange-50 hover:text-orange-600" aria-label="Edit <?= esc($material['title']) ?>">
                                    <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m14 4 6 6L8 22H2v-6L14 4Z"/><path d="m12 6 6 6"/></svg>
                                </a>
                                <form action="<?= site_url('admin/materials/' . $material['id'] . '/delete') ?>" method="post" class="delete-material-form" data-material-name="<?= esc($material['title'], 'attr') ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="grid size-8 cursor-pointer place-items-center rounded-lg border border-slate-200 text-slate-400 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600" aria-label="Hapus <?= esc($material['title']) ?>">
                                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M8 6V3h8v3m3 0-1 15H6L5 6m4 4v7m6-7v7"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 px-5 py-4 text-xs sm:flex-row sm:items-center sm:justify-between">
            <p class="text-slate-400">Menampilkan <span class="font-semibold text-slate-600"><?= count($materials) ?></span> dari <span class="font-semibold text-slate-600"><?= $totalResult ?></span> material</p>
            <?php if ($totalPages > 1): ?>
                <nav class="flex items-center gap-2" aria-label="Pagination material">
                    <a href="<?= $pageUrl(max(1, $page - 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === 1 ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Sebelumnya</a>
                    <span class="px-2 text-slate-400">Halaman <?= $page ?> / <?= $totalPages ?></span>
                    <a href="<?= $pageUrl(min($totalPages, $page + 1)) ?>" class="rounded-lg border border-slate-200 px-3 py-2 font-semibold <?= $page === $totalPages ? 'pointer-events-none opacity-40' : 'text-slate-500 hover:border-orange-200 hover:text-orange-600' ?>">Berikutnya</a>
                </nav>
            <?php endif ?>
        </div>
    </section>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/materials.js') ?>" defer></script>
<?= $this->endSection() ?>
