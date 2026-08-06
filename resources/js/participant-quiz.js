document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('#participant-quiz-form');

    if (!form) {
        return;
    }

    const timer = document.querySelector('#quiz-timer');
    const progressText = document.querySelector('#quiz-progress-text');
    const progressBar = document.querySelector('#quiz-progress-bar');
    const submitButton = document.querySelector('#quiz-submit-button');
    const totalQuestions = Number(form.dataset.totalQuestions || 0);
    const remainingSeconds = Math.max(0, Number(form.dataset.remainingSeconds || 0));
    const deadline = Date.now() + (remainingSeconds * 1000);
    let isSubmitting = false;

    const updateProgress = () => {
        const answeredNames = new Set(
            [...form.querySelectorAll('[data-answer-input]:checked')].map((input) => input.name),
        );
        const answered = answeredNames.size;
        const percentage = totalQuestions > 0 ? (answered / totalQuestions) * 100 : 0;

        progressText.textContent = `${answered}/${totalQuestions}`;
        progressBar.style.width = `${percentage}%`;

        form.querySelectorAll('[data-question-card]').forEach((card) => {
            card.classList.toggle('is-answered', Boolean(card.querySelector('[data-answer-input]:checked')));
        });

        return answered;
    };

    const submitAutomatically = () => {
        if (isSubmitting) {
            return;
        }

        isSubmitting = true;
        submitButton.disabled = true;
        submitButton.querySelector('span').textContent = 'Mengirim...';
        form.submit();
    };

    const updateTimer = () => {
        const seconds = Math.max(0, Math.ceil((deadline - Date.now()) / 1000));
        const minutesPart = Math.floor(seconds / 60).toString().padStart(2, '0');
        const secondsPart = (seconds % 60).toString().padStart(2, '0');

        timer.textContent = `${minutesPart}:${secondsPart}`;
        timer.closest('.participant-timer').classList.toggle('is-urgent', seconds <= 60);

        if (seconds === 0) {
            submitAutomatically();
        }
    };

    form.addEventListener('change', updateProgress);
    form.addEventListener('submit', (event) => {
        if (isSubmitting) {
            return;
        }

        const answered = updateProgress();
        const unanswered = totalQuestions - answered;

        if (unanswered > 0 && !window.confirm(`Masih ada ${unanswered} pertanyaan yang belum dijawab. Tetap kirim jawaban?`)) {
            event.preventDefault();
            const firstEmptyQuestion = [...form.querySelectorAll('[data-question-card]')]
                .find((card) => !card.querySelector('[data-answer-input]:checked'));
            firstEmptyQuestion?.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return;
        }

        isSubmitting = true;
        submitButton.disabled = true;
        submitButton.querySelector('span').textContent = 'Mengirim...';
    });

    updateProgress();
    updateTimer();
    window.setInterval(updateTimer, 1000);
});
