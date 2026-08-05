<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dashboard administrator MKQuizz">
    <title><?= esc($title ?? 'Admin') ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?php $activeMenu = service('uri')->getSegment(2) ?: 'dashboard'; ?>
<div class="dashboard-shell">
    <aside class="sidebar fixed inset-y-0 left-0 z-20 flex flex-col text-slate-700">
        <div class="flex h-20 items-center gap-3 border-b border-slate-100 px-5">
            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-orange-500 text-sm font-black text-white shadow-lg shadow-orange-500/20">MQ</div>
            <div class="brand-copy">
                <p class="text-lg font-extrabold tracking-tight text-slate-900">MK<span class="text-orange-500">Quizz</span></p>
                <p class="text-[.65rem] uppercase tracking-[.2em] text-slate-400">Admin panel</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1 px-3 py-7" aria-label="Navigasi admin">
            <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= $activeMenu === 'dashboard' ? 'active' : '' ?> flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold">
                <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                <span class="sidebar-label">Dashboard</span>
            </a>
            <a href="<?= site_url('admin/materials') ?>" class="nav-link <?= $activeMenu === 'materials' ? 'active' : '' ?> flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold">
                <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 19V5m-3 3 3-3 3 3M5 5h10a2 2 0 0 1 2 2v12H7a2 2 0 0 1-2-2V5Z"/></svg>
                <span class="sidebar-label">Materi</span>
            </a>
            <a href="<?= site_url('admin/questions') ?>" class="nav-link <?= $activeMenu === 'questions' ? 'active' : '' ?> flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold">
                <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 18h.01M9.2 9a3 3 0 1 1 4.7 2.5c-1.2.8-1.9 1.2-1.9 2.5M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20Z"/></svg>
                <span class="sidebar-label">Pertanyaan</span>
            </a>
            <a href="<?= site_url('admin/quizzes') ?>" class="nav-link <?= $activeMenu === 'quizzes' ? 'active' : '' ?> flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold">
                <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 11h6M9 15h4M8 3h8l3 3v15H5V3h3Z"/></svg>
                <span class="sidebar-label">Quiz</span>
            </a>
            <?php foreach ([['Sesi Quiz', 'M8 2v3m8-3v3M3 9h18M5 4h14a2 2 0 0 1 2 2v14H3V6a2 2 0 0 1 2-2Z'], ['Peserta', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm13 10v-2a4 4 0 0 0-3-3.87']] as [$label, $path]): ?>
                <span class="nav-link flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium opacity-70" title="Modul berikutnya">
                    <svg class="size-5 shrink-0" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="<?= esc($path) ?>"/></svg>
                    <span class="sidebar-label"><?= esc($label) ?></span>
                </span>
            <?php endforeach ?>
        </nav>

        <div class="border-t border-slate-100 p-4">
            <div class="flex items-center gap-3 rounded-xl bg-orange-50/70 p-2.5">
                <div class="grid size-9 shrink-0 place-items-center rounded-full bg-orange-100 text-xs font-bold text-orange-600"><?= esc(strtoupper(substr((string) session('admin_name'), 0, 2))) ?></div>
                <div class="admin-copy min-w-0 flex-1">
                    <p class="truncate text-xs font-bold text-slate-700"><?= esc(session('admin_name')) ?></p>
                    <p class="truncate text-[.65rem] text-slate-400"><?= esc(session('admin_email')) ?></p>
                </div>
            </div>
        </div>
    </aside>

    <main class="dashboard-main ml-[16.5rem] min-h-screen">
        <header class="dashboard-header sticky top-0 z-10 flex h-20 items-center justify-between px-5 backdrop-blur md:px-8">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight text-slate-900"><?= esc($title ?? 'Dashboard') ?></h1>
                <p class="mt-1 hidden text-xs text-slate-500 sm:block"><?= esc($subtitle ?? 'Kelola seluruh aktivitas quiz dari satu tempat.') ?></p>
            </div>
            <div class="flex items-center gap-3">
                <span class="hidden rounded-full bg-orange-50 px-3 py-1.5 text-[.65rem] font-bold uppercase tracking-wider text-orange-600 sm:inline-block"><?= esc(session('admin_role')) ?></span>
                <form action="<?= site_url('admin/logout') ?>" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-orange-200 hover:text-orange-600">
                        <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17l5-5-5-5m5 5H3m10-9h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            </div>
        </header>

        <?= $this->renderSection('content') ?>
    </main>
</div>
<?= $this->renderSection('scripts') ?>
</body>
</html>
