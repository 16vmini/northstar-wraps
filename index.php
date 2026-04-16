<?php
require_once 'includes/config.php';
$page_title = 'Premium Vehicle Wrapping Services';
require_once 'includes/header.php';
?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg">
            <div class="hero-overlay"></div>
        </div>
        <div class="hero-particles" id="particles"></div>
        <div class="container">
            <div class="hero-content" data-aos="fade-up" data-aos-duration="1000">
                <h1 class="hero-title">
                    <span class="hero-title-line">Transform Your Ride</span>
                    <span class="hero-title-accent">With Premium Wraps</span>
                </h1>
                <p class="hero-subtitle">
                    Professional vehicle wrapping that protects your paint and turns heads.
                    From subtle color changes to bold custom designs.
                </p>
                <div class="hero-cta">
                    <a href="/pages/contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Get Free Quote
                    </a>
                    <a href="/pages/gallery.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-images"></i> View Our Work
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-scroll">
            <a href="#services-preview" class="scroll-indicator">
                <span>Scroll</span>
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Services Preview Section -->
    <section class="services-preview" id="services-preview">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">What We Do</span>
                <h2 class="section-title">Our Services</h2>
                <p class="section-subtitle">From full color changes to custom graphics, we've got you covered</p>
            </div>

            <div class="services-grid">
                <?php foreach (array_slice($services, 0, 6) as $index => $service): ?>
                <div class="service-card" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="service-card-inner">
                        <?php if (!empty($service['image'])): ?>
                        <div class="service-card-image">
                            <img src="<?php echo $service['image']; ?>" alt="<?php echo $service['name']; ?>" loading="lazy">
                        </div>
                        <?php else: ?>
                        <div class="service-icon">
                            <i class="fas <?php echo $service['icon']; ?>"></i>
                        </div>
                        <?php endif; ?>
                        <h3><?php echo $service['name']; ?></h3>
                        <p><?php echo $service['short_desc']; ?></p>
                        <?php if ($service['price_from']): ?>
                        <span class="service-price">From £<?php echo number_format($service['price_from']); ?></span>
                        <?php endif; ?>
                        <a href="/pages/services.php#<?php echo $service['id']; ?>" class="service-link">
                            Learn More <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="section-cta" data-aos="fade-up">
                <a href="/pages/services.php" class="btn btn-primary">
                    View All Services <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-us">
        <div class="container">
            <div class="why-us-grid">
                <div class="why-us-content" data-aos="fade-right">
                    <span class="section-tag">Why North Star?</span>
                    <h2 class="section-title">Quality That Speaks for Itself</h2>
                    <p class="section-text">
                        We're not just another wrap shop. We're passionate car enthusiasts who understand that your vehicle is more than transportation—it's an expression of who you are.
                    </p>

                    <div class="features-list">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Quality Materials</h4>
                                <p>We work with well-known wrap film brands and pick the right material for each job.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Careful, Unhurried Work</h4>
                                <p>A background in bodywork and paint means we take prep seriously — it's the biggest factor in how a wrap turns out.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Paint Protection</h4>
                                <p>Wraps protect your original paint from UV rays, rock chips, and minor scratches—preserving resale value.</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-undo-alt"></i>
                            </div>
                            <div class="feature-content">
                                <h4>Fully Reversible</h4>
                                <p>Change your mind? No problem. Quality wraps remove cleanly, revealing pristine paint underneath.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="why-us-image" data-aos="fade-left">
                    <div class="image-wrapper">
                        <img src="/assets/images/gallery/range-rover-sport-9.jpg" alt="Wrap in progress" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">How It Works</span>
                <h2 class="section-title">Our Process</h2>
                <p class="section-subtitle">Simple, transparent, and hassle-free from start to finish</p>
            </div>

            <div class="process-timeline">
                <div class="process-step" data-aos="fade-up" data-aos-delay="0">
                    <div class="step-number">01</div>
                    <div class="step-content">
                        <h3>Consultation</h3>
                        <p>Tell us your vision. We'll discuss colors, finishes, coverage options, and provide a detailed quote.</p>
                    </div>
                </div>
                <div class="process-step" data-aos="fade-up" data-aos-delay="150">
                    <div class="step-number">02</div>
                    <div class="step-content">
                        <h3>Design</h3>
                        <p>For custom work, we create mockups so you can see exactly how your vehicle will look before we start.</p>
                    </div>
                </div>
                <div class="process-step" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-number">03</div>
                    <div class="step-content">
                        <h3>Preparation</h3>
                        <p>Your vehicle is thoroughly cleaned and decontaminated. Every surface is prepped for perfect adhesion.</p>
                    </div>
                </div>
                <div class="process-step" data-aos="fade-up" data-aos-delay="450">
                    <div class="step-number">04</div>
                    <div class="step-content">
                        <h3>Installation</h3>
                        <p>We take our time applying the wrap by hand in our indoor workshop — prep and patience are what make the finish.</p>
                    </div>
                </div>
                <div class="process-step" data-aos="fade-up" data-aos-delay="600">
                    <div class="step-number">05</div>
                    <div class="step-content">
                        <h3>Reveal</h3>
                        <p>Final inspection, care instructions, and the exciting moment you see your transformed vehicle!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Preview Section -->
    <section class="gallery-preview">
        <div class="container">
            <div class="section-header" data-aos="fade-up">
                <span class="section-tag">Our Portfolio</span>
                <h2 class="section-title">Recent Projects</h2>
                <p class="section-subtitle">Check out some of our latest transformations</p>
            </div>

            <div class="gallery-grid" data-aos="fade-up">
                <div class="gallery-item gallery-item-lg">
                    <img src="/assets/images/gallery/vivaro-6.jpg" alt="Vauxhall Vivaro Matte Forest Green" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-tag">Full Wrap</span>
                        <h4>Vauxhall Vivaro</h4>
                        <p>Matte Forest Green</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="/assets/images/gallery/vw-t5-camper-1.jpg" alt="VW T5 Camper two-tone" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-tag">Partial Wrap</span>
                        <h4>VW T5 Camper</h4>
                        <p>Matte Military Green</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="/assets/images/gallery/vivaro-3.jpg" alt="Vivaro bonnet wrap detail" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-tag">Full Wrap</span>
                        <h4>Vauxhall Vivaro</h4>
                        <p>Bonnet detail</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="/assets/images/gallery/range-rover-sport-5.jpg" alt="Range Rover Sport roof wrap" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-tag">Partial Wrap</span>
                        <h4>Range Rover Sport</h4>
                        <p>Roof wrap</p>
                    </div>
                </div>
                <div class="gallery-item">
                    <img src="/assets/images/gallery/ice-hockey-helmet-1.jpg" alt="Gloss blue ice hockey helmet wrap" loading="lazy">
                    <div class="gallery-overlay">
                        <span class="gallery-tag">Custom</span>
                        <h4>Ice Hockey Helmet</h4>
                        <p>Gloss Blue</p>
                    </div>
                </div>
            </div>

            <div class="section-cta" data-aos="fade-up">
                <a href="/pages/gallery.php" class="btn btn-primary">
                    View Full Gallery <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Wrapinator Feature Section -->
    <section class="wrapinator-feature">
        <div class="container">
            <div class="wrapinator-content" data-aos="fade-up">
                <div class="wrapinator-text">
                    <span class="section-tag">Try It Free</span>
                    <h2>Visualize Your Dream Wrap</h2>
                    <p>Not sure what colour to choose? Our AI-powered <strong>Wrapinator</strong> lets you upload a photo of your car and preview different wrap colours instantly. See the result before you commit!</p>
                    <div class="wrapinator-features">
                        <div class="wf-item">
                            <i class="fas fa-camera"></i>
                            <span>Upload your car photo</span>
                        </div>
                        <div class="wf-item">
                            <i class="fas fa-palette"></i>
                            <span>Choose from 40+ colours</span>
                        </div>
                        <div class="wf-item">
                            <i class="fas fa-magic"></i>
                            <span>AI generates preview</span>
                        </div>
                        <div class="wf-item">
                            <i class="fas fa-share-alt"></i>
                            <span>Share with friends</span>
                        </div>
                    </div>
                    <div class="wrapinator-cta">
                        <a href="/wrapinator" class="btn btn-primary btn-lg">
                            <i class="fas fa-magic"></i> Try Wrapinator Free
                        </a>
                        <a href="/pages/wrapinator-gallery" class="btn btn-outline btn-lg">
                            <i class="fas fa-images"></i> View Gallery
                        </a>
                    </div>
                </div>
                <div class="wrapinator-visual">
                    <div class="wrapinator-mockup">
                        <div class="mockup-screen">
                            <i class="fas fa-car-side"></i>
                            <div class="mockup-colors">
                                <span style="background: #dc2626;"></span>
                                <span style="background: #2563eb;"></span>
                                <span style="background: #7cb518;"></span>
                                <span style="background: #1a1a1a;"></span>
                            </div>
                            <span class="mockup-label">Wrapinator</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content" data-aos="fade-up">
                <h2>Ready to Transform Your Vehicle?</h2>
                <p>Get a free, no-obligation quote today. Let's make your vision a reality.</p>
                <div class="cta-buttons">
                    <a href="/pages/contact.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane"></i> Get Free Quote
                    </a>
                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', SITE_PHONE); ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone"></i> <?php echo SITE_PHONE; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php require_once 'includes/footer.php'; ?>
