<?php
/**
 * Wrapinator Community Gallery - Approved User Submissions
 * Shows wrap previews created by users
 */
require_once '../includes/config.php';

$page_title = 'Wrapinator Gallery';
$page_description = 'Browse wrap previews created by our community using the North Star Wrap Wrapinator. See real cars visualized with different vinyl wrap colours and finishes.';

// Get approved images
$upload_dir = __DIR__ . '/../uploads/visualizer';
$gallery_images = [];

if (is_dir($upload_dir)) {
    // Get all approved images (files without pending_ prefix)
    $files = glob($upload_dir . '/*.png');
    // Filter out pending files (PHP 7 compatible)
    $files = array_filter($files, function($f) {
        return strpos(basename($f), 'pending_') === false;
    });

    foreach ($files as $file) {
        $filename = basename($file);
        $id = str_replace('.png', '', $filename);
        // Skip if id starts with 'pending' (double check)
        if (strpos($id, 'pending') === 0) continue;

        $meta_file = $upload_dir . '/' . $id . '.json';
        $meta = file_exists($meta_file) ? json_decode(file_get_contents($meta_file), true) : [];

        $gallery_images[] = [
            'id' => $id,
            'wrap' => $meta['wrap'] ?? 'Custom',
            'finish' => $meta['finish'] ?? '',
            'created' => $meta['created'] ?? date('Y-m-d', filemtime($file)),
            'approved_at' => $meta['approved_at'] ?? null
        ];
    }

    // Sort by newest first
    usort($gallery_images, function($a, $b) {
        $date_a = $a['approved_at'] ?? $a['created'];
        $date_b = $b['approved_at'] ?? $b['created'];
        return strtotime($date_b) - strtotime($date_a);
    });
}

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
                    <a href="/visualizer">Wrapinator</a>
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
                <a href="/visualizer" class="btn btn-primary">
                    <i class="fas fa-magic"></i> Try Wrapinator
                </a>
            </div>

            <?php if (empty($gallery_images)): ?>
                <div class="empty-gallery" data-aos="fade-up">
                    <i class="fas fa-images"></i>
                    <h3>No images yet</h3>
                    <p>Be the first to create and share a wrap preview!</p>
                    <a href="/visualizer" class="btn btn-primary">Create a Preview</a>
                </div>
            <?php else: ?>
                <div class="wrapinator-grid" data-aos="fade-up">
                    <?php foreach ($gallery_images as $image): ?>
                        <a href="/share/<?php echo $image['id']; ?>" class="wrapinator-item">
                            <div class="wrapinator-image">
                                <img src="/api/share-image.php?id=<?php echo $image['id']; ?>"
                                     alt="<?php echo htmlspecialchars($image['wrap']); ?> wrap preview"
                                     loading="lazy">
                            </div>
                            <div class="wrapinator-info">
                                <span class="wrap-name"><?php echo htmlspecialchars($image['wrap']); ?></span>
                                <?php if ($image['finish']): ?>
                                    <span class="wrap-finish"><?php echo htmlspecialchars($image['finish']); ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="wrapinator-cta">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Ready to Transform Your Car?</h2>
                <p>Our AI-powered Wrapinator lets you visualize different wrap colours on your own vehicle. Try it free!</p>
                <div class="cta-buttons">
                    <a href="/visualizer" class="btn btn-primary">
                        <i class="fas fa-magic"></i> Try Wrapinator
                    </a>
                    <a href="/contact?service=full-wrap" class="btn btn-outline-light">
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

    .empty-gallery {
        text-align: center;
        padding: 80px 20px;
        background: #fff;
        border-radius: 16px;
    }

    .empty-gallery i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 20px;
    }

    .empty-gallery h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
    }

    .empty-gallery p {
        color: #666;
        margin-bottom: 25px;
    }

    .wrapinator-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .wrapinator-item {
        display: block;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .wrapinator-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .wrapinator-image {
        position: relative;
        padding-top: 66.67%; /* 3:2 aspect ratio */
        overflow: hidden;
    }

    .wrapinator-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .wrapinator-item:hover .wrapinator-image img {
        transform: scale(1.05);
    }

    .wrapinator-info {
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .wrap-name {
        font-weight: 600;
        color: #1a1a1a;
    }

    .wrap-finish {
        font-size: 0.85rem;
        color: #666;
        background: #f3f4f6;
        padding: 4px 10px;
        border-radius: 20px;
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

        .wrapinator-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
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
