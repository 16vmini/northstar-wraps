<?php
session_start();
require_once '../includes/config.php';
$page_title = 'Contact Us';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../includes/header.php';

// Get pre-selected service from URL if present
$selected_service = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : '';

// Check for form submission success/error
$form_submitted = isset($_GET['submitted']) && $_GET['submitted'] === 'true';
$form_error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content" data-aos="fade-up">
                <h1>Contact Us</h1>
                <p>Get a free quote or ask us anything</p>
                <nav class="breadcrumb">
                    <a href="/">Home</a>
                    <span>/</span>
                    <span>Contact</span>
                </nav>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Form -->
                <div class="contact-form-wrapper" data-aos="fade-right">
                    <div class="form-header">
                        <h2>Get Your Free Quote</h2>
                        <p>Fill out the form below and we'll get back to you within 24 hours</p>
                    </div>

                    <?php if ($form_submitted): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <strong>Thank you!</strong>
                            <p>Your message has been sent successfully. We'll be in touch soon!</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if ($form_error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Oops!</strong>
                            <p><?php echo $form_error; ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form action="/process-form.php" method="POST" class="contact-form" id="quote-form">
                        <!-- Security fields -->
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="form_time" value="<?php echo time(); ?>">

                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" required placeholder="John Doe">
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" id="email" name="email" required placeholder="john@example.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" required placeholder="(555) 123-4567">
                            </div>
                            <div class="form-group">
                                <label for="preferred_contact">Preferred Contact Method</label>
                                <select id="preferred_contact" name="preferred_contact">
                                    <option value="email">Email</option>
                                    <option value="phone">Phone Call</option>
                                    <option value="text">Text Message</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="service">Service Interested In <span class="required">*</span></label>
                                <select id="service" name="service" required>
                                    <option value="">Select a service...</option>
                                    <?php foreach ($services as $service): ?>
                                    <option value="<?php echo $service['id']; ?>" <?php echo $selected_service === $service['id'] ? 'selected' : ''; ?>>
                                        <?php echo $service['name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                    <option value="other">Other / Not Sure</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="budget">Approximate Budget</label>
                                <select id="budget" name="budget">
                                    <option value="">Select budget range...</option>
                                    <option value="under-1000">Under £1,000</option>
                                    <option value="1000-2500">£1,000 - £2,500</option>
                                    <option value="2500-5000">£2,500 - £5,000</option>
                                    <option value="5000-10000">£5,000 - £10,000</option>
                                    <option value="over-10000">£10,000+</option>
                                    <option value="not-sure">Not Sure Yet</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="vehicle_year">Vehicle Year</label>
                                <input type="text" id="vehicle_year" name="vehicle_year" placeholder="2024">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_make">Vehicle Make</label>
                                <input type="text" id="vehicle_make" name="vehicle_make" placeholder="BMW">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_model">Vehicle Model</label>
                                <input type="text" id="vehicle_model" name="vehicle_model" placeholder="M4 Competition">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="color_preference">Color/Finish Preference</label>
                            <input type="text" id="color_preference" name="color_preference" placeholder="e.g., Satin Black, Matte Gray, etc.">
                        </div>

                        <div class="form-group">
                            <label for="message">Tell Us About Your Project <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="5" required placeholder="Describe what you're looking for - coverage area, any specific ideas, timeline, etc."></textarea>
                        </div>

                        <div class="form-group">
                            <label for="how_heard">How Did You Hear About Us?</label>
                            <select id="how_heard" name="how_heard">
                                <option value="">Select an option...</option>
                                <option value="google">Google Search</option>
                                <option value="instagram">Instagram</option>
                                <option value="facebook">Facebook</option>
                                <option value="tiktok">TikTok</option>
                                <option value="referral">Friend/Family Referral</option>
                                <option value="saw-vehicle">Saw a Wrapped Vehicle</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <!-- Honeypot field for spam protection -->
                        <div class="form-group" style="display: none;">
                            <label for="website">Website</label>
                            <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>

                        <p class="form-disclaimer">
                            <i class="fas fa-lock"></i> Your information is secure and will never be shared.
                        </p>
                    </form>
                </div>

                <!-- Contact Info Sidebar -->
                <div class="contact-info-wrapper" data-aos="fade-left">
                    <!-- Direct Contact -->
                    <div class="contact-info-card">
                        <h3>Get In Touch</h3>
                        <div class="contact-info-list">
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-info-content">
                                    <span class="contact-info-label">Phone</span>
                                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', SITE_PHONE); ?>"><?php echo SITE_PHONE; ?></a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-info-content">
                                    <span class="contact-info-label">Email</span>
                                    <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                                </div>
                            </div>
                            <div class="contact-info-item">
                                <div class="contact-info-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="contact-info-content">
                                    <span class="contact-info-label">Location</span>
                                    <span><?php echo SITE_ADDRESS; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="contact-info-card">
                        <h3>Business Hours</h3>
                        <div class="hours-list">
                            <?php foreach ($business_hours as $day => $hours): ?>
                            <div class="hours-item <?php echo $hours === 'Closed' ? 'closed' : ''; ?>">
                                <span class="hours-day"><?php echo $day; ?></span>
                                <span class="hours-time"><?php echo $hours; ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="contact-info-card">
                        <h3>Follow Us</h3>
                        <p>Check out our latest work on social media</p>
                        <div class="contact-social">
                            <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" class="social-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" class="social-btn instagram">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="<?php echo SOCIAL_TIKTOK; ?>" target="_blank" class="social-btn tiktok">
                                <i class="fab fa-tiktok"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Response Promise -->
                    <div class="contact-info-card highlight">
                        <div class="promise-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Quick Response</h3>
                        <p>We typically respond to all inquiries within <strong>24 hours</strong> during business days.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="container">
            <div class="map-wrapper" data-aos="fade-up">
                <div class="map-placeholder">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3>Visit Our Shop</h3>
                    <p><?php echo SITE_ADDRESS; ?></p>
                    <a href="https://maps.google.com/?q=<?php echo urlencode(SITE_ADDRESS); ?>" target="_blank" class="btn btn-primary">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                </div>
            </div>
        </div>
    </section>

<?php require_once '../includes/footer.php'; ?>
