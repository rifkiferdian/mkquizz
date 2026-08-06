document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('#leaderboard-table-scroll');

    if (!container || container.scrollHeight <= container.clientHeight) {
        return;
    }

    let isPaused = false;
    let pauseUntil = performance.now() + 2000;
    let previousTime = performance.now();

    const pause = () => {
        isPaused = true;
    };

    const resume = () => {
        isPaused = false;
        pauseUntil = performance.now() + 1500;
    };

    container.addEventListener('mouseenter', pause);
    container.addEventListener('mouseleave', resume);
    container.addEventListener('focusin', pause);
    container.addEventListener('focusout', resume);
    container.addEventListener('touchstart', pause, { passive: true });
    container.addEventListener('touchend', resume, { passive: true });
    container.addEventListener('wheel', () => {
        pauseUntil = performance.now() + 3000;
    }, { passive: true });

    const autoScroll = (currentTime) => {
        const elapsedTime = currentTime - previousTime;

        if (!isPaused && currentTime >= pauseUntil) {
            container.scrollTop += elapsedTime * 0.025;

            const reachedBottom = container.scrollTop + container.clientHeight >= container.scrollHeight - 1;

            if (reachedBottom) {
                container.scrollTop = 0;
                pauseUntil = currentTime + 1800;
            }
        }

        previousTime = currentTime;
        window.requestAnimationFrame(autoScroll);
    };

    window.requestAnimationFrame(autoScroll);
});
