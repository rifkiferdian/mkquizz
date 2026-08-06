import QRCode from 'qrcode';

document.addEventListener('DOMContentLoaded', async () => {
    const root = document.querySelector('#participant-qr-card');
    if (!root) return;

    const canvas = root.querySelector('#participant-qr-code');
    const download = root.querySelector('#download-participant-qr');
    const copyButton = root.querySelector('#copy-participant-url');
    const copyStatus = root.querySelector('#participant-copy-status');
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
