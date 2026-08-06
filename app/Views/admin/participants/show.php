<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$attemptStatusMeta = [
    'SUBMITTED'   => ['Selesai', 'bg-green-50 text-green-600'],
    'IN_PROGRESS' => ['Dikerjakan', 'bg-orange-50 text-orange-600'],
    'EXPIRED'     => ['Kedaluwarsa', 'bg-slate-100 text-slate-500'],
];
$accuracy = $performance['answered'] > 0 ? round(($performance['correct'] / $performance['answered']) * 100, 1) : 0;
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <a href="<?= site_url('admin/participants') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Kembali ke Daftar Peserta</a>

    <section class="participant-detail-hero mt-5" aria-labelledby="participant-detail-title">
        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-4">
                <div class="grid size-16 shrink-0 place-items-center rounded-full bg-orange-500 text-xl font-black text-white shadow-lg shadow-orange-500/20"><?= esc(strtoupper(substr($participant['name'], 0, 2))) ?></div>
                <div><p class="text-[.65rem] font-bold uppercase tracking-[.18em] text-orange-500">Participant Profile</p><h2 id="participant-detail-title" class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900"><?= esc($participant['name']) ?></h2><p class="mt-2 font-mono text-[.65rem] text-slate-400"><?= esc($participant['participant_token']) ?></p></div>
            </div>
            <div class="flex flex-wrap gap-3"><a href="<?= site_url('admin/sessions/' . $participant['session_id']) ?>" class="inline-flex items-center gap-2 rounded-xl border border-orange-100 bg-orange-50 px-4 py-3 text-xs font-bold text-orange-600 transition hover:bg-orange-100"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg><?= esc($participant['session_name']) ?></a><a href="<?= site_url('admin/quizzes/' . $participant['quiz_id']) ?>" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-xs font-bold text-slate-600 transition hover:border-orange-200 hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg>Lihat Quiz</a></div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan performa peserta">
        <?php foreach ([['Total Attempt', $performance['attempts'], 'pengerjaan', 'bg-blue-50 text-blue-500'], ['Nilai Terbaik', number_format($performance['highest'], 1, ',', '.'), 'poin', 'bg-orange-50 text-orange-600'], ['Nilai Rata-rata', number_format($performance['average'], 1, ',', '.'), 'poin', 'bg-violet-50 text-violet-500'], ['Akurasi Jawaban', number_format($accuracy, 0, ',', '.') . '%', $performance['correct'] . ' benar', 'bg-green-50 text-green-600']] as [$label, $value, $unit, $color]): ?>
            <article class="stat-card flex items-center justify-between rounded-2xl border border-slate-100 bg-white p-5"><div><p class="text-xs text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-2xl font-extrabold text-slate-800"><?= esc((string) $value) ?></p><p class="mt-1 text-[.62rem] text-slate-400"><?= esc((string) $unit) ?></p></div><div class="grid size-10 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></div></article>
        <?php endforeach ?>
    </section>

    <div class="mt-6 grid items-start gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(20rem,.6fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="attempt-history-title">
            <div class="flex items-center justify-between border-b border-orange-100 bg-gradient-to-r from-white to-orange-50 px-5 py-4"><div><h3 id="attempt-history-title" class="text-sm font-bold text-slate-800">Riwayat Pengerjaan</h3><p class="mt-1 text-[.68rem] text-slate-400">Seluruh attempt yang dilakukan peserta</p></div><span class="rounded-full bg-orange-100 px-3 py-1.5 text-[.65rem] font-bold text-orange-600"><?= count($attempts) ?> attempt</span></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[52rem] text-left"><thead class="bg-slate-50/70 text-[.62rem] uppercase tracking-wider text-slate-400"><tr><th class="px-5 py-3 font-semibold">Waktu</th><th class="px-5 py-3 font-semibold">Jawaban</th><th class="px-5 py-3 font-semibold">Benar / Salah</th><th class="px-5 py-3 font-semibold">Nilai</th><th class="px-5 py-3 font-semibold">Status</th><th class="px-5 py-3 font-semibold">Hasil</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                <?php if ($attempts === []): ?><tr><td colspan="6" class="px-5 py-12 text-center text-slate-400">Peserta belum memulai pengerjaan quiz.</td></tr><?php endif ?>
                <?php foreach ($attempts as $attempt): ?>
                    <?php [$attemptLabel, $attemptClass] = $attemptStatusMeta[$attempt['status']] ?? [$attempt['status'], 'bg-slate-100 text-slate-500']; ?>
                    <tr class="hover:bg-orange-50/30"><td class="px-5 py-4"><p class="font-semibold text-slate-600"><?= esc(date('d M Y', strtotime($attempt['started_at']))) ?></p><p class="mt-1 text-[.62rem] text-slate-400"><?= esc(date('H:i:s', strtotime($attempt['started_at']))) ?></p></td><td class="px-5 py-4"><p class="font-bold text-slate-700"><?= (int) $attempt['total_answered'] ?> / <?= (int) $attempt['total_questions'] ?></p><p class="mt-1 text-[.62rem] text-slate-400">pertanyaan dijawab</p></td><td class="px-5 py-4"><span class="font-bold text-green-600"><?= (int) $attempt['total_correct'] ?> benar</span><p class="mt-1 text-[.62rem] text-red-500"><?= (int) $attempt['total_wrong'] ?> salah</p></td><td class="px-5 py-4"><p class="text-lg font-extrabold text-orange-600"><?= number_format((float) $attempt['final_score'], 1, ',', '.') ?></p><p class="mt-1 text-[.58rem] text-slate-400"><?= number_format((float) $attempt['total_score'], 0, ',', '.') ?> / <?= number_format((float) $attempt['max_score'], 0, ',', '.') ?> poin</p></td><td class="px-5 py-4"><span class="rounded-full <?= esc($attemptClass) ?> px-2 py-1 text-[.58rem] font-bold"><?= esc(strtoupper($attemptLabel)) ?></span></td><td class="px-5 py-4"><?php if ($attempt['passed'] !== null): ?><span class="rounded-full <?= $attempt['passed'] ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?> px-2.5 py-1 text-[.6rem] font-bold"><?= $attempt['passed'] ? 'LULUS' : 'TIDAK LULUS' ?></span><?php else: ?><span class="text-[.65rem] text-slate-400">Belum dinilai</span><?php endif ?></td></tr>
                <?php endforeach ?>
                </tbody></table>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="participant-info-title">
                <h3 id="participant-info-title" class="text-sm font-bold text-slate-800">Informasi Peserta</h3><p class="mt-1 text-[.68rem] text-slate-400">Identitas dan koneksi saat bergabung</p>
                <div class="mt-4 divide-y divide-slate-100">
                    <div class="py-3"><p class="text-[.62rem] text-slate-400">Nama lengkap</p><p class="mt-1 text-xs font-bold text-slate-700"><?= esc($participant['name']) ?></p></div>
                    <div class="py-3"><p class="text-[.62rem] text-slate-400">Token peserta</p><p class="mt-1 break-all font-mono text-[.68rem] font-semibold text-orange-600"><?= esc($participant['participant_token']) ?></p></div>
                    <div class="py-3"><p class="text-[.62rem] text-slate-400">Alamat IP</p><p class="mt-1 font-mono text-xs font-semibold text-slate-600"><?= esc($participant['ip_address'] ?: 'Tidak tersedia') ?></p></div>
                    <div class="py-3"><p class="text-[.62rem] text-slate-400">Bergabung</p><p class="mt-1 text-xs font-semibold text-slate-600"><?= esc(date('d M Y, H:i:s', strtotime($participant['joined_at']))) ?></p></div>
                    <div class="py-3"><p class="text-[.62rem] text-slate-400">Perangkat / Browser</p><p class="mt-1 break-words text-[.68rem] leading-relaxed text-slate-500"><?= esc($participant['user_agent'] ?: 'Tidak tersedia') ?></p></div>
                </div>
            </section>

            <section class="rounded-2xl border border-orange-100 bg-orange-50/70 p-5"><p class="text-xs font-bold text-orange-700">Sesi & Material</p><a href="<?= site_url('admin/sessions/' . $participant['session_id']) ?>" class="mt-3 block text-sm font-bold text-slate-700 hover:text-orange-600"><?= esc($participant['session_name']) ?></a><p class="mt-1 font-mono text-[.65rem] text-orange-600">PIN <?= esc($participant['session_pin']) ?></p><p class="mt-4 text-[.62rem] uppercase tracking-wider text-slate-400">Material</p><p class="mt-1 text-xs font-semibold text-slate-600"><?= esc($participant['material_title']) ?></p><p class="mt-1 font-mono text-[.6rem] text-slate-400"><?= esc($participant['material_code'] ?: '-') ?></p></section>
        </aside>
    </div>
</div>
</div>
<?= $this->endSection() ?>
