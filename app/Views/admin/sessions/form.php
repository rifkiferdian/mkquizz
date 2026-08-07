<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$errors = session('errors') ?? [];
$isEdit = $session !== null;
$quizValue = (string) old('quiz_id', $selectedQuizId ?: '');
$statusValue = (string) old('status', $session['status'] ?? 'WAITING');
$allowDuplicate = (string) old('allow_duplicate_name', (string) ($session['allow_duplicate_name'] ?? '0')) === '1';
$action = $isEdit ? site_url('admin/sessions/' . $session['id']) : site_url('admin/sessions');
$backUrl = $isEdit ? site_url('admin/sessions/' . $session['id']) : site_url('admin/sessions');
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <a href="<?= $backUrl ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg><?= $isEdit ? 'Kembali ke Detail Sesi' : 'Kembali ke Daftar Sesi' ?></a>

    <?php if (session('error')): ?><div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"><?= esc(session('error')) ?></div><?php endif ?>

    <form action="<?= $action ?>" method="post" class="mt-5">
        <?= csrf_field() ?>
        <div class="grid items-start gap-6 xl:grid-cols-[minmax(0,1.15fr)_minmax(22rem,.85fr)]">
            <div class="space-y-6">
                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="session-form-title">
                    <div class="question-form-heading"><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500"><?= $isEdit ? 'Edit Session' : 'New Session' ?></p><h2 id="session-form-title" class="mt-1 text-lg font-bold text-slate-800">Informasi Sesi Quiz</h2><p class="mt-1 text-xs text-slate-400">Pilih quiz aktif dan tentukan identitas pelaksanaannya.</p></div>
                    <div class="p-5 md:p-6">
                        <div><label for="quiz_id" class="question-label">Quiz <span class="text-red-500">*</span></label><?php if ($isQuizLocked): ?><input type="hidden" name="quiz_id" value="<?= esc($quizValue, 'attr') ?>"><?php endif ?><select id="quiz_id" name="<?= $isQuizLocked ? '' : 'quiz_id' ?>" class="question-control <?= isset($errors['quiz_id']) ? 'has-error' : '' ?>" required <?= $isQuizLocked ? 'disabled' : '' ?>><option value="">Pilih quiz aktif</option><?php foreach ($quizzes as $quiz): ?><option value="<?= $quiz['id'] ?>" <?= $quizValue === (string) $quiz['id'] ? 'selected' : '' ?> <?= (int) $quiz['question_count'] === 0 ? 'disabled' : '' ?>><?= esc($quiz['title']) ?> — <?= (int) $quiz['question_count'] ?> soal</option><?php endforeach ?></select><p class="mt-1.5 text-[.65rem] text-slate-400"><?= $isQuizLocked ? 'Quiz tidak dapat diganti karena peserta sudah bergabung.' : 'Quiz tanpa pertanyaan tidak dapat digunakan.' ?></p><?php if (isset($errors['quiz_id'])): ?><p class="question-error"><?= esc($errors['quiz_id']) ?></p><?php endif ?></div>
                        <div class="mt-5"><label for="session_name" class="question-label">Nama Sesi <span class="text-red-500">*</span></label><input id="session_name" name="session_name" type="text" value="<?= esc(old('session_name', $session['session_name'] ?? ''), 'attr') ?>" maxlength="200" class="question-control <?= isset($errors['session_name']) ? 'has-error' : '' ?>" placeholder="Contoh: Ujian HTML — Kelas XII RPL A" required><?php if (isset($errors['session_name'])): ?><p class="question-error"><?= esc($errors['session_name']) ?></p><?php endif ?></div>
                    </div>
                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="session-schedule-title">
                    <div class="question-form-heading"><p class="text-xs font-bold uppercase tracking-[.18em] text-orange-500">Schedule & Capacity</p><h2 id="session-schedule-title" class="mt-1 text-lg font-bold text-slate-800">Jadwal dan Kapasitas</h2><p class="mt-1 text-xs text-slate-400">Atur waktu berlakunya PIN dan batas peserta yang dapat bergabung.</p></div>
                    <div class="p-5 md:p-6">
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div><label for="pin_valid_from" class="question-label">Mulai Berlaku <span class="text-red-500">*</span></label><input id="pin_valid_from" name="pin_valid_from" type="datetime-local" value="<?= esc(old('pin_valid_from', $defaultStart), 'attr') ?>" class="question-control <?= isset($errors['pin_valid_from']) ? 'has-error' : '' ?>" required><?php if (isset($errors['pin_valid_from'])): ?><p class="question-error"><?= esc($errors['pin_valid_from']) ?></p><?php endif ?></div>
                            <div><label for="pin_valid_minutes" class="question-label">Masa Berlaku PIN <span class="text-red-500">*</span></label><div class="relative"><input id="pin_valid_minutes" name="pin_valid_minutes" type="number" value="<?= esc(old('pin_valid_minutes', $session['pin_valid_minutes'] ?? '2'), 'attr') ?>" min="1" max="10080" class="question-control pr-16" required><span class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2 text-[.62rem] font-bold text-slate-400">MENIT</span></div><?php if (isset($errors['pin_valid_minutes'])): ?><p class="question-error"><?= esc($errors['pin_valid_minutes']) ?></p><?php endif ?></div>
                        </div>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div><label for="max_participants" class="question-label">Maksimal Peserta</label><input id="max_participants" name="max_participants" type="number" value="<?= esc(old('max_participants', $session['max_participants'] ?? ''), 'attr') ?>" min="1" max="100000" class="question-control <?= isset($errors['max_participants']) ? 'has-error' : '' ?>" placeholder="Kosongkan jika tanpa batas"><?php if (isset($errors['max_participants'])): ?><p class="question-error"><?= esc($errors['max_participants']) ?></p><?php endif ?></div>
                            <div><label for="status" class="question-label"><?= $isEdit ? 'Status Sesi' : 'Status Awal' ?> <span class="text-red-500">*</span></label><select id="status" name="status" class="question-control <?= isset($errors['status']) ? 'has-error' : '' ?>" required><option value="DRAFT" <?= $statusValue === 'DRAFT' ? 'selected' : '' ?>>Draft</option><option value="WAITING" <?= $statusValue === 'WAITING' ? 'selected' : '' ?>>Menunggu</option><option value="OPEN" <?= $statusValue === 'OPEN' ? 'selected' : '' ?>>Dibuka</option><?php if ($isEdit): ?><option value="CLOSED" <?= $statusValue === 'CLOSED' ? 'selected' : '' ?>>Ditutup</option><?php endif ?></select><?php if (isset($errors['status'])): ?><p class="question-error"><?= esc($errors['status']) ?></p><?php endif ?></div>
                        </div>
                    </div>
                </section>
            </div>

            <div class="space-y-6 xl:sticky xl:top-24">
                <section class="session-create-access" aria-labelledby="automatic-access-title">
                    <div class="relative z-10"><div class="flex items-center justify-between"><div><p class="text-[.65rem] font-bold uppercase tracking-[.18em] text-orange-200"><?= $isEdit ? 'Participant Access' : 'Automatic Access' ?></p><h2 id="automatic-access-title" class="mt-1 text-base font-bold text-white"><?= $isEdit ? 'Akses Peserta Tetap' : 'PIN & Token Otomatis' ?></h2></div><svg class="size-7 text-orange-200" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></div><div class="mt-6 rounded-xl border border-white/15 bg-white/10 p-4"><p class="text-[.62rem] uppercase tracking-wider text-orange-100"><?= $isEdit ? 'PIN Sesi' : 'Contoh PIN' ?></p><p class="mt-2 font-mono text-3xl font-black tracking-[.25em] text-white"><?= $isEdit ? esc($session['pin']) : '••••••' ?></p></div><p class="mt-4 text-[.68rem] leading-relaxed text-orange-100"><?= $isEdit ? 'PIN dan token peserta tidak berubah saat pengaturan sesi diperbarui.' : 'Sistem membuat PIN 6 digit dan token unik setelah sesi berhasil disimpan.' ?></p></div>
                </section>

                <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
                    <label class="flex cursor-pointer items-center justify-between gap-4"><span><span class="block text-xs font-bold text-slate-700">Izinkan nama peserta sama</span><span class="mt-1 block text-[.68rem] leading-relaxed text-slate-400">Aktifkan jika beberapa peserta mungkin menggunakan nama identik.</span></span><span class="shrink-0"><input type="hidden" name="allow_duplicate_name" value="0"><input type="checkbox" name="allow_duplicate_name" value="1" class="material-switch" <?= $allowDuplicate ? 'checked' : '' ?>></span></label>
                    <div class="mt-5 rounded-xl bg-blue-50 px-4 py-3 text-[.68rem] leading-relaxed text-blue-700">Status <strong>Menunggu</strong> cocok untuk sesi terjadwal. Pilih <strong>Langsung Dibuka</strong> hanya jika waktu mulai tidak berada di masa mendatang.</div>
                </section>

                <div class="grid grid-cols-2 gap-3"><a href="<?= $backUrl ?>" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-500 transition hover:border-orange-200 hover:text-orange-600">Batal</a><button type="submit" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl bg-orange-500 px-4 py-3 text-xs font-bold text-white shadow-lg shadow-orange-500/20 transition hover:bg-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg><?= $isEdit ? 'Simpan Perubahan' : 'Buat Sesi' ?></button></div>
            </div>
        </div>
    </form>
</div>
</div>
<?= $this->endSection() ?>
