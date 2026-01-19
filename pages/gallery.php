<?php
require_once '../includes/config.php';
$page_title = 'Gallery';
require_once '../includes/header.php';

// Gallery items - In future this can come from database
$gallery_items = [
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-1.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-2.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-3.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-4.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-5.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-6.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-7.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-8.jpg'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'full-wrap',
        'description' => 'Full Color Change Wrap',
        'color' => 'Custom Finish',
        'image' => '/assets/images/gallery/range-rover-sport-9.jpg'
    ],
];

// Categories for filtering
$categories = [
    'all' => 'All Projects',
    'full-wrap' => 'Full Wraps',
    'partial-wrap' => 'Partial Wraps',
    'commercial' => 'Commercial',
    'chrome-delete' => 'Chrome Delete'
];
?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Our Gallery</h1>
                <p>Browse our portfolio of vehicle transformations</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <span>Gallery</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <!-- Filter Buttons -->
            <div class="gallery-filters" data-aos="fade-up">
                <?php foreach ($categories as $key => $label): ?>
                <button class="filter-btn <?php echo $key === 'all' ? 'active' : ''; ?>" data-filter="<?php echo $key; ?>">
                    <?php echo $label; ?>
                </button>
                <?php endforeach; ?>
            </div>

            <!-- Gallery Grid -->
            <div class="gallery-masonry" data-aos="fade-up">
                <?php foreach ($gallery_items as $index => $item): ?>
                <div class="gallery-card" data-category="<?php echo $item['category']; ?>">
                    <div class="gallery-card-inner">
                        <div class="gallery-card-image">
                            <?php if (!empty($item['image'])): ?>
                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" loading="lazy">
                            <?php else: ?>
                            <div class="gallery-placeholder-card">
                                <i class="fas <?php echo $item['placeholder_icon'] ?? 'fa-car'; ?>"></i>
                            </div>
                            <?php endif; ?>
                            <div class="gallery-card-overlay">
                                <button class="gallery-zoom" data-index="<?php echo $index; ?>" data-image="<?php echo $item['image'] ?? ''; ?>">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="gallery-card-content">
                            <span class="gallery-card-category"><?php echo $categories[$item['category']] ?? $item['category']; ?></span>
                            <h3><?php echo $item['title']; ?></h3>
                            <p><?php echo $item['description']; ?></p>
                            <span class="gallery-card-color">
                                <i class="fas fa-palette"></i> <?php echo $item['color']; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Load More (for future pagination) -->
            <div class="gallery-load-more" data-aos="fade-up">
                <p class="gallery-count">Showing <span id="visible-count"><?php echo count($gallery_items); ?></span> of <?php echo count($gallery_items); ?> projects</p>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Want Your Car Featured Here?</h2>
                <p>Let's create something amazing together</p>
                <div class="cta-buttons">
                    <a href="/pages/contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Start Your Project
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div class="lightbox-modal" id="lightbox">
        <button class="lightbox-close"><i class="fas fa-times"></i></button>
        <button class="lightbox-prev"><i class="fas fa-chevron-left"></i></button>
        <button class="lightbox-next"><i class="fas fa-chevron-right"></i></button>
        <div class="lightbox-content">
            <div class="lightbox-image">
                <img src="" alt="" id="lightbox-img">
            </div>
            <div class="lightbox-info">
                <h3 id="lightbox-title"></h3>
                <p id="lightbox-description"></p>
            </div>
        </div>
    </div>

<?php require_once '../includes/footer.php'; ?>
