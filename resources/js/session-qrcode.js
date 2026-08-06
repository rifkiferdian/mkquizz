import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', async () => {
    const root = document.querySelector('#participant-qr-card');
    if (!root) return;

    const canvas = root.querySelector('#participant-qr-code');
    const download = root.querySelector('#download-participant-qr');
    const copyButton = root.querySelector('#copy-participant-url');
    const copyStatus = root.querySelector('#participant-copy-status');
    const pinValue = root.querySelector('#participant-pin-value');
    const pinToggle = root.querySelector('#toggle-participant-pin');
    const validityCard = root.querySelector('#pin-validity-card');
    const url = root.dataset.url;

    try {
        await QRCode.toCanvas(canvas, url, {
            width: 220,
            margin: 2,
            errorCorrectionLevel: 'M',
            color: { dark: '#19212f', light: '#ffffff' },
        });
        if (download) {
            download.href = canvas.toDataURL('image/png');
            download.classList.remove('pointer-events-none', 'opacity-50');
        }
    } catch {
        canvas.hidden = true;
        root.querySelector('#participant-qr-error').classList.remove('hidden');
    }

    if (pinValue && pinToggle) {
        pinToggle.addEventListener('click', () => {
            const isVisible = pinToggle.getAttribute('aria-pressed') === 'true';
            const nextVisible = !isVisible;

            pinValue.textContent = nextVisible ? pinValue.dataset.pin : '••••••';
            pinToggle.setAttribute('aria-pressed', String(nextVisible));
            pinToggle.setAttribute('aria-label', nextVisible ? 'Sembunyikan PIN quiz' : 'Tampilkan PIN quiz');
            pinToggle.querySelector('[data-eye-open]').classList.toggle('hidden', nextVisible);
            pinToggle.querySelector('[data-eye-closed]').classList.toggle('hidden', !nextVisible);
        });
    }

    if (validityCard) {
        const validFrom = new Date(validityCard.dataset.validFrom).getTime();
        const validUntil = new Date(validityCard.dataset.validUntil).getTime();
        const status = validityCard.querySelector('#pin-validity-status');
        const countdown = validityCard.querySelector('#pin-validity-countdown');
        const message = validityCard.querySelector('#pin-validity-message');

        const formatTime = (totalSeconds) => {
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            return [hours, minutes, seconds]
                .map((part) => String(part).padStart(2, '0'))
                .join(':');
        };

        const updatePinValidity = () => {
            const now = Date.now();
            validityCard.classList.remove('is-active', 'is-waiting', 'is-expired');

            if (now < validFrom) {
                validityCard.classList.add('is-waiting');
                status.textContent = 'Belum berlaku';
                countdown.textContent = formatTime(Math.ceil((validFrom - now) / 1000));
                message.textContent = 'PIN mulai berlaku dalam';
            } else if (now <= validUntil) {
                validityCard.classList.add('is-active');
                status.textContent = 'Aktif';
                countdown.textContent = formatTime(Math.ceil((validUntil - now) / 1000));
                message.textContent = 'Sisa waktu PIN';
            } else {
                validityCard.classList.add('is-expired');
                status.textContent = 'Kedaluwarsa';
                countdown.textContent = '00:00:00';
                message.textContent = 'Masa berlaku telah berakhir';
            }
        };

        updatePinValidity();
        window.setInterval(updatePinValidity, 1000);
    }

    if (!copyButton || !copyStatus) return;

    copyButton.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(url);
        } catch {
            const temporaryInput = document.createElement('textarea');
            temporaryInput.value = url;
            temporaryInput.style.position = 'fixed';
            temporaryInput.style.opacity = '0';
            document.body.appendChild(temporaryInput);
            temporaryInput.select();
            document.execCommand('copy');
            temporaryInput.remove();
        }

        copyStatus.textContent = 'Link berhasil disalin';
        copyButton.classList.add('is-copied');
        window.setTimeout(() => {
            copyStatus.textContent = '';
            copyButton.classList.remove('is-copied');
        }, 2200);
    });
});
