document.addEventListener('DOMContentLoaded', () => {
    const pinInput = document.querySelector('#quiz-pin');
    if (!pinInput) return;

    pinInput.addEventListener('input', () => {
        pinInput.value = pinInput.value.replace(/\D/g, '').slice(0, 6);
    });
});
