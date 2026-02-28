<style>
    .percentage-card {
        border: 1px solid rgba(255,255,255,0.05);
        transition: all 0.3s ease;
    }
    .percentage-card:hover {
        border-color: var(--accent);
        box-shadow: 0 4px 15px rgba(52,152,219,0.15);
    }
    .percentage-input {
        font-size: 1.1rem;
        border: 2px solid #3498db;
        background: rgba(255,255,255,0.05);
        color: var(--text-primary, #fff);
        border-radius: 6px;
    }
    .percentage-input:focus {
        border-color: #2980b9;
        box-shadow: 0 0 0 3px rgba(52,152,219,0.3);
        background: rgba(255,255,255,0.08);
    }
    .percentage-slider-container {
        position: relative;
        height: 36px;
        border-radius: 18px;
        background: #e9ecef;
        overflow: visible;
        border: 2px solid #ced4da;
    }
    .percentage-bar {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        border-radius: 16px;
        transition: width 0.15s ease, background 0.3s ease;
        z-index: 1;
    }
    .percentage-slider {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        z-index: 2;
        opacity: 0;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
    }
    .percentage-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 36px;
        height: 36px;
        cursor: grab;
    }
    .percentage-slider::-moz-range-thumb {
        width: 36px;
        height: 36px;
        cursor: grab;
        border: none;
        background: transparent;
    }
    .percentage-thumb {
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: white;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        border: 2px solid #3498db;
        z-index: 3;
        pointer-events: none;
        transition: left 0.15s ease;
        left: 0%;
    }
</style>
