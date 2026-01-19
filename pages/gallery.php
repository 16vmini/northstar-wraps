<?php
require_once '../includes/config.php';
$page_title = 'Gallery';
require_once '../includes/header.php';

// Gallery items - In future this can come from database
$gallery_items = [
    [
        'title' => 'BMW M4 Competition',
        'category' => 'full-wrap',
        'description' => 'Satin Black Full Wrap',
        'color' => 'Satin Black',
        'placeholder_icon' => 'fa-car-side'
    ],
    [
        'title' => 'Tesla Model 3',
        'category' => 'full-wrap',
        'description' => 'Midnight Purple Color Change',
        'color' => 'Midnight Purple',
        'placeholder_icon' => 'fa-car'
    ],
    [
        'title' => 'Ford F-150',
        'category' => 'commercial',
        'description' => 'Full Fleet Graphics Package',
        'color' => 'Custom Design',
        'placeholder_icon' => 'fa-truck-pickup'
    ],
    [
        'title' => 'Porsche 911 GT3',
        'category' => 'partial-wrap',
        'description' => 'Racing Stripes & Accents',
        'color' => 'Gloss Red',
        'placeholder_icon' => 'fa-car-side'
    ],
    [
        'title' => 'Mercedes AMG GT',
        'category' => 'chrome-delete',
        'description' => 'Full Blackout Package',
        'color' => 'Gloss Black',
        'placeholder_icon' => 'fa-car'
    ],
    [
        'title' => 'Audi RS7',
        'category' => 'full-wrap',
        'description' => 'Nardo Gray Full Wrap',
        'color' => 'Nardo Gray',
        'placeholder_icon' => 'fa-car-side'
    ],
    [
        'title' => 'Lamborghini Huracán',
        'category' => 'ppf',
        'description' => 'Full Front PPF + Ceramic',
        'color' => 'Clear Protection',
        'placeholder_icon' => 'fa-car'
    ],
    [
        'title' => 'Chevrolet Corvette C8',
        'category' => 'partial-wrap',
        'description' => 'Carbon Fiber Accents',
        'color' => 'Carbon Fiber',
        'placeholder_icon' => 'fa-car-side'
    ],
    [
        'title' => 'GMC Sierra',
        'category' => 'commercial',
        'description' => 'Contractor Business Wrap',
        'color' => 'Custom Graphics',
        'placeholder_icon' => 'fa-truck'
    ],
    [
        'title' => 'Toyota Supra',
        'category' => 'full-wrap',
        'description' => 'Color Shift Wrap',
        'color' => 'Color Shift Purple/Blue',
        'placeholder_icon' => 'fa-car-side'
    ],
    [
        'title' => 'Range Rover Sport',
        'category' => 'chrome-delete',
        'description' => 'Chrome Delete + Tint',
        'color' => 'Satin Black',
        'placeholder_icon' => 'fa-truck-monster'
    ],
    [
        'title' => 'Dodge Charger',
        'category' => 'partial-wrap',
        'description' => 'Custom Racing Livery',
        'color' => 'Multi-Color',
        'placeholder_icon' => 'fa-car-side'
    ],
];

// Categories for filtering
$categories = [
    'all' => 'All Projects',
    'full-wrap' => 'Full Wraps',
    'partial-wrap' => 'Partial Wraps',
    'commercial' => 'Commercial',
    'chrome-delete' => 'Chrome Delete',
    'ppf' => 'Paint Protection'
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
                            <div class="gallery-placeholder-card">
                                <i class="fas <?php echo $item['placeholder_icon']; ?>"></i>
                            </div>
                            <div class="gallery-card-overlay">
                                <button class="gallery-zoom" data-index="<?php echo $index; ?>">
                                    <i class="fas fa-expand"></i>
                                </button>
                            </div>
                        </div>
                        <div class="gallery-card-content">
                            <span class="gallery-card-category"><?php echo $categories[$item['category']]; ?></span>
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

    <!-- Before/After Section -->
    <section class="before-after-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Transformations</span>
                <h2 class="section-title">Before & After</h2>
                <p class="section-subtitle">See the dramatic difference a wrap can make</p>
            </div>

            <div class="before-after-grid" data-aos="fade-up">
                <div class="before-after-card">
                    <div class="before-after-slider">
                        <div class="before-side">
                            <div class="ba-placeholder">
                                <i class="fas fa-car"></i>
                                <span>Before</span>
                            </div>
                            <span class="ba-label">Before</span>
                        </div>
                        <div class="after-side">
                            <div class="ba-placeholder after">
                                <i class="fas fa-car"></i>
                                <span>After</span>
                            </div>
                            <span class="ba-label">After</span>
                        </div>
                        <div class="ba-slider-handle">
                            <i class="fas fa-arrows-alt-h"></i>
                        </div>
                    </div>
                    <div class="before-after-info">
                        <h4>Mercedes C300</h4>
                        <p>Factory White → Satin Midnight Blue</p>
                    </div>
                </div>

                <div class="before-after-card">
                    <div class="before-after-slider">
                        <div class="before-side">
                            <div class="ba-placeholder">
                                <i class="fas fa-truck-pickup"></i>
                                <span>Before</span>
                            </div>
                            <span class="ba-label">Before</span>
                        </div>
                        <div class="after-side">
                            <div class="ba-placeholder after">
                                <i class="fas fa-truck-pickup"></i>
                                <span>After</span>
                            </div>
                            <span class="ba-label">After</span>
                        </div>
                        <div class="ba-slider-handle">
                            <i class="fas fa-arrows-alt-h"></i>
                        </div>
                    </div>
                    <div class="before-after-info">
                        <h4>Ford F-150 Raptor</h4>
                        <p>Plain White → Full Commercial Graphics</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Instagram Feed Section -->
    <section class="social-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Social Media</span>
                <h2 class="section-title">Follow Our Work</h2>
                <p class="section-subtitle">See our latest projects on Instagram</p>
            </div>

            <div class="social-cta" data-aos="fade-up">
                <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" class="btn btn-instagram btn-lg">
                    <i class="fab fa-instagram"></i> Follow @northstarwraps
                </a>
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
                <div class="gallery-placeholder-card lightbox-placeholder">
                    <i class="fas fa-car"></i>
                </div>
            </div>
            <div class="lightbox-info">
                <h3 id="lightbox-title"></h3>
                <p id="lightbox-description"></p>
            </div>
        </div>
    </div>

<?php require_once '../includes/footer.php'; ?>
