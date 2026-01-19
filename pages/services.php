<?php
require_once '../includes/config.php';
$page_title = 'Our Services';
require_once '../includes/header.php';
?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Our Services</h1>
                <p>Professional vehicle wrapping solutions for every need and budget</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <span>Services</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Services Introduction -->
    <section class="services-intro">
        <div class="container">
            <div class="intro-content" data-aos="fade-up">
                <p class="lead-text">
                    Whether you're looking to completely change your vehicle's color
                    or create eye-catching graphics for your business, North Star Wraps has the expertise
                    and premium materials to make it happen.
                </p>
            </div>
        </div>
    </section>

    <!-- Services Detail -->
    <section class="services-detail">
        <div class="container">
            <?php foreach ($services as $index => $service): ?>
            <div class="service-detail-card" id="<?php echo $service['id']; ?>" data-aos="fade-up">
                <div class="service-detail-grid <?php echo $index % 2 === 1 ? 'reverse' : ''; ?>">
                    <div class="service-detail-image">
                        <div class="image-placeholder-large">
                            <i class="fas <?php echo $service['icon']; ?>"></i>
                        </div>
                    </div>
                    <div class="service-detail-content">
                        <div class="service-detail-icon">
                            <i class="fas <?php echo $service['icon']; ?>"></i>
                        </div>
                        <h2><?php echo $service['name']; ?></h2>
                        <p class="service-description"><?php echo $service['description']; ?></p>

                        <?php if ($service['id'] === 'full-wrap'): ?>
                        <div class="service-features">
                            <h4>What's Included:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Full exterior coverage (all visible panels)</li>
                                <li><i class="fas fa-check"></i> Door jamb wrapping for seamless look</li>
                                <li><i class="fas fa-check"></i> Premium vinyl with 5+ year durability</li>
                                <li><i class="fas fa-check"></i> Complete surface preparation</li>
                                <li><i class="fas fa-check"></i> Post-installation heat treatment</li>
                            </ul>
                        </div>
                        <?php elseif ($service['id'] === 'partial-wrap'): ?>
                        <div class="service-features">
                            <h4>Popular Options:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Hood and roof wraps</li>
                                <li><i class="fas fa-check"></i> Racing stripes and accent lines</li>
                                <li><i class="fas fa-check"></i> Mirror caps and spoiler wraps</li>
                                <li><i class="fas fa-check"></i> Two-tone color combinations</li>
                                <li><i class="fas fa-check"></i> Custom graphic placements</li>
                            </ul>
                        </div>
                        <?php elseif ($service['id'] === 'commercial'): ?>
                        <div class="service-features">
                            <h4>Business Benefits:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Custom design services included</li>
                                <li><i class="fas fa-check"></i> Fleet pricing available</li>
                                <li><i class="fas fa-check"></i> Brand consistency across vehicles</li>
                                <li><i class="fas fa-check"></i> Faster turnaround than paint</li>
                                <li><i class="fas fa-check"></i> Easy updates when branding changes</li>
                            </ul>
                        </div>
                        <?php elseif ($service['id'] === 'chrome-delete'): ?>
                        <div class="service-features">
                            <h4>Common Applications:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Window trim blackout</li>
                                <li><i class="fas fa-check"></i> Grille and badge wrapping</li>
                                <li><i class="fas fa-check"></i> Door handle covers</li>
                                <li><i class="fas fa-check"></i> Exhaust tip wraps</li>
                                <li><i class="fas fa-check"></i> Mirror housing wraps</li>
                            </ul>
                        </div>
                        <?php elseif ($service['id'] === 'custom-design'): ?>
                        <div class="service-features">
                            <h4>Design Services:</h4>
                            <ul>
                                <li><i class="fas fa-check"></i> Professional design consultation</li>
                                <li><i class="fas fa-check"></i> 3D mockup visualization</li>
                                <li><i class="fas fa-check"></i> Unlimited revisions</li>
                                <li><i class="fas fa-check"></i> Complex graphics and patterns</li>
                                <li><i class="fas fa-check"></i> Livery and race car designs</li>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <div class="service-detail-footer">
                            <?php if ($service['price_from']): ?>
                            <div class="price-tag">
                                <span class="price-label">Starting at</span>
                                <span class="price-value">£<?php echo number_format($service['price_from']); ?></span>
                            </div>
                            <?php else: ?>
                            <div class="price-tag">
                                <span class="price-label">Pricing</span>
                                <span class="price-value">Custom Quote</span>
                            </div>
                            <?php endif; ?>
                            <a href="/pages/contact.php?service=<?php echo $service['id']; ?>" class="btn btn-primary">
                                Get Quote <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Materials Section -->
    <section class="materials-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Premium Materials</span>
                <h2 class="section-title">Finishes & Colors</h2>
                <p class="section-subtitle">Choose from hundreds of colors and finishes</p>
            </div>

            <div class="finishes-grid" data-aos="fade-up">
                <?php foreach ($finishes as $name => $description): ?>
                <div class="finish-card">
                    <div class="finish-preview finish-<?php echo strtolower(str_replace(' ', '-', $name)); ?>"></div>
                    <h4><?php echo $name; ?></h4>
                    <p><?php echo $description; ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="brands-showcase" data-aos="fade-up">
                <h3>We Use Premium Brands</h3>
                <div class="brand-logos">
                    <div class="brand-logo">3M</div>
                    <div class="brand-logo">Avery Dennison</div>
                    <div class="brand-logo">XPEL</div>
                    <div class="brand-logo">Inozetek</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">FAQ</span>
                <h2 class="section-title">Common Questions</h2>
            </div>

            <div class="faq-grid" data-aos="fade-up">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How long does a full wrap last?</span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <p>With proper care, a quality vinyl wrap typically lasts 5-7 years. Factors affecting longevity include sun exposure, climate, and maintenance. We provide detailed care instructions with every wrap.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Will a wrap damage my paint?</span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <p>No! In fact, wraps protect your paint. When removed properly by professionals, your original paint will be in the same condition as when the wrap was applied—often better protected than if it had been exposed.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How long does installation take?</span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <p>A full wrap typically takes 3-5 days depending on vehicle complexity. Partial wraps and chrome deletes can often be completed in 1-2 days. We'll provide a specific timeline with your quote.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Can I wash my wrapped car?</span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! Hand washing is recommended. Avoid high-pressure washers close to edges. Touchless automatic washes are generally safe. We recommend avoiding brush-style car washes.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Is wrap cheaper than paint?</span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <p>For quality results, wraps are often comparable or less expensive than a professional paint job. Plus, wraps are reversible, can be replaced, and protect your original paint's value.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Do you offer warranties?</span>
                        <i class="fas fa-plus"></i>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! We offer workmanship warranties on all installations. Additionally, premium vinyl manufacturers provide material warranties ranging from 3-10 years depending on the product.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Ready to Get Started?</h2>
                <p>Contact us today for a free consultation and quote</p>
                <div class="cta-buttons">
                    <a href="/pages/contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Request Quote
                    </a>
                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', SITE_PHONE); ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
