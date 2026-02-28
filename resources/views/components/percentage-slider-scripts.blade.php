<script>
function getBarColor(val) {
    if (val <= 25) return 'linear-gradient(90deg, #e74c3c, #e67e22)';
    if (val <= 50) return 'linear-gradient(90deg, #e67e22, #f1c40f)';
    if (val <= 75) return 'linear-gradient(90deg, #f1c40f, #2ecc71)';
    return 'linear-gradient(90deg, #2ecc71, #27ae60)';
}

function updateSliderVisuals(slider, input, bar, thumb) {
    const val = parseInt(slider.value) || 0;
    bar.style.width = val + '%';
    bar.style.background = getBarColor(val);
    input.value = val;
    if (thumb) thumb.style.left = val + '%';
}

// Initialize all sliders
document.querySelectorAll('.percentage-slider').forEach(slider => {
    const inputId = slider.dataset.input;
    const barId = slider.dataset.bar;
    const input = document.getElementById(inputId);
    const bar = document.getElementById(barId);
    const thumb = bar.parentElement.querySelector('.percentage-thumb');

    // Initial state
    updateSliderVisuals(slider, input, bar, thumb);

    // Slider → input sync
    slider.addEventListener('input', () => updateSliderVisuals(slider, input, bar, thumb));

    // Input → slider sync
    input.addEventListener('input', () => {
        let val = parseInt(input.value);
        if (isNaN(val)) val = 0;
        if (val < 0) val = 0;
        if (val > 100) val = 100;
        slider.value = val;
        bar.style.width = val + '%';
        bar.style.background = getBarColor(val);
        if (thumb) thumb.style.left = val + '%';
    });

    input.addEventListener('blur', () => {
        let val = parseInt(input.value);
        if (isNaN(val) || val < 0) val = 0;
        if (val > 100) val = 100;
        input.value = val;
        slider.value = val;
        updateSliderVisuals(slider, input, bar, thumb);
    });
});
</script>
