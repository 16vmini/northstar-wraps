<?php
session_start();
require_once '../includes/config.php';
$page_title = 'Wrapinator';
$page_description = 'See how your car would look with a new vinyl wrap. Upload a photo and preview different colours and finishes instantly with our AI-powered Wrapinator.';

// Load wrap options
$wraps_json = file_get_contents(__DIR__ . '/../assets/wraps/wraps.json');
$wraps_data = json_decode($wraps_json, true);

require_once '../includes/header.php';
?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Wrapinator</h1>
                <p>See your car in a new colour before you commit</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <span>Wrapinator</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Visualizer Section -->
    <section class="visualizer-section">
        <div class="container">
            <div class="visualizer-intro" data-aos="fade-up">
                <h2>Preview Your Dream Wrap</h2>
                <p>Upload a photo of your car and select a wrap colour or finish to see how it would look. Our AI-powered Wrapinator creates a realistic preview in seconds.</p>
                <a href="/wrapinator-gallery" class="gallery-link-btn">
                    <i class="fas fa-images"></i> View Community Gallery
                </a>
            </div>

            <div class="visualizer-wrapper" data-aos="fade-up">
                <!-- Left: Upload & Controls -->
                <div class="visualizer-controls">
                    <!-- Upload Area -->
                    <div class="upload-section">
                        <h3><i class="fas fa-camera"></i> Step 1: Upload Your Car</h3>
                        <div class="upload-area" id="upload-area">
                            <input type="file" id="car-upload" accept="image/jpeg,image/png,image/webp" hidden>
                            <div class="upload-placeholder" id="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Drag & drop your car photo here</p>
                                <span>or click to browse</span>
                                <small>JPEG, PNG or WebP (max 10MB)</small>
                            </div>
                            <div class="upload-preview" id="upload-preview" style="display: none;">
                                <img id="preview-image" src="" alt="Your car">
                                <button type="button" class="btn-remove" id="remove-image">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="upload-tips">
                            <p><strong>Tips for best results:</strong></p>
                            <ul>
                                <li>Use a clear, well-lit photo</li>
                                <li>Side or 3/4 angle works best</li>
                                <li>Avoid busy backgrounds if possible</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Wrap Selection -->
                    <div class="wrap-selection">
                        <h3><i class="fas fa-palette"></i> Step 2: Choose Your Wrap</h3>

                        <!-- Category Tabs -->
                        <div class="wrap-categories">
                            <?php foreach ($wraps_data['categories'] as $index => $category): ?>
                            <button type="button" class="category-tab <?php echo $index === 0 ? 'active' : ''; ?>"
                                    data-category="<?php echo $category['id']; ?>">
                                <?php echo $category['name']; ?>
                            </button>
                            <?php endforeach; ?>
                            <button type="button" class="category-tab" data-category="custom">
                                <i class="fas fa-upload"></i> Custom
                            </button>
                        </div>

                        <!-- Wrap Options -->
                        <div class="wrap-options-container">
                            <?php foreach ($wraps_data['categories'] as $index => $category): ?>
                            <div class="wrap-options <?php echo $index === 0 ? 'active' : ''; ?>"
                                 data-category="<?php echo $category['id']; ?>">
                                <?php foreach ($category['wraps'] as $wrap): ?>
                                <button type="button" class="wrap-option" data-wrap-id="<?php echo $wrap['id']; ?>"
                                        data-wrap-name="<?php echo htmlspecialchars($wrap['name']); ?>"
                                        title="<?php echo htmlspecialchars($wrap['name']); ?>">
                                    <span class="wrap-swatch" style="background-color: <?php echo $wrap['hex']; ?>;
                                        <?php if ($wrap['image']): ?>background-image: url('/assets/wraps/<?php echo $wrap['image']; ?>');<?php endif; ?>">
                                    </span>
                                    <span class="wrap-name"><?php echo $wrap['name']; ?></span>
                                    <span class="wrap-finish"><?php echo $wrap['finish']; ?></span>
                                </button>
                                <?php endforeach; ?>
                            </div>
                            <?php endforeach; ?>

                            <!-- Custom Upload -->
                            <div class="wrap-options" data-category="custom">
                                <div class="custom-wrap-upload">
                                    <input type="file" id="custom-wrap-upload" accept="image/jpeg,image/png,image/webp" hidden>
                                    <div class="custom-upload-area" id="custom-upload-area">
                                        <i class="fas fa-image"></i>
                                        <p>Upload a wrap pattern or texture</p>
                                        <small>PNG with pattern works best</small>
                                    </div>
                                    <div class="custom-preview" id="custom-preview" style="display: none;">
                                        <img id="custom-preview-image" src="" alt="Custom wrap">
                                        <button type="button" class="btn-remove" id="remove-custom">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Generate Button -->
                    <div class="visualizer-actions">
                        <button type="button" class="btn btn-primary btn-lg btn-block" id="generate-btn" disabled>
                            <i class="fas fa-magic"></i> Generate Preview
                        </button>
                        <div class="usage-info" id="usage-info">
                            <span id="usage-text">2 free previews remaining</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Result -->
                <div class="visualizer-result">
                    <div class="result-container" id="result-container">
                        <div class="result-placeholder" id="result-placeholder">
                            <i class="fas fa-car"></i>
                            <h3>Your Wrapped Car</h3>
                            <p>Upload a photo and select a wrap to see the magic happen</p>
                        </div>
                        <div class="result-loading" id="result-loading" style="display: none;">
                            <div class="loading-spinner"></div>
                            <p>Creating your preview...</p>
                            <small>This may take 15-30 seconds</small>
                        </div>
                        <div class="result-image" id="result-image" style="display: none;">
                            <img id="generated-image" src="" alt="Generated preview">
                            <div class="result-overlay">
                                <span class="wrap-badge" id="wrap-badge"></span>
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
                        <button type="button" class="btn btn-outline" id="share-btn">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                        <a href="/pages/contact.php?service=full-wrap" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Get a Quote
                        </a>
                    </div>

                    <!-- Share Modal -->
                    <div class="share-modal" id="share-modal" style="display: none;">
                        <div class="modal-overlay share-modal-overlay"></div>
                        <div class="modal-content share-modal-content">
                            <button type="button" class="modal-close" id="share-modal-close">&times;</button>
                            <h3>Share Your Preview</h3>
                            <p>Show off your wrapped car on social media!</p>
                            <div class="share-buttons-grid">
                                <a href="#" id="share-facebook" target="_blank" rel="noopener" class="share-btn-modal share-facebook">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </a>
                                <a href="#" id="share-twitter" target="_blank" rel="noopener" class="share-btn-modal share-twitter">
                                    <i class="fab fa-x-twitter"></i> X
                                </a>
                                <a href="#" id="share-whatsapp" target="_blank" rel="noopener" class="share-btn-modal share-whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <button type="button" class="share-btn-modal share-copy-modal" id="copy-link-btn">
                                    <i class="fas fa-link"></i> Copy Link
                                </button>
                            </div>
                            <div class="share-link-input">
                                <input type="text" id="share-link-input" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Capture Modal -->
            <div class="modal" id="email-modal" style="display: none;">
                <div class="modal-overlay"></div>
                <div class="modal-content">
                    <button type="button" class="modal-close" id="modal-close">&times;</button>
                    <div class="modal-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Unlock More Previews</h3>
                    <p>Enter your email to continue using the visualizer and receive exclusive offers.</p>
                    <form id="email-form">
                        <div class="form-group">
                            <input type="email" id="email-input" placeholder="your@email.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-unlock"></i> Unlock 10 More Previews
                        </button>
                    </form>
                    <small class="modal-disclaimer">We respect your privacy. Unsubscribe anytime.</small>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works-section">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">How It Works</h2>
            <div class="steps-grid">
                <div class="step-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-number">1</div>
                    <div class="step-icon"><i class="fas fa-camera"></i></div>
                    <h3>Upload Your Photo</h3>
                    <p>Take a clear photo of your car from the side or at a 3/4 angle</p>
                </div>
                <div class="step-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-number">2</div>
                    <div class="step-icon"><i class="fas fa-palette"></i></div>
                    <h3>Choose a Wrap</h3>
                    <p>Browse our collection of colours, finishes, and patterns</p>
                </div>
                <div class="step-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-number">3</div>
                    <div class="step-icon"><i class="fas fa-magic"></i></div>
                    <h3>See the Result</h3>
                    <p>Our AI generates a realistic preview of your wrapped car</p>
                </div>
                <div class="step-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="step-number">4</div>
                    <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Get a Quote</h3>
                    <p>Love what you see? Request a quote to make it real</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Disclaimer -->
    <section class="disclaimer-section">
        <div class="container">
            <div class="disclaimer-box" data-aos="fade-up">
                <i class="fas fa-info-circle"></i>
                <p><strong>Note:</strong> The visualizer uses AI to generate previews and results are approximations. Actual wrap colours and finishes may vary. We recommend viewing physical samples before making your final decision.</p>
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

    .visualizer-intro p {
        margin-bottom: 20px;
    }

    .gallery-link-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: #1a1a1a;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .gallery-link-btn:hover {
        background: #333;
        color: #fff;
    }

    .visualizer-wrapper {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        align-items: start;
        max-width: 100%;
        overflow: hidden;
    }

    /* Controls */
    .visualizer-controls {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        max-width: 100%;
        overflow: hidden;
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

    /* Upload Area */
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
        font-size: 3rem;
        color: #7CB518;
        margin-bottom: 15px;
    }

    .upload-placeholder p {
        font-size: 1.1rem;
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
        max-height: 200px;
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
        margin-bottom: 25px;
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

    /* Wrap Selection */
    .wrap-selection {
        margin-bottom: 25px;
    }

    .wrap-categories {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 15px;
    }

    .category-tab {
        padding: 8px 16px;
        border: 1px solid #ddd;
        border-radius: 20px;
        background: #fff;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .category-tab:hover {
        border-color: #7CB518;
    }

    .category-tab.active {
        background: #7CB518;
        border-color: #7CB518;
        color: #fff;
    }

    .wrap-options-container {
        min-height: 150px;
    }

    .wrap-options {
        display: none;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px;
    }

    .wrap-options.active {
        display: grid;
    }

    .wrap-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
        border: 2px solid #eee;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .wrap-option:hover {
        border-color: #7CB518;
        transform: translateY(-2px);
    }

    .wrap-option.selected {
        border-color: #7CB518;
        background: rgba(124, 181, 24, 0.1);
    }

    .wrap-swatch {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        margin-bottom: 8px;
        border: 1px solid #ddd;
        background-size: cover;
        background-position: center;
    }

    .wrap-name {
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
    }

    .wrap-finish {
        font-size: 0.65rem;
        color: #999;
    }

    /* Custom Upload */
    .custom-wrap-upload {
        grid-column: 1 / -1;
    }

    .custom-upload-area {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .custom-upload-area:hover {
        border-color: #7CB518;
    }

    .custom-upload-area i {
        font-size: 2rem;
        color: #7CB518;
        margin-bottom: 10px;
    }

    .custom-preview {
        position: relative;
        display: inline-block;
    }

    .custom-preview img {
        max-width: 150px;
        max-height: 100px;
        border-radius: 8px;
    }

    /* Actions */
    .visualizer-actions {
        margin-top: 20px;
    }

    .usage-info {
        text-align: center;
        margin-top: 15px;
        font-size: 0.85rem;
        color: #666;
    }

    /* Result */
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
        max-width: 100%;
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
        max-width: 100%;
        height: auto;
        display: block;
        object-fit: contain;
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

    /* Modal */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.7);
    }

    .modal-content {
        position: relative;
        background: #fff;
        border-radius: 16px;
        padding: 40px;
        max-width: 400px;
        width: 90%;
        text-align: center;
    }

    .modal-close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #999;
    }

    .modal-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #7CB518, #5a8a12);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .modal-icon i {
        font-size: 2rem;
        color: #fff;
    }

    .modal-content h3 {
        margin-bottom: 10px;
    }

    .modal-content p {
        color: #666;
        margin-bottom: 20px;
    }

    .modal-content .form-group {
        margin-bottom: 15px;
    }

    .modal-content input {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
    }

    .modal-disclaimer {
        display: block;
        margin-top: 15px;
        color: #999;
        font-size: 0.8rem;
    }

    /* How It Works */
    .how-it-works-section {
        padding: 80px 0;
        background: #fff;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
        margin-top: 40px;
    }

    .step-card {
        text-align: center;
        padding: 30px 20px;
        background: #f9f9f9;
        border-radius: 12px;
        position: relative;
    }

    .step-number {
        position: absolute;
        top: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 30px;
        height: 30px;
        background: #7CB518;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .step-icon {
        width: 60px;
        height: 60px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .step-icon i {
        font-size: 1.5rem;
        color: #7CB518;
    }

    .step-card h3 {
        font-size: 1rem;
        margin-bottom: 10px;
    }

    .step-card p {
        font-size: 0.9rem;
        color: #666;
        margin: 0;
    }

    /* Share Modal */
    .share-modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .share-modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
    }

    .share-modal-content {
        position: relative;
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        max-width: 400px;
        width: 90%;
        text-align: center;
    }

    .share-modal-content h3 {
        font-size: 1.3rem;
        margin-bottom: 10px;
    }

    .share-modal-content > p {
        color: #666;
        margin-bottom: 20px;
    }

    .share-buttons-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 20px;
    }

    .share-btn-modal {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 15px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        color: #fff;
    }

    .share-btn-modal.share-facebook { background: #1877f2; }
    .share-btn-modal.share-facebook:hover { background: #0d65d9; }
    .share-btn-modal.share-twitter { background: #000; }
    .share-btn-modal.share-twitter:hover { background: #333; }
    .share-btn-modal.share-whatsapp { background: #25d366; }
    .share-btn-modal.share-whatsapp:hover { background: #1da851; }
    .share-btn-modal.share-copy-modal { background: #6b7280; }
    .share-btn-modal.share-copy-modal:hover { background: #4b5563; }

    .share-link-input {
        display: flex;
        gap: 10px;
    }

    .share-link-input input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.85rem;
        background: #f9fafb;
        text-align: center;
    }

    /* Disclaimer */
    .disclaimer-section {
        padding: 40px 0 80px;
        background: #fff;
    }

    .disclaimer-box {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 12px;
        padding: 20px;
    }

    .disclaimer-box i {
        color: #f59e0b;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .disclaimer-box p {
        margin: 0;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .visualizer-wrapper {
            grid-template-columns: 1fr;
        }

        .visualizer-result {
            position: static;
            order: -1;
        }

        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .visualizer-section {
            padding: 30px 0;
        }

        .visualizer-intro {
            margin-bottom: 25px;
        }

        .visualizer-intro h2 {
            font-size: 1.5rem;
        }

        .visualizer-intro p {
            font-size: 0.9rem;
        }

        .visualizer-controls {
            padding: 15px;
            border-radius: 12px;
        }

        .visualizer-controls h3 {
            font-size: 1rem;
        }

        .upload-area {
            padding: 20px 15px;
        }

        .upload-placeholder i {
            font-size: 2rem;
        }

        .upload-placeholder p {
            font-size: 0.95rem;
        }

        .upload-tips {
            padding: 12px;
            margin-bottom: 20px;
        }

        .upload-tips p {
            font-size: 0.85rem;
        }

        .upload-tips ul {
            font-size: 0.8rem;
        }

        .result-container {
            min-height: 280px;
            border-radius: 12px;
        }

        .result-placeholder {
            padding: 25px 15px;
        }

        .result-placeholder i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .result-placeholder h3 {
            font-size: 1.1rem;
        }

        .result-placeholder p {
            font-size: 0.85rem;
        }

        .wrap-selection {
            margin-bottom: 20px;
        }

        .wrap-options {
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 8px;
        }

        .wrap-option {
            padding: 8px;
        }

        .wrap-swatch {
            width: 40px;
            height: 40px;
            margin-bottom: 6px;
        }

        .wrap-name {
            font-size: 0.7rem;
        }

        .wrap-finish {
            font-size: 0.6rem;
        }

        .how-it-works-section {
            padding: 50px 0;
        }

        .how-it-works-section h2 {
            font-size: 1.5rem;
        }

        .step-card {
            padding: 25px 15px;
        }

        .disclaimer-section {
            padding: 30px 0 50px;
        }

        .disclaimer-box {
            padding: 15px;
        }

        .disclaimer-box i {
            font-size: 1.2rem;
        }

        .disclaimer-box p {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 640px) {
        .wrap-categories {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 10px;
            margin: 0 -15px 15px;
            padding-left: 15px;
            padding-right: 15px;
        }

        .wrap-categories::-webkit-scrollbar {
            height: 4px;
        }

        .wrap-categories::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 2px;
        }

        .category-tab {
            flex-shrink: 0;
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .steps-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .result-actions {
            flex-direction: column;
            gap: 10px;
        }

        .visualizer-actions {
            margin-top: 15px;
        }

        .usage-info {
            font-size: 0.8rem;
        }

        .modal-content {
            padding: 30px 20px;
            margin: 20px;
            width: calc(100% - 40px);
        }

        .modal-icon {
            width: 60px;
            height: 60px;
        }

        .modal-icon i {
            font-size: 1.5rem;
        }

        .modal-content h3 {
            font-size: 1.1rem;
        }

        .modal-content p {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 400px) {
        .wrap-options {
            grid-template-columns: repeat(3, 1fr);
        }

        .wrap-swatch {
            width: 35px;
            height: 35px;
        }
    }
    </style>

<?php require_once '../includes/footer.php'; ?>

    <!-- Visualizer Script -->
    <script>
    (function() {
        // Elements
        const uploadArea = document.getElementById('upload-area');
        const carUpload = document.getElementById('car-upload');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const uploadPreview = document.getElementById('upload-preview');
        const previewImage = document.getElementById('preview-image');
        const removeImageBtn = document.getElementById('remove-image');

        const categoryTabs = document.querySelectorAll('.category-tab');
        const wrapOptionsContainers = document.querySelectorAll('.wrap-options');
        const wrapOptions = document.querySelectorAll('.wrap-option');

        const customUploadArea = document.getElementById('custom-upload-area');
        const customWrapUpload = document.getElementById('custom-wrap-upload');
        const customPreview = document.getElementById('custom-preview');
        const customPreviewImage = document.getElementById('custom-preview-image');
        const removeCustomBtn = document.getElementById('remove-custom');

        const generateBtn = document.getElementById('generate-btn');
        const usageText = document.getElementById('usage-text');

        const resultContainer = document.getElementById('result-container');
        const resultPlaceholder = document.getElementById('result-placeholder');
        const resultLoading = document.getElementById('result-loading');
        const resultImage = document.getElementById('result-image');
        const resultError = document.getElementById('result-error');
        const errorMessage = document.getElementById('error-message');
        const generatedImage = document.getElementById('generated-image');
        const wrapBadge = document.getElementById('wrap-badge');
        const resultActions = document.getElementById('result-actions');
        const downloadBtn = document.getElementById('download-btn');
        const retryBtn = document.getElementById('retry-btn');

        const emailModal = document.getElementById('email-modal');
        const emailForm = document.getElementById('email-form');
        const emailInput = document.getElementById('email-input');
        const modalClose = document.getElementById('modal-close');

        const shareBtn = document.getElementById('share-btn');
        const shareModal = document.getElementById('share-modal');
        const shareModalClose = document.getElementById('share-modal-close');
        const shareFacebook = document.getElementById('share-facebook');
        const shareTwitter = document.getElementById('share-twitter');
        const shareWhatsapp = document.getElementById('share-whatsapp');
        const copyLinkBtn = document.getElementById('copy-link-btn');
        const shareLinkInput = document.getElementById('share-link-input');

        // State
        let carImageData = null;
        let selectedWrap = null;
        let selectedWrapName = null;
        let customWrapData = null;
        let currentShareId = null;

        // Initialize
        checkStatus();

        // Upload handlers
        uploadArea.addEventListener('click', () => carUpload.click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                handleCarUpload(e.dataTransfer.files[0]);
            }
        });

        carUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleCarUpload(e.target.files[0]);
            }
        });

        removeImageBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            carImageData = null;
            uploadPlaceholder.style.display = 'block';
            uploadPreview.style.display = 'none';
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
                previewImage.src = carImageData;
                uploadPlaceholder.style.display = 'none';
                uploadPreview.style.display = 'block';
                updateGenerateButton();
            };
            reader.readAsDataURL(file);
        }

        // Category tabs
        categoryTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                categoryTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const category = tab.dataset.category;
                wrapOptionsContainers.forEach(container => {
                    container.classList.toggle('active', container.dataset.category === category);
                });

                // Clear selection when switching categories
                if (category === 'custom') {
                    selectedWrap = null;
                    selectedWrapName = null;
                    wrapOptions.forEach(opt => opt.classList.remove('selected'));
                } else {
                    customWrapData = null;
                    if (customPreview) {
                        customPreview.style.display = 'none';
                        customUploadArea.style.display = 'block';
                    }
                }
                updateGenerateButton();
            });
        });

        // Wrap selection
        wrapOptions.forEach(option => {
            option.addEventListener('click', () => {
                wrapOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                selectedWrap = option.dataset.wrapId;
                selectedWrapName = option.dataset.wrapName;
                customWrapData = null;
                updateGenerateButton();
            });
        });

        // Custom wrap upload
        if (customUploadArea) {
            customUploadArea.addEventListener('click', () => customWrapUpload.click());
        }

        customWrapUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleCustomWrapUpload(e.target.files[0]);
            }
        });

        if (removeCustomBtn) {
            removeCustomBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                customWrapData = null;
                customPreview.style.display = 'none';
                customUploadArea.style.display = 'block';
                updateGenerateButton();
            });
        }

        function handleCustomWrapUpload(file) {
            if (!file.type.match(/image\/(jpeg|jpg|png|webp)/)) {
                alert('Please upload a JPEG, PNG or WebP image');
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                customWrapData = e.target.result;
                customPreviewImage.src = customWrapData;
                customPreview.style.display = 'block';
                customUploadArea.style.display = 'none';
                selectedWrap = 'custom';
                selectedWrapName = 'Custom Pattern';
                wrapOptions.forEach(opt => opt.classList.remove('selected'));
                updateGenerateButton();
            };
            reader.readAsDataURL(file);
        }

        function updateGenerateButton() {
            const hasImage = !!carImageData;
            const hasWrap = !!selectedWrap || !!customWrapData;
            generateBtn.disabled = !(hasImage && hasWrap);
        }

        // Generate
        generateBtn.addEventListener('click', generate);
        retryBtn.addEventListener('click', generate);

        async function generate() {
            if (!carImageData || (!selectedWrap && !customWrapData)) return;

            // Show loading
            resultPlaceholder.style.display = 'none';
            resultImage.style.display = 'none';
            resultError.style.display = 'none';
            resultLoading.style.display = 'block';
            resultActions.style.display = 'none';
            generateBtn.disabled = true;

            // On mobile, scroll to result area so user can see the loading/result
            if (window.innerWidth <= 1024) {
                resultContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            try {
                const response = await fetch('/api/visualize.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        car_image: carImageData,
                        wrap: selectedWrap,
                        wrap_image: customWrapData
                    })
                });

                const data = await response.json();

                if (data.error === 'email_required') {
                    resultLoading.style.display = 'none';
                    resultPlaceholder.style.display = 'block';
                    emailModal.style.display = 'flex';
                    generateBtn.disabled = false;
                    return;
                }

                if (!response.ok || data.error) {
                    throw new Error(data.error || data.message || 'Generation failed');
                }

                // Show result
                resultLoading.style.display = 'none';
                generatedImage.src = data.image;
                wrapBadge.textContent = data.wrap;
                resultImage.style.display = 'block';
                resultActions.style.display = 'flex';

                // Store share ID for sharing
                currentShareId = data.share_id;

                // Update usage
                updateUsageText(data.remaining, data.needs_email);

            } catch (error) {
                resultLoading.style.display = 'none';
                errorMessage.textContent = error.message;
                resultError.style.display = 'block';
            }

            generateBtn.disabled = false;
        }

        // Download
        downloadBtn.addEventListener('click', () => {
            const link = document.createElement('a');
            link.download = 'wrap-preview.png';
            link.href = generatedImage.src;
            link.click();
        });

        // Share button
        shareBtn.addEventListener('click', () => {
            if (!currentShareId) return;

            const shareUrl = window.location.origin + '/share/' + currentShareId;
            const shareText = 'Check out this ' + wrapBadge.textContent + ' wrap preview from North Star Wrap!';

            // Update share links
            shareFacebook.href = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl);
            shareTwitter.href = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareText);
            shareWhatsapp.href = 'https://wa.me/?text=' + encodeURIComponent(shareText + ' ' + shareUrl);
            shareLinkInput.value = shareUrl;

            shareModal.style.display = 'flex';
        });

        // Share modal close handlers
        shareModalClose.addEventListener('click', () => {
            shareModal.style.display = 'none';
        });

        document.querySelector('.share-modal-overlay').addEventListener('click', () => {
            shareModal.style.display = 'none';
        });

        // Copy link button
        copyLinkBtn.addEventListener('click', () => {
            shareLinkInput.select();
            shareLinkInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(shareLinkInput.value).then(() => {
                const originalText = copyLinkBtn.innerHTML;
                copyLinkBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    copyLinkBtn.innerHTML = originalText;
                }, 2000);
            });
        });

        // Email modal
        modalClose.addEventListener('click', () => {
            emailModal.style.display = 'none';
        });

        document.querySelector('.modal-overlay').addEventListener('click', () => {
            emailModal.style.display = 'none';
        });

        emailForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = emailInput.value.trim();
            if (!email) return;

            try {
                const response = await fetch('/api/visualize.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'submit_email', email })
                });

                const data = await response.json();

                if (data.success) {
                    emailModal.style.display = 'none';
                    updateUsageText(data.remaining, false);
                    // Try generating again
                    generate();
                } else {
                    alert(data.error || 'Failed to save email');
                }
            } catch (error) {
                alert('Something went wrong. Please try again.');
            }
        });

        // Check status
        async function checkStatus() {
            try {
                const response = await fetch('/api/visualize.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'status' })
                });
                const data = await response.json();
                updateUsageText(data.remaining, !data.has_email && data.remaining <= 0);
            } catch (error) {
                console.error('Failed to check status:', error);
            }
        }

        function updateUsageText(remaining, needsEmail) {
            if (needsEmail) {
                usageText.textContent = 'Enter your email to unlock more previews';
                usageText.style.color = '#f59e0b';
            } else if (remaining <= 0) {
                usageText.textContent = 'No previews remaining';
                usageText.style.color = '#dc2626';
            } else {
                usageText.textContent = `${remaining} preview${remaining !== 1 ? 's' : ''} remaining`;
                usageText.style.color = '#666';
            }
        }
    })();
    </script>
