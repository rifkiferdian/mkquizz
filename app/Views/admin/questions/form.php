<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$isEdit = $question !== null;
$materialContextId = (int) ($materialContextId ?? 0);
$questionListUrl = site_url('admin/questions') . ($materialContextId > 0 ? '?' . http_build_query(['material_id' => $materialContextId]) : '');
$action = $isEdit ? site_url('admin/questions/' . $question['id']) : site_url('admin/questions') . ($materialContextId > 0 ? '?' . http_build_query(['material_id' => $materialContextId]) : '');
$selectedMaterialId = (string) old('material_id', $question['material_id'] ?? ($materialContextId ?: ''));
$questionType = (string) old('question_type', $question['question_type'] ?? 'MULTIPLE_CHOICE');
$postedOptions = old('option_text');

if (is_array($postedOptions)) {
    $optionValues = array_values($postedOptions);
} elseif ($options !== []) {
    $optionValues = array_column($options, 'option_text');
} else {
    $optionValues = ['', '', '', ''];
}

$correctOption = old('correct_option');
if ($correctOption === null && $options !== []) {
    foreach ($options as $index => $option) {
        if ((bool) $option['is_correct']) {
            $correctOption = (string) $index;
            break;
        }
    }
}
$correctOption = (string) ($correctOption ?? '0');
$isActive = (string) old('is_active', $question['is_active'] ?? 1) === '1';
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <a href="<?= $questionListUrl ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Kembali ke Pertanyaan</a>

    <?php if (session('error')): ?>
        <div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"><?= esc(session('error')) ?></div>
    <?php endif ?>

    <form action="<?= $action ?>" method="post" class="mt-5">
        <?= csrf_field() ?>
        <input type="hidden" name="return_material_id" value="<?= $materialContextId ?>">
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(24rem,.75fr)]">
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="question-detail-title">
                    <div class="question-form-heading"><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500"><?= $isEdit ? 'Update Question' : 'New Question' ?></p><h2 id="question-detail-title" class="mt-1 text-lg font-bold text-slate-800">Detail Pertanyaan</h2><p class="mt-1 text-xs text-slate-400">Tentukan material, tipe, dan isi pertanyaan.</p></div>
                    <div class="p-5 md:p-6">
                        <div class="grid gap-5 md:grid-cols-2">
                            <div><label for="material_id" class="question-label">Material <span class="text-red-500">*</span></label><select id="material_id" name="material_id" class="question-control <?= isset($errors['material_id']) ? 'has-error' : '' ?>" required><option value="">Pilih material</option><?php foreach ($materials as $material): ?><option value="<?= $material['id'] ?>" <?= $selectedMaterialId === (string) $material['id'] ? 'selected' : '' ?>><?= esc(($material['code'] ? $material['code'] . ' — ' : '') . $material['title']) ?></option><?php endforeach ?></select><?php if (isset($errors['material_id'])): ?><p class="question-error"><?= esc($errors['material_id']) ?></p><?php endif ?></div>
                            <div><label for="question_type" class="question-label">Tipe Pertanyaan <span class="text-red-500">*</span></label><select id="question_type" name="question_type" class="question-control" required><option value="MULTIPLE_CHOICE" <?= $questionType === 'MULTIPLE_CHOICE' ? 'selected' : '' ?>>Pilihan Ganda</option><option value="TRUE_FALSE" <?= $questionType === 'TRUE_FALSE' ? 'selected' : '' ?>>Benar / Salah</option></select></div>
                        </div>

                        <div class="mt-5"><label for="question_text" class="question-label">Isi Pertanyaan <span class="text-red-500">*</span></label><textarea id="question_text" name="question_text" rows="6" maxlength="5000" class="question-control min-h-40 resize-y <?= isset($errors['question_text']) ? 'has-error' : '' ?>" placeholder="Tuliskan pertanyaan secara jelas..." required><?= old('question_text', $question['question_text'] ?? '') ?></textarea><?php if (isset($errors['question_text'])): ?><p class="question-error"><?= esc($errors['question_text']) ?></p><?php endif ?></div>

                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div><label for="difficulty" class="question-label">Tingkat Kesulitan <span class="text-red-500">*</span></label><select id="difficulty" name="difficulty" class="question-control" required><?php foreach (['EASY' => 'Mudah', 'MEDIUM' => 'Sedang', 'HARD' => 'Sulit'] as $value => $label): ?><option value="<?= $value ?>" <?= (string) old('difficulty', $question['difficulty'] ?? 'MEDIUM') === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach ?></select></div>
                            <div><label for="default_score" class="question-label">Skor Default <span class="text-red-500">*</span></label><div class="relative"><input id="default_score" name="default_score" type="number" value="<?= old('default_score', $question['default_score'] ?? '10') ?>" min="0.01" max="10000" step="0.01" class="question-control pr-14" required><span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[.65rem] font-bold text-slate-400">POIN</span></div><?php if (isset($errors['default_score'])): ?><p class="question-error"><?= esc($errors['default_score']) ?></p><?php endif ?></div>
                        </div>

                        <div class="mt-5"><label for="explanation" class="question-label">Penjelasan Jawaban</label><textarea id="explanation" name="explanation" rows="4" maxlength="5000" class="question-control resize-y <?= isset($errors['explanation']) ? 'has-error' : '' ?>" placeholder="Berikan penjelasan yang akan ditampilkan setelah quiz..."><?= old('explanation', $question['explanation'] ?? '') ?></textarea><p class="mt-1.5 text-[.65rem] text-slate-400">Opsional. Membantu peserta memahami jawaban yang benar.</p><?php if (isset($errors['explanation'])): ?><p class="question-error"><?= esc($errors['explanation']) ?></p><?php endif ?></div>
                    </div>
                </section>
            </div>

            <div class="space-y-6 xl:sticky xl:top-24">
                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="answer-option-title">
                    <div class="question-form-heading"><div class="flex items-center justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Answer Options</p><h2 id="answer-option-title" class="mt-1 text-lg font-bold text-slate-800">Pilihan Jawaban</h2></div><span id="option-counter" class="rounded-full bg-orange-100 px-3 py-1.5 text-[.65rem] font-bold text-orange-600"><?= count($optionValues) ?> pilihan</span></div><p class="mt-1 text-xs text-slate-400">Pilih radio button untuk menentukan jawaban benar.</p></div>
                    <div class="p-5">
                        <?php if (isset($errors['options'])): ?><div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-600"><?= esc($errors['options']) ?></div><?php endif ?>
                        <?php if (isset($errors['correct_option'])): ?><div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-600"><?= esc($errors['correct_option']) ?></div><?php endif ?>

                        <div id="question-options" class="space-y-3" data-question-type="<?= esc($questionType, 'attr') ?>">
                            <?php foreach ($optionValues as $index => $optionValue): ?>
                                <div class="question-option-row">
                                    <label class="question-correct-control" title="Jadikan jawaban benar"><input type="radio" name="correct_option" value="<?= $index ?>" <?= $correctOption === (string) $index ? 'checked' : '' ?> required><span class="question-option-key"><?= chr(65 + $index) ?></span></label>
                                    <textarea name="option_text[]" rows="2" maxlength="2000" class="question-option-input" placeholder="Pilihan <?= chr(65 + $index) ?>" required><?= esc($optionValue) ?></textarea>
                                    <button type="button" class="remove-option-button" aria-label="Hapus pilihan <?= chr(65 + $index) ?>"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M8 6V3h8v3m3 0-1 15H6L5 6"/></svg></button>
                                </div>
                            <?php endforeach ?>
                        </div>

                        <button id="add-option-button" type="button" class="mt-4 flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-orange-200 bg-orange-50/50 px-4 py-3 text-xs font-bold text-orange-600 transition hover:bg-orange-50"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>Tambah Pilihan</button>
                        <div class="mt-4 flex items-start gap-2 rounded-xl bg-green-50 px-3 py-3 text-[.68rem] leading-relaxed text-green-700"><svg class="mt-0.5 size-4 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg><span>Lingkaran berwarna hijau menandakan pilihan yang menjadi jawaban benar.</span></div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <label class="flex cursor-pointer items-center justify-between gap-4"><span><span class="block text-xs font-bold text-slate-700">Pertanyaan Aktif</span><span class="mt-1 block text-[.68rem] text-slate-400">Pertanyaan dapat digunakan dalam quiz.</span></span><span><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" class="material-switch" <?= $isActive ? 'checked' : '' ?>></span></label>
                    <div class="mt-5 grid grid-cols-2 gap-3"><a href="<?= $questionListUrl ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-3 text-xs font-bold text-slate-500 transition hover:border-orange-200 hover:text-orange-600">Batal</a><button type="submit" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg><?= $isEdit ? 'Simpan' : 'Buat Soal' ?></button></div>
                </section>
            </div>
        </div>
    </form>
</div>
</div>

<template id="question-option-template">
    <div class="question-option-row">
        <label class="question-correct-control" title="Jadikan jawaban benar"><input type="radio" name="correct_option" required><span class="question-option-key"></span></label>
        <textarea name="option_text[]" rows="2" maxlength="2000" class="question-option-input" required></textarea>
        <button type="button" class="remove-option-button"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M8 6V3h8v3m3 0-1 15H6L5 6"/></svg></button>
    </div>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/questions.js') ?>" defer></script>
<?= $this->endSection() ?>
