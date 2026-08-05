<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login administrator MKQuizz">
    <title><?= esc($title) ?> | MKQuizz</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<?php $errors = session('errors') ?? []; ?>
<main class="login-page">
    <section class="auth-card" aria-labelledby="login-title">
        <div class="brand-mark" aria-label="Logo MKQuizz">
            <div class="text-center leading-none">
                <span class="block text-[1.05rem] font-black tracking-tight">MQ</span>
                <span class="block text-[.42rem] font-bold tracking-wide">MKQUIZZ</span>
            </div>
        </div>

        <div class="mt-5 text-center">
            <p class="text-xs font-bold uppercase tracking-[.22em] text-orange-500">Admin Portal</p>
            <h1 id="login-title" class="mt-2 text-[1.65rem] font-extrabold tracking-tight text-slate-900">Welcome Back</h1>
            <p class="mt-2 text-sm text-slate-500">Login untuk mengelola quiz dan materi</p>
        </div>

        <?php if (session('error')): ?>
            <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                <?= esc(session('error')) ?>
            </div>
        <?php endif ?>

        <?php if (session('success')): ?>
            <div class="mt-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700" role="status">
                <?= esc(session('success')) ?>
            </div>
        <?php endif ?>

        <form class="mt-7" action="<?= site_url('admin/login') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div>
                <label for="email" class="mb-2 block text-xs font-bold text-slate-700">Email Address</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/></svg>
                    <input id="email" name="email" type="email" value="<?= old('email') ?>" autocomplete="email" placeholder="admin@mkquizz.edu" class="form-input <?= isset($errors['email']) ? 'has-error' : '' ?>" required autofocus>
                </div>
                <?php if (isset($errors['email'])): ?>
                    <p class="mt-1.5 text-xs text-red-600"><?= esc($errors['email']) ?></p>
                <?php endif ?>
            </div>

            <div class="mt-5">
                <label for="password" class="mb-2 block text-xs font-bold text-slate-700">Password</label>
                <div class="relative">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-slate-400" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="5" y="10" width="14" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                    <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan password" class="form-input <?= isset($errors['password']) ? 'has-error' : '' ?>" required>
                    <button id="toggle-password" type="button" class="absolute right-3.5 top-1/2 -translate-y-1/2 cursor-pointer border-0 bg-transparent p-1 text-slate-400 hover:text-orange-500" aria-label="Tampilkan password">
                        <svg id="eye-icon" class="size-5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                    </button>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <p class="mt-1.5 text-xs text-red-600"><?= esc($errors['password']) ?></p>
                <?php endif ?>
            </div>

            <div class="my-5 flex items-center justify-between text-xs">
                <label class="flex cursor-pointer items-center gap-2 text-slate-500">
                    <input type="checkbox" name="remember" value="1" class="size-4 rounded border-slate-300 accent-orange-500">
                    Remember me
                </label>
                <span class="font-semibold text-orange-600">Secure login</span>
            </div>

            <button type="submit" class="primary-button">
                <span>Login</span>
                <svg class="size-4" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M14 7l5 5-5 5"/></svg>
            </button>
        </form>

        <div class="mt-7 border-t border-slate-100 pt-5 text-center text-[.7rem] text-slate-500">
            <span class="inline-flex items-center gap-1.5">
                <svg class="size-3.5" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 5 6v5c0 4.6 2.7 8 7 10 4.3-2 7-5.4 7-10V6l-7-3Z"/><path d="m9.5 12 1.5 1.5 3.5-4"/></svg>
                Secure Admin Access
            </span>
        </div>
    </section>
</main>

<script src="<?= base_url('assets/js/login.js') ?>" defer></script>
</body>
</html>
