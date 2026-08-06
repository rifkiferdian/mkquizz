document.addEventListener('DOMContentLoaded', () => {
    const material = document.querySelector('#material_id');
    const search = document.querySelector('#question-search');
    const choices = [...document.querySelectorAll('.quiz-question-choice')];
    const empty = document.querySelector('#quiz-question-empty');
    const counter = document.querySelector('#selected-question-count');

    if (!material || !search || !empty || !counter) return;

    const refreshCounter = () => {
        const selected = choices.filter((choice) => choice.querySelector('input').checked).length;
        counter.textContent = `${selected} dipilih`;
    };

    const filterQuestions = () => {
        const materialId = material.value;
        const keyword = search.value.trim().toLocaleLowerCase('id-ID');
        let visible = 0;

        choices.forEach((choice) => {
            const matchesMaterial = materialId !== '' && choice.dataset.material === materialId;
            const matchesSearch = keyword === '' || choice.dataset.search.includes(keyword);
            const show = matchesMaterial && matchesSearch;
            choice.hidden = !show;
            visible += show ? 1 : 0;
        });

        empty.classList.toggle('hidden', visible > 0);
    };

    material.addEventListener('change', () => {
        choices.forEach((choice) => {
            const checkbox = choice.querySelector('input');
            if (choice.dataset.material !== material.value) checkbox.checked = false;
        });
        filterQuestions();
        refreshCounter();
    });
    search.addEventListener('input', filterQuestions);
    choices.forEach((choice) => choice.querySelector('input').addEventListener('change', refreshCounter));

    filterQuestions();
    refreshCounter();
});
