<?= $this->extend('admin/layouts/main') ?>

<?= $this->section('content') ?>
<?php
$statusMeta = [
    'OPEN'    => ['Dibuka', 'bg-green-50 text-green-600 border-green-100', 'bg-green-500'],
    'WAITING' => ['Menunggu', 'bg-orange-50 text-orange-600 border-orange-100', 'bg-orange-500'],
    'CLOSED'  => ['Ditutup', 'bg-slate-100 text-slate-500 border-slate-200', 'bg-slate-400'],
    'DRAFT'   => ['Draft', 'bg-blue-50 text-blue-500 border-blue-100', 'bg-blue-500'],
];
[$statusLabel, $statusClass, $statusDot] = $statusMeta[$quizSession['status']] ?? [$quizSession['status'], 'bg-slate-100 text-slate-500 border-slate-200', 'bg-slate-400'];

$attemptStatusMeta = [
    'SUBMITTED'   => ['Selesai', 'bg-green-50 text-green-600'],
    'IN_PROGRESS' => ['Dikerjakan', 'bg-orange-50 text-orange-600'],
    'EXPIRED'     => ['Kedaluwarsa', 'bg-slate-100 text-slate-500'],
];
$participantAccessUrl = site_url('quiz/' . rawurlencode($quizSession['session_token']));
?>
<div class="p-5 md:p-8">
<div class="mx-auto max-w-7xl">
    <a href="<?= site_url('admin/sessions') ?>" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m15 18-6-6 6-6"/></svg>Kembali ke Daftar Sesi</a>

    <?php if (session('success')): ?><div class="mt-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status"><?= esc(session('success')) ?></div><?php endif ?>
    <?php if (session('error')): ?><div class="mt-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert"><?= esc(session('error')) ?></div><?php endif ?>

    <section class="quiz-detail-hero mt-5" aria-labelledby="session-detail-title">
        <div class="relative z-10">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="flex items-start gap-4">
                    <div class="grid size-14 shrink-0 place-items-center rounded-2xl bg-orange-500 text-white shadow-lg shadow-orange-500/20"><svg class="size-7" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z"/></svg></div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2"><span class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[.62rem] font-bold <?= esc($statusClass) ?>"><span class="size-1.5 rounded-full <?= esc($statusDot) ?>"></span><?= esc(strtoupper($statusLabel)) ?></span><span class="rounded-full border border-orange-100 bg-orange-50 px-2.5 py-1 text-[.62rem] font-bold text-orange-600"><?= esc($quizSession['material_code'] ?: 'MATERIAL') ?></span></div>
                        <h2 id="session-detail-title" class="mt-3 max-w-3xl text-2xl font-extrabold tracking-tight text-slate-900"><?= esc($quizSession['session_name']) ?></h2>
                        <a href="<?= site_url('admin/quizzes/' . $quizSession['quiz_id']) ?>" class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-orange-600"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg><?= esc($quizSession['quiz_title']) ?></a>
                    </div>
                </div>
                <div class="shrink-0 rounded-xl border border-orange-100 bg-white/80 px-4 py-3 text-xs"><p class="text-slate-400">Dibuat oleh</p><p class="mt-1 font-bold text-slate-700"><?= esc($quizSession['creator_name'] ?? '-') ?></p><p class="mt-1 text-[.65rem] text-slate-400"><?= esc(date('d M Y, H:i', strtotime($quizSession['created_at']))) ?></p></div>
            </div>
        </div>
    </section>

    <div class="mt-6 grid gap-4 xl:grid-cols-[.9fr_1.65fr]">
        <section class="session-access-card" aria-labelledby="session-access-title">
            <div class="relative z-10">
                <div class="flex items-center justify-between"><div><p class="text-[.65rem] font-bold uppercase tracking-[.18em] text-orange-200">Session Access</p><h3 id="session-access-title" class="mt-1 text-sm font-bold text-white">Kode Akses Peserta</h3></div><svg class="size-6 text-orange-200" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg></div>
                <div class="mt-6 flex items-end justify-between gap-4"><div><p class="text-[.65rem] text-orange-200">PIN SESI</p><p class="mt-1 font-mono text-4xl font-black tracking-[.22em] text-white"><?= esc($quizSession['pin']) ?></p></div><span class="rounded-full <?= $quizSession['pin_expired'] ? 'bg-red-500/30 text-red-100' : 'bg-green-500/30 text-green-100' ?> px-3 py-1.5 text-[.62rem] font-bold"><?= $quizSession['pin_expired'] ? 'KEDALUWARSA' : 'MASIH BERLAKU' ?></span></div>
                <div class="mt-5 border-t border-white/15 pt-4"><p class="text-[.6rem] uppercase tracking-wider text-orange-200">Token Sesi</p><p class="mt-1 break-all font-mono text-xs font-semibold text-white"><?= esc($quizSession['session_token']) ?></p><a href="<?= esc($participantAccessUrl, 'attr') ?>" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-[.65rem] font-bold text-white transition hover:bg-white/20"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3h7v7m0-7-9 9M10 5H5a2 2 0 0 0-2 2v12h12a2 2 0 0 0 2-2v-5"/></svg>Buka Halaman Peserta</a><p class="mt-3 text-[.65rem] text-orange-100">Berlaku hingga <?= esc(date('d M Y, H:i', strtotime($quizSession['pin_valid_until']))) ?> WIB</p></div>
                <?php if ($quizSession['status'] !== 'CLOSED'): ?>
                    <form action="<?= site_url('admin/sessions/' . $quizSession['id'] . '/extend-pin') ?>" method="post" class="mt-5 border-t border-white/15 pt-4">
                        <?= csrf_field() ?>
                        <label for="additional_minutes" class="text-[.65rem] font-bold text-white">Tambah Masa Berlaku PIN</label>
                        <div class="mt-2 flex gap-2"><div class="relative min-w-0 flex-1"><input id="additional_minutes" name="additional_minutes" type="number" value="<?= esc(old('additional_minutes', '2'), 'attr') ?>" min="1" max="1440" class="w-full rounded-lg border border-white/20 bg-white/95 px-3 py-2.5 pr-14 text-xs font-bold text-slate-700 outline-none transition focus:border-orange-200 focus:ring-2 focus:ring-white/30" required><span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[.58rem] font-bold text-slate-400">MENIT</span></div><button type="submit" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-xs font-bold text-orange-600 shadow-sm transition hover:bg-orange-50"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg>Perpanjang</button></div>
                        <p class="mt-2 text-[.6rem] leading-relaxed text-orange-100">Jika sudah kedaluwarsa, PIN akan aktif kembali mulai sekarang.</p>
                    </form>
                <?php else: ?>
                    <p class="mt-5 border-t border-white/15 pt-4 text-[.65rem] text-orange-100">Sesi sudah ditutup sehingga PIN tidak dapat diperpanjang.</p>
                <?php endif ?>
            </div>
        </section>

        <section id="participant-qr-card" class="rounded-2xl border border-orange-100 bg-white p-5 shadow-sm" data-url="<?= esc($participantAccessUrl, 'attr') ?>" aria-labelledby="participant-qr-title">
            <div class="flex items-start justify-between gap-4"><div><p class="text-[.62rem] font-bold uppercase tracking-[.16em] text-orange-500">Participant Access</p><h3 id="participant-qr-title" class="mt-1 text-sm font-bold text-slate-800">QR Code & Ringkasan Sesi</h3><p class="mt-1 text-[.68rem] leading-relaxed text-slate-400">Bagikan akses sekaligus pantau aktivitas peserta.</p></div><div class="grid size-9 shrink-0 place-items-center rounded-xl bg-orange-50 text-orange-500"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h3v3h-3zM18 18h3v3h-3zM18 14h3"/></svg></div></div>

            <div class="session-overview-grid mt-5">
                <a href="<?= site_url('admin/sessions/' . $quizSession['id'] . '/share') ?>" target="_blank" rel="noopener" class="session-qr-preview group" title="Buka QR Code ukuran besar"><div class="session-qr-frame"><canvas id="participant-qr-code" class="block max-w-full" aria-label="QR Code halaman peserta"></canvas><p id="participant-qr-error" class="hidden px-4 py-10 text-center text-xs text-red-500">QR Code gagal dibuat. Gunakan link peserta di bawah.</p></div><span class="session-qr-preview-label"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3h7v7m0-7-9 9M10 5H5a2 2 0 0 0-2 2v12h12a2 2 0 0 0 2-2v-5"/></svg>Perbesar QR</span></a>

                <section class="grid gap-3 sm:grid-cols-2" aria-label="Ringkasan sesi">
                    <?php foreach ([
                        ['Peserta', $performance['participants'], 'orang bergabung', 'bg-blue-50 text-blue-500', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                        ['Pengerjaan', $performance['attempts'], 'total attempt', 'bg-orange-50 text-orange-600', 'M12 7v5l3 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z'],
                        ['Rata-rata', number_format($performance['average'], 1, ',', '.'), 'nilai peserta', 'bg-violet-50 text-violet-500', 'M4 19V9m5 10V5m5 14v-7m5 7V3'],
                        ['Kelulusan', number_format($performance['pass_rate'], 0, ',', '.') . '%', $performance['passed'] . ' peserta lulus', 'bg-green-50 text-green-600', 'm5 12 4 4L19 6'],
                    ] as [$label, $value, $unit, $color, $icon]): ?>
                        <article class="session-summary-card session-summary-card-compact">
                            <div class="grid size-10 shrink-0 place-items-center rounded-xl <?= esc($color) ?>"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="<?= esc($icon) ?>"/></svg></div>
                            <div class="min-w-0"><p class="text-[.65rem] font-medium text-slate-400"><?= esc($label) ?></p><div class="mt-1"><p class="text-xl font-extrabold leading-none tracking-tight text-slate-800"><?= esc((string) $value) ?></p><p class="mt-1 truncate text-[.58rem] text-slate-400"><?= esc((string) $unit) ?></p></div></div>
                        </article>
                    <?php endforeach ?>
                </section>
            </div>

            <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 px-3 py-2.5"><p class="text-[.58rem] font-bold uppercase tracking-wider text-slate-400">Link Peserta</p><p class="mt-1 truncate font-mono text-[.65rem] text-slate-600" title="<?= esc($participantAccessUrl, 'attr') ?>"><?= esc($participantAccessUrl) ?></p></div>
            <div class="participant-access-actions mt-3"><button id="copy-participant-url" type="button" class="session-qr-action"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V4H4v12h4"/></svg>Salin Link</button><a id="download-participant-qr" href="#" download="qr-<?= esc($quizSession['session_token'], 'attr') ?>.png" class="session-qr-action pointer-events-none opacity-50"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3v12m-4-4 4 4 4-4M5 21h14"/></svg>Unduh QR</a><a href="<?= esc($participantAccessUrl, 'attr') ?>" target="_blank" rel="noopener" class="session-qr-action border-orange-200 bg-orange-50 text-orange-600"><svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3h7v7m0-7-9 9M10 5H5a2 2 0 0 0-2 2v12h12a2 2 0 0 0 2-2v-5"/></svg>Buka Link</a></div>
            <p id="participant-copy-status" class="mt-2 min-h-4 text-center text-[.62rem] font-semibold text-green-600" aria-live="polite"></p>
        </section>
    </div>

    <div class="mt-6 grid items-start gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(20rem,.6fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="participant-list-title">
            <div class="flex items-center justify-between border-b border-orange-100 bg-gradient-to-r from-white to-orange-50 px-5 py-4"><div><h3 id="participant-list-title" class="text-sm font-bold text-slate-800">Leaderboard</h3><p class="mt-1 text-[.68rem] text-slate-400">Peringkat peserta berdasarkan nilai terbaik</p></div><span class="rounded-full bg-orange-100 px-3 py-1.5 text-[.65rem] font-bold text-orange-600"><?= count($participants) ?> peserta</span></div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[44rem] text-left"><thead class="bg-slate-50/70 text-[.62rem] uppercase tracking-wider text-slate-400"><tr><th class="px-5 py-3 text-center font-semibold">Peringkat</th><th class="px-5 py-3 font-semibold">Peserta</th><th class="px-5 py-3 font-semibold">Nilai Terbaik</th><th class="px-5 py-3 font-semibold">Pengerjaan</th><th class="px-5 py-3 font-semibold">Hasil</th></tr></thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                <?php if ($participants === []): ?><tr><td colspan="5" class="px-5 py-12 text-center text-slate-400">Leaderboard belum memiliki peserta.</td></tr><?php endif ?>
                <?php foreach ($participants as $index => $participant): ?>
                    <?php
                    [$attemptLabel, $attemptClass] = $attemptStatusMeta[$participant['latest_attempt_status']] ?? ['Belum mulai', 'bg-slate-100 text-slate-500'];
                    $hasScore = $participant['best_score'] !== null;
                    $rank = $hasScore ? $index + 1 : null;
                    $rankClass = match ($rank) {
                        1       => 'bg-amber-100 text-amber-600 border-amber-200',
                        2       => 'bg-slate-100 text-slate-500 border-slate-200',
                        3       => 'bg-orange-100 text-orange-600 border-orange-200',
                        default => 'bg-white text-slate-400 border-slate-200',
                    };
                    ?>
                    <tr class="hover:bg-orange-50/30"><td class="px-5 py-4"><div class="mx-auto grid size-9 place-items-center rounded-full border text-xs font-extrabold <?= esc($rankClass) ?>"><?= $rank ?? '—' ?></div></td><td class="px-5 py-4"><div class="flex items-center gap-3"><div class="grid size-9 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600"><?= esc(strtoupper(substr($participant['name'], 0, 2))) ?></div><div><a href="<?= site_url('admin/participants/' . $participant['id']) ?>" class="font-bold text-slate-700 transition hover:text-orange-600"><?= esc($participant['name']) ?></a><p class="mt-1 font-mono text-[.58rem] text-slate-400"><?= esc($participant['participant_token']) ?></p></div></div></td><td class="px-5 py-4"><p class="text-lg font-extrabold <?= $hasScore ? 'text-orange-600' : 'text-slate-400' ?>"><?= $hasScore ? number_format((float) $participant['best_score'], 1, ',', '.') : '-' ?></p></td><td class="px-5 py-4"><p class="font-bold text-slate-700"><?= (int) $participant['attempt_count'] ?> kali</p><span class="mt-1 inline-block rounded-full <?= esc($attemptClass) ?> px-2 py-1 text-[.58rem] font-bold"><?= esc(strtoupper($attemptLabel)) ?></span></td><td class="px-5 py-4"><?php if ($participant['latest_passed'] !== null): ?><span class="rounded-full <?= $participant['latest_passed'] ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?> px-2.5 py-1 text-[.6rem] font-bold"><?= $participant['latest_passed'] ? 'LULUS' : 'TIDAK LULUS' ?></span><?php else: ?><span class="text-[.65rem] text-slate-400">Belum ada hasil</span><?php endif ?></td></tr>
                <?php endforeach ?>
                </tbody></table>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="session-config-title">
                <h3 id="session-config-title" class="text-sm font-bold text-slate-800">Konfigurasi Sesi</h3><p class="mt-1 text-[.68rem] text-slate-400">Batas akses dan peserta</p>
                <div class="mt-4 divide-y divide-slate-100">
                    <div class="flex items-center justify-between py-3"><span class="text-xs text-slate-500">Durasi quiz</span><span class="text-xs font-bold text-slate-700"><?= (int) $quizSession['duration_minutes'] ?> menit</span></div>
                    <div class="flex items-center justify-between py-3"><span class="text-xs text-slate-500">Passing grade</span><span class="text-xs font-bold text-slate-700"><?= number_format((float) $quizSession['passing_score'], 0, ',', '.') ?></span></div>
                    <div class="flex items-center justify-between py-3"><span class="text-xs text-slate-500">Masa berlaku PIN</span><span class="text-xs font-bold text-slate-700"><?= (int) $quizSession['pin_valid_minutes'] ?> menit</span></div>
                    <div class="flex items-center justify-between py-3"><span class="text-xs text-slate-500">Maksimal peserta</span><span class="text-xs font-bold text-slate-700"><?= $quizSession['max_participants'] !== null ? (int) $quizSession['max_participants'] : 'Tanpa batas' ?></span></div>
                    <div class="flex items-center justify-between py-3"><span class="text-xs text-slate-500">Nama duplikat</span><span class="rounded-full <?= $quizSession['allow_duplicate_name'] ? 'bg-green-50 text-green-600' : 'bg-slate-100 text-slate-500' ?> px-2 py-1 text-[.58rem] font-bold"><?= $quizSession['allow_duplicate_name'] ? 'DIIZINKAN' : 'DITOLAK' ?></span></div>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm" aria-labelledby="session-timeline-title">
                <h3 id="session-timeline-title" class="text-sm font-bold text-slate-800">Timeline Sesi</h3>
                <div class="session-timeline mt-5">
                    <div class="session-timeline-item"><span class="session-timeline-dot bg-blue-500"></span><p class="text-xs font-semibold text-slate-600">PIN mulai berlaku</p><p class="mt-1 text-[.65rem] text-slate-400"><?= esc(date('d M Y, H:i', strtotime($quizSession['pin_valid_from']))) ?></p></div>
                    <div class="session-timeline-item"><span class="session-timeline-dot bg-orange-500"></span><p class="text-xs font-semibold text-slate-600">Sesi dibuka</p><p class="mt-1 text-[.65rem] text-slate-400"><?= $quizSession['opened_at'] ? esc(date('d M Y, H:i', strtotime($quizSession['opened_at']))) : 'Belum dibuka' ?></p></div>
                    <div class="session-timeline-item"><span class="session-timeline-dot <?= $quizSession['closed_at'] ? 'bg-slate-500' : 'bg-slate-300' ?>"></span><p class="text-xs font-semibold text-slate-600">Sesi ditutup</p><p class="mt-1 text-[.65rem] text-slate-400"><?= $quizSession['closed_at'] ? esc(date('d M Y, H:i', strtotime($quizSession['closed_at']))) : 'Belum ditutup' ?></p></div>
                </div>
            </section>
        </aside>
    </div>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/session-qrcode.js') ?>" defer></script>
<?= $this->endSection() ?>
