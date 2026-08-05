<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$isEdit = $material !== null;
$action = $isEdit ? site_url('admin/materials/' . $material['id']) : site_url('admin/materials');
$isActive = (string) old('is_active', $material['is_active'] ?? 1) === '1';
?>
<div class="p-5 md:p-8">
    <div class="mx-auto max-w-4xl">
        <a href="<?= site_url('admin/materials') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600">
            <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Materials
        </a>

        <div class="mt-5 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="border-b border-orange-100 bg-gradient-to-r from-white to-orange-50 px-6 py-5">
                <p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500"><?= $isEdit ? 'Update Content' : 'New Content' ?></p>
                <h2 class="mt-1 text-lg font-bold text-slate-800"><?= $isEdit ? 'Edit Material' : 'Tambah Material Baru' ?></h2>
                <p class="mt-1 text-xs text-slate-400">Lengkapi informasi utama material di bawah ini.</p>
            </div>

            <form action="<?= $action ?>" method="post" class="p-6">
                <?= csrf_field() ?>
                <div class="grid gap-5 md:grid-cols-[.65fr_1.35fr]">
                    <div>
                        <label for="code" class="mb-2 block text-xs font-bold text-slate-700">Kode Material</label>
                        <input id="code" name="code" type="text" value="<?= old('code', $material['code'] ?? '') ?>" maxlength="30" class="material-form-control uppercase <?= isset($errors['code']) ? 'has-error' : '' ?>" placeholder="Contoh: MAT001">
                        <p class="mt-1.5 text-[.65rem] text-slate-400">Gunakan huruf, angka, strip, atau underscore.</p>
                        <?php if (isset($errors['code'])): ?><p class="mt-1.5 text-xs text-red-600"><?= esc($errors['code']) ?></p><?php endif ?>
                    </div>
                    <div>
                        <label for="title" class="mb-2 block text-xs font-bold text-slate-700">Nama Material <span class="text-red-500">*</span></label>
                        <input id="title" name="title" type="text" value="<?= old('title', $material['title'] ?? '') ?>" maxlength="200" class="material-form-control <?= isset($errors['title']) ? 'has-error' : '' ?>" placeholder="Masukkan nama material" required autofocus>
                        <?php if (isset($errors['title'])): ?><p class="mt-1.5 text-xs text-red-600"><?= esc($errors['title']) ?></p><?php endif ?>
                    </div>
                </div>

                <div class="mt-5">
                    <label for="description" class="mb-2 block text-xs font-bold text-slate-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="6" maxlength="2000" class="material-form-control min-h-36 resize-y <?= isset($errors['description']) ? 'has-error' : '' ?>" placeholder="Jelaskan gambaran singkat material ini..."><?= old('description', $material['description'] ?? '') ?></textarea>
                    <div class="mt-1.5 flex items-center justify-between"><p class="text-[.65rem] text-slate-400">Opsional, maksimal 2.000 karakter.</p></div>
                    <?php if (isset($errors['description'])): ?><p class="mt-1.5 text-xs text-red-600"><?= esc($errors['description']) ?></p><?php endif ?>
                </div>

                <div class="mt-5 rounded-xl border border-slate-100 bg-slate-50/70 p-4">
                    <label class="flex cursor-pointer items-center justify-between gap-4">
                        <span><span class="block text-xs font-bold text-slate-700">Material Aktif</span><span class="mt-1 block text-[.68rem] text-slate-400">Material aktif dapat digunakan saat membuat quiz dan pertanyaan.</span></span>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="material-switch" <?= $isActive ? 'checked' : '' ?>>
                    </label>
                </div>

                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="<?= site_url('admin/materials') ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-5 py-3 text-xs font-bold text-slate-500 transition hover:border-orange-200 hover:text-orange-600">Batal</a>
                    <button type="submit" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-500 px-5 py-3 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                        <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Material' ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
