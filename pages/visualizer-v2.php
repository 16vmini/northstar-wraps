<?php
/**
 * Wrapinator V2 - Test Version
 * Testing multi-image approach for custom wraps
 * Access via: /pages/visualizer-v2 (no public links)
 */
session_start();
require_once '../includes/config.php';
$page_title = 'Wrapinator V2 (Test)';
$page_description = 'Testing custom wrap feature with multi-image AI.';

require_once '../includes/header.php';
?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Wrapinator V2 <span style="color: #f59e0b;">(TEST)</span></h1>
                <p>Testing custom wrap patterns with multi-image AI</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <span>Wrapinator V2</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Visualizer Section -->
    <section class="visualizer-section">
        <div class="container">
            <div class="visualizer-intro" data-aos="fade-up">
                <h2>Custom Wrap Test</h2>
                <p>Upload your car photo AND a wrap pattern/design. The AI will attempt to apply the pattern to your car.</p>
                <div class="test-notice" style="background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin-top: 15px;">
                    <strong>Test Mode:</strong> This is an experimental feature. Results may vary. No usage limits applied.
                </div>
            </div>

            <div class="visualizer-wrapper" data-aos="fade-up">
                <!-- Left: Upload & Controls -->
                <div class="visualizer-controls">
                    <!-- Car Upload Area -->
                    <div class="upload-section">
                        <h3><i class="fas fa-car"></i> Step 1: Upload Your Car</h3>
                        <div class="upload-area" id="car-upload-area">
                            <input type="file" id="car-upload" accept="image/jpeg,image/png,image/webp" hidden>
                            <div class="upload-placeholder" id="car-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & drop your car photo here</p>
                                <span>or click to browse</span>
                                <small>JPEG, PNG or WebP (max 10MB)</small>
                            </div>
                            <div class="upload-preview" id="car-preview" style="display: none;">
                                <img id="car-preview-image" src="" alt="Your car">
                                <button type="button" class="btn-remove" id="remove-car">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Wrap Pattern Upload Area -->
                    <div class="upload-section">
                        <h3><i class="fas fa-palette"></i> Step 2: Upload Wrap Pattern</h3>
                        <div class="upload-area" id="wrap-upload-area">
                            <input type="file" id="wrap-upload" accept="image/jpeg,image/png,image/webp" hidden>
                            <div class="upload-placeholder" id="wrap-placeholder">
                                <i class="fas fa-image"></i>
                                <p>Drag & drop wrap pattern here</p>
                                <span>or click to browse</span>
                                <small>A seamless pattern or design works best</small>
                            </div>
                            <div class="upload-preview" id="wrap-preview" style="display: none;">
                                <img id="wrap-preview-image" src="" alt="Wrap pattern">
                                <button type="button" class="btn-remove" id="remove-wrap">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="upload-tips">
                            <p><strong>Tips for patterns:</strong></p>
                            <ul>
                                <li>Use a clear, high-contrast pattern</li>
                                <li>Seamless/tileable textures work better</li>
                                <li>Carbon fiber, camo, geometric patterns</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Optional Text Description -->
                    <div class="prompt-section">
                        <h3><i class="fas fa-comment-alt"></i> Step 3: Describe (Optional)</h3>
                        <textarea id="custom-prompt" placeholder="Optionally describe how you want the wrap applied, e.g., 'Apply this carbon fiber pattern to the car hood and side panels'" rows="3" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit;"></textarea>
                    </div>

                    <!-- Generate Button -->
                    <div class="visualizer-actions">
                        <button type="button" class="btn btn-primary btn-lg btn-block" id="generate-btn" disabled>
                            <i class="fas fa-magic"></i> Generate Preview
                        </button>
                        <p style="text-align: center; margin-top: 10px; color: #666; font-size: 0.85rem;">
                            Test mode - no usage limits
                        </p>
                    </div>
                </div>

                <!-- Right: Result -->
                <div class="visualizer-result">
                    <div class="result-container" id="result-container">
                        <div class="result-placeholder" id="result-placeholder">
                            <i class="fas fa-car"></i>
                            <h3>Your Wrapped Car</h3>
                            <p>Upload both images to see the magic happen</p>
                        </div>
                        <div class="result-loading" id="result-loading" style="display: none;">
                            <div class="loading-spinner"></div>
                            <p>Creating your preview...</p>
                            <small>This may take 30-60 seconds</small>
                        </div>
                        <div class="result-image" id="result-image" style="display: none;">
                            <img id="generated-image" src="" alt="Generated preview">
                            <div class="result-overlay">
                                <span class="wrap-badge" id="wrap-badge">Custom Pattern</span>
                            </div>
                        </div>
                        <div class="result-error" id="result-error" style="display: none;">
                            <i class="fas fa-exclamation-circle"></i>
                            <p id="error-message">Something went wrong</p>
                            <button type="button" class="btn btn-outline" id="retry-btn">Try Again</button>
                        </div>
                    </div>

                    <!-- Result Actions -->
                    <div class="result-actions" id="result-actions" style="display: none;">
                        <button type="button" class="btn btn-outline" id="download-btn">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>

                    <!-- Debug Log -->
                    <div id="debug-log" style="margin-top: 20px; padding: 15px; background: #1a1a1a; border-radius: 8px; font-family: monospace; font-size: 0.8rem; color: #7cb518; max-height: 200px; overflow-y: auto; display: none;">
                        <strong style="color: #fff;">Debug Log:</strong><br>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Visualizer Styles -->
    <style>
    .visualizer-section {
        padding: 60px 0;
        background: #f5f5f5;
        overflow-x: hidden;
    }

    .visualizer-intro {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 40px;
    }

    .visualizer-intro h2 {
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .visualizer-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
    }

    .visualizer-controls {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .visualizer-controls h3 {
        font-size: 1.1rem;
        margin-bottom: 15px;
        color: #333;
    }

    .visualizer-controls h3 i {
        color: #7CB518;
        margin-right: 8px;
    }

    .upload-section {
        margin-bottom: 25px;
    }

    .upload-area {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 15px;
        position: relative;
    }

    .upload-area:hover,
    .upload-area.dragover {
        border-color: #7CB518;
        background: rgba(124, 181, 24, 0.05);
    }

    .upload-placeholder i {
        font-size: 2.5rem;
        color: #7CB518;
        margin-bottom: 15px;
    }

    .upload-placeholder p {
        font-size: 1rem;
        margin-bottom: 5px;
    }

    .upload-placeholder span {
        color: #7CB518;
        text-decoration: underline;
    }

    .upload-placeholder small {
        display: block;
        margin-top: 10px;
        color: #999;
    }

    .upload-preview {
        position: relative;
    }

    .upload-preview img {
        max-width: 100%;
        max-height: 150px;
        border-radius: 8px;
    }

    .btn-remove {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #dc2626;
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .upload-tips {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
    }

    .upload-tips p {
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .upload-tips ul {
        margin: 0;
        padding-left: 20px;
        font-size: 0.85rem;
        color: #666;
    }

    .prompt-section {
        margin-bottom: 25px;
    }

    .visualizer-actions {
        margin-top: 20px;
    }

    .visualizer-result {
        position: sticky;
        top: 100px;
    }

    .result-container {
        background: #1a1a1a;
        border-radius: 16px;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .result-placeholder {
        text-align: center;
        color: #666;
        padding: 40px;
    }

    .result-placeholder i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .result-placeholder h3 {
        color: #fff;
        margin-bottom: 10px;
    }

    .result-loading {
        text-align: center;
        color: #fff;
    }

    .loading-spinner {
        width: 60px;
        height: 60px;
        border: 4px solid rgba(255,255,255,0.1);
        border-top-color: #7CB518;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 20px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .result-image {
        width: 100%;
        position: relative;
    }

    .result-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .result-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 15px;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
    }

    .wrap-badge {
        display: inline-block;
        background: #7CB518;
        color: #fff;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .result-error {
        text-align: center;
        color: #dc2626;
        padding: 40px;
    }

    .result-error i {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .result-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }

    .result-actions .btn {
        flex: 1;
    }

    @media (max-width: 1024px) {
        .visualizer-wrapper {
            grid-template-columns: 1fr;
        }

        .visualizer-result {
            position: static;
            order: -1;
        }
    }
    </style>

<?php require_once '../includes/footer.php'; ?>

    <!-- Visualizer V2 Script -->
    <script>
    (function() {
        // Elements
        const carUploadArea = document.getElementById('car-upload-area');
        const carUpload = document.getElementById('car-upload');
        const carPlaceholder = document.getElementById('car-placeholder');
        const carPreview = document.getElementById('car-preview');
        const carPreviewImage = document.getElementById('car-preview-image');
        const removeCar = document.getElementById('remove-car');

        const wrapUploadArea = document.getElementById('wrap-upload-area');
        const wrapUpload = document.getElementById('wrap-upload');
        const wrapPlaceholder = document.getElementById('wrap-placeholder');
        const wrapPreview = document.getElementById('wrap-preview');
        const wrapPreviewImage = document.getElementById('wrap-preview-image');
        const removeWrap = document.getElementById('remove-wrap');

        const customPrompt = document.getElementById('custom-prompt');
        const generateBtn = document.getElementById('generate-btn');

        const resultContainer = document.getElementById('result-container');
        const resultPlaceholder = document.getElementById('result-placeholder');
        const resultLoading = document.getElementById('result-loading');
        const resultImage = document.getElementById('result-image');
        const resultError = document.getElementById('result-error');
        const errorMessage = document.getElementById('error-message');
        const generatedImage = document.getElementById('generated-image');
        const resultActions = document.getElementById('result-actions');
        const downloadBtn = document.getElementById('download-btn');
        const retryBtn = document.getElementById('retry-btn');
        const debugLog = document.getElementById('debug-log');

        // State
        let carImageData = null;
        let wrapImageData = null;

        function log(message) {
            debugLog.style.display = 'block';
            debugLog.innerHTML += new Date().toLocaleTimeString() + ': ' + message + '<br>';
            debugLog.scrollTop = debugLog.scrollHeight;
        }

        // Car upload handlers
        carUploadArea.addEventListener('click', () => carUpload.click());
        carUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            carUploadArea.classList.add('dragover');
        });
        carUploadArea.addEventListener('dragleave', () => {
            carUploadArea.classList.remove('dragover');
        });
        carUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            carUploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                handleCarUpload(e.dataTransfer.files[0]);
            }
        });

        carUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleCarUpload(e.target.files[0]);
            }
        });

        removeCar.addEventListener('click', (e) => {
            e.stopPropagation();
            carImageData = null;
            carPlaceholder.style.display = 'block';
            carPreview.style.display = 'none';
            updateGenerateButton();
        });

        function handleCarUpload(file) {
            if (!file.type.match(/image\/(jpeg|jpg|png|webp)/)) {
                alert('Please upload a JPEG, PNG or WebP image');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('Image must be under 10MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                carImageData = e.target.result;
                carPreviewImage.src = carImageData;
                carPlaceholder.style.display = 'none';
                carPreview.style.display = 'block';
                log('Car image uploaded');
                updateGenerateButton();
            };
            reader.readAsDataURL(file);
        }

        // Wrap upload handlers
        wrapUploadArea.addEventListener('click', () => wrapUpload.click());
        wrapUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            wrapUploadArea.classList.add('dragover');
        });
        wrapUploadArea.addEventListener('dragleave', () => {
            wrapUploadArea.classList.remove('dragover');
        });
        wrapUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            wrapUploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                handleWrapUpload(e.dataTransfer.files[0]);
            }
        });

        wrapUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleWrapUpload(e.target.files[0]);
            }
        });

        removeWrap.addEventListener('click', (e) => {
            e.stopPropagation();
            wrapImageData = null;
            wrapPlaceholder.style.display = 'block';
            wrapPreview.style.display = 'none';
            updateGenerateButton();
        });

        function handleWrapUpload(file) {
            if (!file.type.match(/image\/(jpeg|jpg|png|webp)/)) {
                alert('Please upload a JPEG, PNG or WebP image');
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                alert('Image must be under 10MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                wrapImageData = e.target.result;
                wrapPreviewImage.src = wrapImageData;
                wrapPlaceholder.style.display = 'none';
                wrapPreview.style.display = 'block';
                log('Wrap pattern uploaded');
                updateGenerateButton();
            };
            reader.readAsDataURL(file);
        }

        function updateGenerateButton() {
            generateBtn.disabled = !(carImageData && wrapImageData);
        }

        // Generate
        generateBtn.addEventListener('click', generate);
        retryBtn.addEventListener('click', generate);

        async function generate() {
            if (!carImageData || !wrapImageData) return;

            // Show loading
            resultPlaceholder.style.display = 'none';
            resultImage.style.display = 'none';
            resultError.style.display = 'none';
            resultLoading.style.display = 'block';
            resultActions.style.display = 'none';
            generateBtn.disabled = true;

            log('Starting generation...');

            try {
                const response = await fetch('/api/visualize-v2.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        car_image: carImageData,
                        wrap_image: wrapImageData,
                        prompt: customPrompt.value.trim()
                    })
                });

                const data = await response.json();
                log('Response received: ' + (data.success ? 'SUCCESS' : 'ERROR'));

                if (!response.ok || data.error) {
                    throw new Error(data.error || data.message || 'Generation failed');
                }

                // Show result
                resultLoading.style.display = 'none';
                generatedImage.src = data.image;
                resultImage.style.display = 'block';
                resultActions.style.display = 'flex';
                log('Image generated successfully!');

                if (data.debug) {
                    log('Model used: ' + data.debug.model);
                    log('Prompt: ' + data.debug.prompt);
                }

            } catch (error) {
                resultLoading.style.display = 'none';
                errorMessage.textContent = error.message;
                resultError.style.display = 'block';
                log('ERROR: ' + error.message);
            }

            generateBtn.disabled = false;
        }

        // Download
        downloadBtn.addEventListener('click', () => {
            const link = document.createElement('a');
            link.download = 'wrap-preview-v2.png';
            link.href = generatedImage.src;
            link.click();
        });
    })();
    </script>
