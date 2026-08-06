<?php
$scoredParticipants = array_values(array_filter($participants, static fn (array $row): bool => $row['best_score'] !== null));
$topParticipants = array_slice($scoredParticipants, 0, 3);
$formatDuration = static function ($seconds): string {
    if ($seconds === null) {
        return '-';
    }

    $seconds = max(0, (int) $seconds);
    $minutes = intdiv($seconds, 60);
    $remainder = $seconds % 60;

    return $minutes > 0 ? $minutes . 'm ' . $remainder . 'd' : $remainder . ' detik';
};
$podiumMeta = [
    1 => ['Juara 1', 'leaderboard-podium-first', 'bg-amber-100 text-amber-600'],
    2 => ['Juara 2', 'leaderboard-podium-second', 'bg-slate-100 text-slate-500'],
    3 => ['Juara 3', 'leaderboard-podium-third', 'bg-orange-100 text-orange-600'],
];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Leaderboard hasil quiz <?= esc($quizSession['session_name'], 'attr') ?>">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="qr-share-page public-leaderboard-page">
<header class="qr-share-header"><div class="participant-brand" aria-label="MKQuizz"><span class="text-base font-black leading-none">MQ</span><span class="text-[.38rem] font-bold leading-none">MKQUIZZ</span></div><div><p class="text-sm font-extrabold text-slate-800">MK<span class="text-orange-500">Quizz</span></p><p class="text-[.62rem] text-slate-400">Leaderboard Peserta</p></div></header>

<main class="public-leaderboard-main">
<div class="public-leaderboard-shell">
    <section class="leaderboard-hero" aria-labelledby="leaderboard-title">
        <div class="relative z-10 flex items-center justify-between gap-5">
            <div><div class="flex flex-wrap items-center gap-2"><span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[.58rem] font-bold uppercase tracking-[.14em] text-orange-100">Live Result</span><span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[.58rem] font-bold text-white"><?= esc($quizSession['status']) ?></span><span class="rounded-full border border-white/20 bg-white/10 px-3 py-1 text-[.58rem] font-bold text-white"><?= count($participants) ?> peserta</span></div><h1 id="leaderboard-title" class="mt-2 text-xl font-black tracking-tight text-white md:text-2xl">Leaderboard <?= esc($quizSession['session_name']) ?></h1><p class="mt-1 text-xs text-orange-100"><?= esc($quizSession['quiz_title']) ?> &middot; Nilai terbaik, kemudian waktu tercepat.</p></div>
            <div class="leaderboard-trophy"><svg class="size-9" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 21h8M12 17v4M7 4H4v2a5 5 0 0 0 5 5m8-7h3v2a5 5 0 0 1-5 5M7 3h10v5a5 5 0 0 1-10 0V3Z"/></svg></div>
        </div>
    </section>

    <div class="leaderboard-screen-body">
        <div class="leaderboard-screen-left">
            <section class="leaderboard-screen-stats" aria-label="Statistik leaderboard">
                <?php foreach ([
                    ['Peserta', $performance['participants'], 'bergabung', 'bg-blue-50 text-blue-500', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z'],
                    ['Selesai', $performance['submitted'], 'hasil masuk', 'bg-orange-50 text-orange-600', 'm5 12 4 4L19 6'],
                    ['Rata-rata', number_format($performance['average'], 1, ',', '.'), 'nilai', 'bg-violet-50 text-violet-500', 'M4 19V9m5 10V5m5 14v-7m5 7V3'],
                    ['Tertinggi', number_format($performance['highest'], 1, ',', '.'), 'skor', 'bg-green-50 text-green-600', 'm12 3 2.6 5.3 5.9.8-4.3 4.2 1 5.9-5.2-2.8-5.2 2.8 1-5.9-4.3-4.2 5.9-.8L12 3Z'],
                ] as [$label, $value, $unit, $color, $icon]): ?>
                    <article class="stat-card flex items-center gap-3 rounded-xl border border-slate-100 bg-white p-3"><div class="grid size-9 shrink-0 place-items-center rounded-lg <?= esc($color) ?>"><svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="<?= esc($icon) ?>"/></svg></div><div><p class="text-[.58rem] text-slate-400"><?= esc($label) ?></p><p class="mt-1 text-xl font-extrabold leading-none text-slate-800"><?= esc((string) $value) ?></p><p class="mt-1 text-[.52rem] text-slate-400"><?= esc($unit) ?></p></div></article>
                <?php endforeach ?>
            </section>

            <section class="leaderboard-screen-podium rounded-2xl border border-orange-100 bg-white p-4 shadow-sm" aria-labelledby="podium-title">
                <div class="text-center"><p class="text-[.58rem] font-bold uppercase tracking-[.18em] text-orange-500">Top Performers</p><h2 id="podium-title" class="mt-1 text-sm font-extrabold text-slate-800">3 Peringkat Terbaik</h2></div>
                <?php if ($topParticipants === []): ?>
                    <div class="grid h-full place-items-center py-6 text-center"><div><div class="mx-auto grid size-12 place-items-center rounded-2xl bg-orange-50 text-orange-500"><svg class="size-6" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M8 21h8M12 17v4M7 4H4v2a5 5 0 0 0 5 5m8-7h3v2a5 5 0 0 1-5 5M7 3h10v5a5 5 0 0 1-10 0V3Z"/></svg></div><p class="mt-3 text-xs font-bold text-slate-600">Belum ada hasil quiz</p></div></div>
                <?php else: ?>
                    <div class="leaderboard-podium mt-4">
                        <?php foreach ($topParticipants as $index => $participant): ?>
                            <?php $rank = $index + 1; [$rankLabel, $podiumClass, $badgeClass] = $podiumMeta[$rank]; ?>
                            <article class="leaderboard-podium-card <?= esc($podiumClass) ?>"><?php if ($rank === 1): ?><svg class="mx-auto mb-1 size-5 text-amber-500" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m4 7 4 4 4-7 4 7 4-4-2 11H6L4 7Z"/></svg><?php endif ?><div class="relative mx-auto grid size-12 place-items-center rounded-full bg-orange-100 text-sm font-black text-orange-600 ring-4 ring-white"><?= esc(strtoupper(substr($participant['name'], 0, 2))) ?><span class="absolute -right-1 -top-1 grid size-6 place-items-center rounded-full text-[.58rem] font-black <?= esc($badgeClass) ?>"><?= $rank ?></span></div><p class="mt-2 text-[.52rem] font-bold uppercase tracking-wider text-slate-400"><?= esc($rankLabel) ?></p><p class="mt-1 truncate text-xs font-extrabold text-slate-800"><?= esc($participant['name']) ?></p><p class="mt-2 text-2xl font-black text-orange-600"><?= number_format((float) $participant['best_score'], 1, ',', '.') ?></p><p class="mt-1 text-[.55rem] text-slate-400"><?= esc($formatDuration($participant['best_duration_seconds'])) ?> &middot; <?= (int) $participant['attempt_count'] ?> attempt</p></article>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </section>
        </div>

        <section class="leaderboard-screen-table overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm" aria-labelledby="ranking-list-title">
            <div class="flex items-center justify-between gap-3 border-b border-orange-100 bg-gradient-to-r from-white to-orange-50 px-5 py-3"><div><h2 id="ranking-list-title" class="text-sm font-bold text-slate-800">Peringkat Lengkap</h2><p class="mt-1 text-[.62rem] text-slate-400">Hasil terbaik seluruh peserta</p></div><span class="rounded-full bg-orange-100 px-3 py-1.5 text-[.58rem] font-bold text-orange-600"><?= count($scoredParticipants) ?> memiliki nilai</span></div>
            <div id="leaderboard-table-scroll" class="leaderboard-table-scroll">
                <table class="w-full min-w-[40rem] text-left">
                    <thead class="bg-slate-50/95 text-[.68rem] uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-5 py-4 text-center font-bold">Rank</th>
                            <th class="px-5 py-4 font-bold">Peserta</th>
                            <th class="px-5 py-4 font-bold">Nilai</th>
                            <th class="px-5 py-4 font-bold">Waktu</th>
                            <th class="px-5 py-4 font-bold">Hasil</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-[.82rem]">
                    <?php if ($participants === []): ?><tr><td colspan="5" class="px-5 py-14 text-center text-slate-400">Belum ada peserta pada sesi ini.</td></tr><?php endif ?>
                    <?php foreach ($participants as $index => $participant): ?>
                        <?php
                        $hasScore = $participant['best_score'] !== null;
                        $rank = $hasScore ? $index + 1 : null;
                        $passed = $hasScore && (float) $participant['best_score'] >= (float) $quizSession['passing_score'];
                        $rankClass = match ($rank) { 1 => 'bg-amber-100 text-amber-600 border-amber-200', 2 => 'bg-slate-100 text-slate-500 border-slate-200', 3 => 'bg-orange-100 text-orange-600 border-orange-200', default => 'bg-white text-slate-400 border-slate-200' };
                        ?>
                        <tr class="transition hover:bg-orange-50/30">
                            <td class="px-5 py-4"><div class="mx-auto grid size-10 place-items-center rounded-full border text-sm font-extrabold <?= esc($rankClass) ?>"><?= $rank ?? '&mdash;' ?></div></td>
                            <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="grid size-10 shrink-0 place-items-center rounded-full bg-orange-100 text-sm font-bold text-orange-600"><?= esc(strtoupper(substr($participant['name'], 0, 2))) ?></div><div><p class="text-sm font-bold text-slate-700"><?= esc($participant['name']) ?></p><p class="mt-1 text-[.62rem] text-slate-400"><?= esc(date('d M, H:i', strtotime($participant['joined_at']))) ?></p></div></div></td>
                            <td class="px-5 py-4"><p class="text-xl font-black <?= $hasScore ? 'text-orange-600' : 'text-slate-300' ?>"><?= $hasScore ? number_format((float) $participant['best_score'], 1, ',', '.') : '-' ?></p><?php if ($participant['average_score'] !== null): ?><p class="text-[.62rem] text-slate-400">avg <?= number_format((float) $participant['average_score'], 1, ',', '.') ?></p><?php endif ?></td>
                            <td class="px-5 py-4 text-sm font-bold text-slate-600"><?= esc($formatDuration($participant['best_duration_seconds'])) ?></td>
                            <td class="px-5 py-4"><?php if ($hasScore): ?><span class="inline-flex rounded-full <?= $passed ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' ?> px-3 py-1.5 text-[.68rem] font-bold"><?= $passed ? 'LULUS' : 'BELUM LULUS' ?></span><?php else: ?><span class="inline-flex rounded-full bg-slate-100 px-3 py-1.5 text-[.68rem] font-bold text-slate-500">BELUM SELESAI</span><?php endif ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody></table>
            </div>
        </section>
    </div>
</div>
</main>
<script src="<?= base_url('assets/js/public-leaderboard.js') ?>" defer></script>
</body>
</html>
