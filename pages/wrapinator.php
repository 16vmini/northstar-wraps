<?php
require_once '../includes/config.php';
$page_title = 'Wrapinator';
$page_description = 'See how your car would look with a new vinyl wrap. Upload a photo and preview different colours and finishes instantly with our AI-powered Wrapinator.';

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

    <!-- Wrapinator Widget Section -->
    <section class="wrapinator-section">
        <div class="container">
            <div class="wrapinator-intro" data-aos="fade-up">
                <h2>Preview Your Dream Wrap</h2>
                <p>Upload a photo of your car and select a wrap colour or finish to see how it would look. Our AI-powered Wrapinator creates a realistic preview in seconds.</p>
            </div>

            <!-- Wrapinator Widget -->
            <div class="wrapinator-widget-wrapper" data-aos="fade-up">
                <div id="wrapinator-widget" data-key="wk_aa031df03b7854ce46a11046260ee1f8838fa56e6d0d0951483f327ce0ba"></div>
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

    <!-- Wrapinator Page Styles -->
    <style>
    .wrapinator-section {
        padding: 60px 0;
        background: #f5f5f5;
    }

    .wrapinator-intro {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 40px;
    }

    .wrapinator-intro h2 {
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .wrapinator-intro p {
        color: #666;
    }

    .wrapinator-widget-wrapper {
        max-width: 650px;
        margin: 0 auto;
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
        .steps-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .wrapinator-section {
            padding: 40px 0;
        }

        .wrapinator-intro h2 {
            font-size: 1.5rem;
        }

        .how-it-works-section {
            padding: 50px 0;
        }

        .disclaimer-section {
            padding: 30px 0 50px;
        }
    }

    @media (max-width: 640px) {
        .steps-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
    </style>

<?php require_once '../includes/footer.php'; ?>

    <!-- Wrapinator Widget Script -->
    <script src="https://wrapinator.co.uk/embed/widget.js"></script>
