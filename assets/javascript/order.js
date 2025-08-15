document.addEventListener('DOMContentLoaded', function() {

    // Highlight current status in timeline
    const currentStatus = document.querySelector('.status-badge').classList[1];
    document.querySelectorAll('.step').forEach(step => {
        if (step.classList.contains('active')) {
            const dot = step.querySelector('.dot');
            dot.style.backgroundColor = `var(--${currentStatus.replace('_', '-')})`;
        }
    });
});