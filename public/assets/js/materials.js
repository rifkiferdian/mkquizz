document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.delete-material-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const materialName = form.dataset.materialName || 'material ini';

            if (!window.confirm(`Hapus material “${materialName}”? Tindakan ini tidak dapat dibatalkan.`)) {
                event.preventDefault();
            }
        });
    });
});
