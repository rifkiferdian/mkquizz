document.addEventListener('DOMContentLoaded', () => {
    const optionsContainer = document.querySelector('#question-options');
    const optionTemplate = document.querySelector('#question-option-template');
    const addOptionButton = document.querySelector('#add-option-button');
    const questionType = document.querySelector('#question_type');
    const optionCounter = document.querySelector('#option-counter');

    document.querySelectorAll('.delete-question-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const questionText = form.dataset.questionText || 'pertanyaan ini';
            if (!window.confirm('Hapus “' + questionText + '”? Semua pilihan jawabannya juga akan dihapus.')) {
                event.preventDefault();
            }
        });
    });

    if (!optionsContainer || !optionTemplate || !addOptionButton || !questionType) {
        return;
    }

    const updateRows = () => {
        const rows = [...optionsContainer.querySelectorAll('.question-option-row')];
        const isTrueFalse = questionType.value === 'TRUE_FALSE';

        rows.forEach((row, index) => {
            const key = String.fromCharCode(65 + index);
            const radio = row.querySelector('input[type="radio"]');
            const input = row.querySelector('textarea');
            const removeButton = row.querySelector('.remove-option-button');

            row.querySelector('.question-option-key').textContent = key;
            radio.value = String(index);
            input.placeholder = 'Pilihan ' + key;
            removeButton.setAttribute('aria-label', 'Hapus pilihan ' + key);
            removeButton.hidden = isTrueFalse || rows.length <= 2;
            input.readOnly = isTrueFalse;
        });

        addOptionButton.hidden = isTrueFalse || rows.length >= 5;
        optionCounter.textContent = rows.length + ' pilihan';
    };

    const appendOption = (value = '', isCorrect = false) => {
        const fragment = optionTemplate.content.cloneNode(true);
        fragment.querySelector('textarea').value = value;
        fragment.querySelector('input[type="radio"]').checked = isCorrect;
        optionsContainer.appendChild(fragment);
        updateRows();
    };

    const rebuildOptions = (values) => {
        optionsContainer.innerHTML = '';
        values.forEach((value, index) => appendOption(value, index === 0));
        updateRows();
    };

    addOptionButton.addEventListener('click', () => {
        if (optionsContainer.children.length < 5) {
            appendOption();
        }
    });

    optionsContainer.addEventListener('click', (event) => {
        const button = event.target.closest('.remove-option-button');
        if (button && optionsContainer.children.length > 2) {
            button.closest('.question-option-row').remove();
            updateRows();
        }
    });

    questionType.addEventListener('change', () => {
        if (questionType.value === 'TRUE_FALSE') {
            rebuildOptions(['Benar', 'Salah']);
        } else {
            rebuildOptions(['', '', '', '']);
        }
    });

    updateRows();
});
