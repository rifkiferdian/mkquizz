document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('#toggle-password');
    const password = document.querySelector('#password');

    if (!toggle || !password) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isHidden = password.type === 'password';
        password.type = isHidden ? 'text' : 'password';
        toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan password' : 'Tampilkan password');
    });
});
