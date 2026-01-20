<?php
/**
 * Share page for Wrapinator visualizations
 * Displays a shared image with social sharing options
 */
require_once '../includes/config.php';

// Get share ID from URL
$share_id = preg_replace('/[^a-f0-9]/', '', $_GET['id'] ?? '');

if (empty($share_id)) {
    header('Location: /wrapinator');
    exit;
}

// Load metadata (check both approved and pending)
$upload_dir = __DIR__ . '/../uploads/visualizer';
$metadata_file = $upload_dir . '/' . $share_id . '.json';
$image_file = $upload_dir . '/' . $share_id . '.png';

// Check for pending files if approved not found
if (!file_exists($metadata_file)) {
    $metadata_file = $upload_dir . '/pending_' . $share_id . '.json';
    $image_file = $upload_dir . '/pending_' . $share_id . '.png';
}

if (!file_exists($metadata_file) || !file_exists($image_file)) {
    header('Location: /wrapinator');
    exit;
}

$metadata = json_decode(file_get_contents($metadata_file), true);
$wrap_name = $metadata['wrap'] ?? 'Custom Wrap';
$wrap_finish = $metadata['finish'] ?? '';
$created = $metadata['created'] ?? '';

// Generate URLs
$site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$share_url = $site_url . '/share/' . $share_id;
$image_url = $site_url . '/api/share-image.php?id=' . $share_id;

// Page meta
$page_title = $wrap_name . ' Wrap Preview - North Star Wrap';
$page_description = 'Check out this ' . $wrap_name . ' wrap preview created with the North Star Wrap Wrapinator!';

require_once '../includes/header.php';
?>

    <!-- Page Hero -->
    <section class="page-hero page-hero-compact">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Wrap Preview</h1>
                <p><?php echo htmlspecialchars($wrap_name); ?> <?php echo $wrap_finish ? '(' . htmlspecialchars($wrap_finish) . ')' : ''; ?></p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <a href="/wrapinator">Wrapinator</a>
                    <span>/</span>
                    <span>Share</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Share Content -->
    <section class="share-section">
        <div class="container">
            <div class="share-wrapper" data-aos="fade-up">
                <!-- Image Display -->
                <div class="share-image-container">
                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($wrap_name); ?> wrap preview" class="share-image">
                    <div class="wrap-badge"><?php echo htmlspecialchars($wrap_name); ?></div>
                </div>

                <!-- Share Options -->
                <div class="share-options">
                    <h3>Share this preview</h3>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($share_url); ?>"
                           target="_blank" rel="noopener" class="share-btn share-facebook">
                            <i class="fab fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($share_url); ?>&text=<?php echo urlencode('Check out this ' . $wrap_name . ' wrap preview from North Star Wrap!'); ?>"
                           target="_blank" rel="noopener" class="share-btn share-twitter">
                            <i class="fab fa-x-twitter"></i>
                            <span>X</span>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode('Check out this wrap preview: ' . $share_url); ?>"
                           target="_blank" rel="noopener" class="share-btn share-whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                        <button class="share-btn share-copy" onclick="copyShareLink()">
                            <i class="fas fa-link"></i>
                            <span>Copy Link</span>
                        </button>
                    </div>

                    <div class="share-link-box">
                        <input type="text" id="share-link" value="<?php echo htmlspecialchars($share_url); ?>" readonly>
                        <button onclick="copyShareLink()" class="copy-btn">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <!-- CTA -->
                <div class="share-cta">
                    <h3>Want to see your car wrapped?</h3>
                    <p>Try our AI-powered Wrapinator to preview different wrap colours on your own vehicle.</p>
                    <div class="cta-buttons">
                        <a href="/wrapinator" class="btn btn-primary">
                            <i class="fas fa-magic"></i> Try Wrapinator
                        </a>
                        <a href="/contact?service=full-wrap" class="btn btn-outline">
                            <i class="fas fa-envelope"></i> Get a Quote
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
    .page-hero-compact {
        padding: 40px 0;
    }

    .share-section {
        padding: 40px 0 80px;
        background: #f5f5f5;
    }

    .share-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }

    .share-image-container {
        position: relative;
        background: #1a1a1a;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 30px;
    }

    .share-image {
        width: 100%;
        height: auto;
        display: block;
    }

    .wrap-badge {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: rgba(0, 0, 0, 0.8);
        color: #fff;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 500;
        border-left: 3px solid #7cb518;
    }

    .share-options {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .share-options h3 {
        font-size: 1.2rem;
        margin-bottom: 20px;
        text-align: center;
    }

    .share-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .share-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .share-btn i {
        font-size: 1.1rem;
    }

    .share-facebook {
        background: #1877f2;
        color: #fff;
    }

    .share-facebook:hover {
        background: #0d65d9;
    }

    .share-twitter {
        background: #000;
        color: #fff;
    }

    .share-twitter:hover {
        background: #333;
    }

    .share-whatsapp {
        background: #25d366;
        color: #fff;
    }

    .share-whatsapp:hover {
        background: #1da851;
    }

    .share-copy {
        background: #6b7280;
        color: #fff;
    }

    .share-copy:hover {
        background: #4b5563;
    }

    .share-link-box {
        display: flex;
        gap: 10px;
        max-width: 500px;
        margin: 0 auto;
    }

    .share-link-box input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.9rem;
        background: #f9fafb;
    }

    .copy-btn {
        padding: 12px 20px;
        background: #7cb518;
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .copy-btn:hover {
        background: #6a9c15;
    }

    .share-cta {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 16px;
        padding: 40px;
        text-align: center;
        color: #fff;
    }

    .share-cta h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .share-cta p {
        color: #9ca3af;
        margin-bottom: 25px;
    }

    .cta-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .cta-buttons .btn {
        padding: 14px 28px;
    }

    .cta-buttons .btn-outline {
        border-color: #fff;
        color: #fff;
    }

    .cta-buttons .btn-outline:hover {
        background: #fff;
        color: #1a1a1a;
    }

    @media (max-width: 640px) {
        .share-section {
            padding: 30px 0 60px;
        }

        .share-options {
            padding: 20px;
        }

        .share-buttons {
            gap: 10px;
        }

        .share-btn {
            padding: 10px 16px;
            font-size: 0.85rem;
        }

        .share-btn span {
            display: none;
        }

        .share-btn i {
            font-size: 1.2rem;
        }

        .share-cta {
            padding: 30px 20px;
        }

        .share-cta h3 {
            font-size: 1.3rem;
        }

        .cta-buttons {
            flex-direction: column;
        }

        .cta-buttons .btn {
            width: 100%;
        }
    }
    </style>

    <script>
    function copyShareLink() {
        const input = document.getElementById('share-link');
        input.select();
        input.setSelectionRange(0, 99999);

        navigator.clipboard.writeText(input.value).then(() => {
            // Show feedback
            const btn = document.querySelector('.share-copy span') || document.querySelector('.copy-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 2000);
        });
    }
    </script>

<?php require_once '../includes/footer.php'; ?>
