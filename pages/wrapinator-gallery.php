<?php
/**
 * Wrapinator Community Gallery - Powered by Wrapinator Widget
 */
require_once '../includes/config.php';

$page_title = 'Wrapinator Gallery';
$page_description = 'Browse wrap previews created by our community using the North Star Wrap Wrapinator. See real cars visualized with different vinyl wrap colours and finishes.';

require_once '../includes/header.php';
?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Wrapinator Gallery</h1>
                <p>Community wrap previews from our AI visualizer</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <a href="/pages/wrapinator.php">Wrapinator</a>
                    <span>/</span>
                    <span>Gallery</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="wrapinator-gallery-section">
        <div class="container">
            <div class="gallery-intro" data-aos="fade-up">
                <h2>See What's Possible</h2>
                <p>Browse wrap previews created by our community. Want to see your car here? Try the Wrapinator and share your creation!</p>
                <a href="/pages/wrapinator.php" class="btn btn-primary">
                    <i class="fas fa-magic"></i> Try Wrapinator
                </a>
            </div>

            <!-- Wrapinator Gallery Widget -->
            <div class="gallery-widget-wrapper" data-aos="fade-up">
                <div id="wrapinator-gallery"
                     data-key="wk_aa031df03b7854ce46a11046260ee1f8838fa56e6d0d0951483f327ce0ba"
                     data-source="own"
                     data-limit="24"
                     data-columns="auto"
                     data-show-download="true"
                     data-show-details="true"
                     data-show-vehicle="true"></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="wrapinator-cta">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Ready to Transform Your Car?</h2>
                <p>Our AI-powered Wrapinator lets you visualize different wrap colours on your own vehicle. Try it free!</p>
                <div class="cta-buttons">
                    <a href="/pages/wrapinator.php" class="btn btn-primary">
                        <i class="fas fa-magic"></i> Try Wrapinator
                    </a>
                    <a href="/pages/contact.php?service=full-wrap" class="btn btn-outline-light">
                        <i class="fas fa-paper-plane"></i> Get a Quote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <style>
    .wrapinator-gallery-section {
        padding: 60px 0;
        background: #f5f5f5;
    }

    .gallery-intro {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 50px;
    }

    .gallery-intro h2 {
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .gallery-intro p {
        color: #666;
        margin-bottom: 25px;
    }

    .gallery-widget-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .wrapinator-cta {
        padding: 80px 0;
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #fff;
        text-align: center;
    }

    .wrapinator-cta h2 {
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .wrapinator-cta p {
        color: #9ca3af;
        max-width: 500px;
        margin: 0 auto 30px;
    }

    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .btn-outline-light {
        border: 2px solid #fff;
        color: #fff;
        background: transparent;
    }

    .btn-outline-light:hover {
        background: #fff;
        color: #1a1a1a;
    }

    @media (max-width: 768px) {
        .wrapinator-gallery-section {
            padding: 40px 0;
        }

        .gallery-intro {
            margin-bottom: 30px;
        }

        .gallery-intro h2 {
            font-size: 1.5rem;
        }

        .wrapinator-cta {
            padding: 50px 0;
        }

        .wrapinator-cta h2 {
            font-size: 1.5rem;
        }

        .cta-buttons {
            flex-direction: column;
            align-items: center;
        }

        .cta-buttons .btn {
            width: 100%;
            max-width: 280px;
        }
    }
    </style>

<?php require_once '../includes/footer.php'; ?>

    <!-- Wrapinator Gallery Widget Script -->
    <script src="https://wrapinator.co.uk/embed/gallery-widget.js"></script>
