<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$selectedQuestions = array_map('strval', (array) old('question_ids', []));
$selectedMaterial = (string) old('material_id', '');
$settings = [
    'shuffle_questions'   => ['Acak pertanyaan', 'Urutan soal berbeda untuk setiap peserta.', 0],
    'shuffle_options'     => ['Acak pilihan jawaban', 'Susunan opsi jawaban akan diacak.', 0],
    'show_score'          => ['Tampilkan nilai', 'Peserta dapat melihat nilai akhirnya.', 1],
    'show_correct_answer' => ['Tampilkan jawaban benar', 'Jawaban benar muncul setelah selesai.', 1],
    'show_explanation'    => ['Tampilkan penjelasan', 'Penjelasan soal ditampilkan saat review.', 1],
    'allow_review'        => ['Izinkan review', 'Peserta dapat meninjau hasil pengerjaan.', 1],
];
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <a href="<?= site_url('admin/quizzes') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Kembali ke Daftar Quiz</a>

    <?php if (session('error')): ?><div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"><?= esc(session('error')) ?></div><?php endif ?>

    <form action="<?= site_url('admin/quizzes') ?>" method="post" class="mt-5">
        <?= csrf_field() ?>
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(22rem,.85fr)]">
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-form-title">
                    <div class="question-form-heading"><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">New Quiz</p><h2 id="quiz-form-title" class="mt-1 text-lg font-bold text-slate-800">Informasi Quiz</h2><p class="mt-1 text-xs text-slate-400">Tentukan materi, judul, durasi, dan standar kelulusan.</p></div>
                    <div class="p-5 md:p-6">
                        <div><label for="material_id" class="question-label">Material <span class="text-red-500">*</span></label><select id="material_id" name="material_id" class="question-control <?= isset($errors['material_id']) ? 'has-error' : '' ?>" required><option value="">Pilih material</option><?php foreach ($materials as $material): ?><option value="<?= $material['id'] ?>" <?= $selectedMaterial === (string) $material['id'] ? 'selected' : '' ?>><?= esc(($material['code'] ? $material['code'] . ' — ' : '') . $material['title']) ?></option><?php endforeach ?></select><?php if (isset($errors['material_id'])): ?><p class="question-error"><?= esc($errors['material_id']) ?></p><?php endif ?></div>
                        <div class="mt-5"><label for="title" class="question-label">Judul Quiz <span class="text-red-500">*</span></label><input id="title" name="title" type="text" value="<?= esc(old('title'), 'attr') ?>" maxlength="200" class="question-control <?= isset($errors['title']) ? 'has-error' : '' ?>" placeholder="Contoh: Evaluasi Dasar HTML" required><?php if (isset($errors['title'])): ?><p class="question-error"><?= esc($errors['title']) ?></p><?php endif ?></div>
                        <div class="mt-5"><label for="description" class="question-label">Deskripsi</label><textarea id="description" name="description" rows="4" maxlength="5000" class="question-control resize-y <?= isset($errors['description']) ? 'has-error' : '' ?>" placeholder="Jelaskan tujuan atau petunjuk singkat quiz..."><?= esc(old('description')) ?></textarea><?php if (isset($errors['description'])): ?><p class="question-error"><?= esc($errors['description']) ?></p><?php endif ?></div>
                        <div class="mt-5 grid gap-5 sm:grid-cols-3">
                            <div><label for="duration_minutes" class="question-label">Durasi <span class="text-red-500">*</span></label><div class="relative"><input id="duration_minutes" name="duration_minutes" type="number" value="<?= esc(old('duration_minutes', '15'), 'attr') ?>" min="1" max="480" class="question-control pr-16" required><span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[.62rem] font-bold text-slate-400">MENIT</span></div><?php if (isset($errors['duration_minutes'])): ?><p class="question-error"><?= esc($errors['duration_minutes']) ?></p><?php endif ?></div>
                            <div><label for="passing_score" class="question-label">Nilai Lulus <span class="text-red-500">*</span></label><div class="relative"><input id="passing_score" name="passing_score" type="number" value="<?= esc(old('passing_score', '75'), 'attr') ?>" min="0" max="100" step="0.01" class="question-control pr-10" required><span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[.7rem] font-bold text-slate-400">%</span></div><?php if (isset($errors['passing_score'])): ?><p class="question-error"><?= esc($errors['passing_score']) ?></p><?php endif ?></div>
                            <div><label for="status" class="question-label">Status <span class="text-red-500">*</span></label><select id="status" name="status" class="question-control" required><?php foreach (['DRAFT' => 'Draft', 'ACTIVE' => 'Aktif', 'INACTIVE' => 'Nonaktif'] as $value => $label): ?><option value="<?= $value ?>" <?= (string) old('status', 'DRAFT') === $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach ?></select></div>
                        </div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-question-title">
                    <div class="question-form-heading"><div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Question Bank</p><h2 id="quiz-question-title" class="mt-1 text-lg font-bold text-slate-800">Pilih Pertanyaan</h2></div><span id="selected-question-count" class="w-fit rounded-full bg-orange-100 px-3 py-1.5 text-[.65rem] font-bold text-orange-600">0 dipilih</span></div><p class="mt-1 text-xs text-slate-400">Hanya pertanyaan aktif dari material yang dipilih yang dapat digunakan.</p></div>
                    <div class="p-5 md:p-6">
                        <?php if (isset($errors['question_ids'])): ?><div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs text-red-600"><?= esc($errors['question_ids']) ?></div><?php endif ?>
                        <div class="relative"><svg class="pointer-events-none absolute left-3.5 top-1/2 z-10 size-4 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg><input id="question-search" type="search" class="question-control question-search-control" placeholder="Cari isi pertanyaan..."></div>
                        <div id="quiz-question-list" class="quiz-question-picker mt-4">
                            <?php foreach ($questions as $question): ?>
                                <label class="quiz-question-choice" data-material="<?= $question['material_id'] ?>" data-search="<?= esc(mb_strtolower($question['question_text']), 'attr') ?>">
                                    <input type="checkbox" name="question_ids[]" value="<?= $question['id'] ?>" class="quiz-question-checkbox" <?= in_array((string) $question['id'], $selectedQuestions, true) ? 'checked' : '' ?>>
                                    <span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold text-slate-700"><?= esc($question['question_text']) ?></span><span class="mt-1 flex flex-wrap gap-2 text-[.62rem] text-slate-400"><span><?= esc($question['material_code'] ?: $question['material_title']) ?></span><span>•</span><span><?= esc($question['question_type'] === 'TRUE_FALSE' ? 'Benar/Salah' : 'Pilihan Ganda') ?></span><span>•</span><span><?= number_format((float) $question['default_score'], 0, ',', '.') ?> poin</span></span></span>
                                </label>
                            <?php endforeach ?>
                            <div id="quiz-question-empty" class="hidden px-4 py-10 text-center text-xs text-slate-400">Pilih material atau buat pertanyaan aktif terlebih dahulu.</div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6 xl:sticky xl:top-24">
                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="quiz-setting-title">
                    <div class="question-form-heading"><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Quiz Settings</p><h2 id="quiz-setting-title" class="mt-1 text-lg font-bold text-slate-800">Pengaturan Tampilan</h2></div>
                    <div class="divide-y divide-slate-100 px-5">
                        <?php foreach ($settings as $field => [$label, $description, $default]): ?>
                            <?php $checked = (string) old($field, (string) $default) === '1'; ?>
                            <label class="flex cursor-pointer items-center justify-between gap-4 py-4"><span><span class="block text-xs font-bold text-slate-700"><?= esc($label) ?></span><span class="mt-1 block text-[.65rem] leading-relaxed text-slate-400"><?= esc($description) ?></span></span><span class="shrink-0"><input type="hidden" name="<?= esc($field, 'attr') ?>" value="0"><input type="checkbox" name="<?= esc($field, 'attr') ?>" value="1" class="material-switch" <?= $checked ? 'checked' : '' ?>></span></label>
                        <?php endforeach ?>
                    </div>
                </section>
                <section class="rounded-2xl border border-orange-100 bg-orange-50/60 p-5"><div class="flex items-start gap-3"><svg class="mt-0.5 size-5 shrink-0 text-orange-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="M12 8v5m0 3h.01"/></svg><div><p class="text-xs font-bold text-orange-700">Sebelum menyimpan</p><p class="mt-1 text-[.68rem] leading-relaxed text-orange-700/70">Bobot setiap pertanyaan mengikuti skor default pada bank pertanyaan dan dapat dilihat pada detail quiz.</p></div></div></section>
                <div class="grid grid-cols-2 gap-3"><a href="<?= site_url('admin/quizzes') ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-500 transition hover:border-orange-200 hover:text-orange-600">Batal</a><button type="submit" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>Buat Quiz</button></div>
            </div>
        </div>
    </form>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/quiz-form.js') ?>" defer></script>
<?= $this->endSection() ?>
