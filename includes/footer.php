    <!-- Footer -->
    <footer class="main-footer">
        <div class="footer-top">
            <div class="container">
                <div class="footer-grid">
                    <!-- Company Info -->
                    <div class="footer-section">
                        <img src="/assets/images/logo.png" alt="<?php echo SITE_NAME; ?>" class="footer-logo">
                        <p class="footer-tagline"><?php echo SITE_TAGLINE; ?></p>
                        <p class="footer-description">
                            Transforming vehicles with precision and passion. Quality vinyl wraps and custom designs that turn heads.
                        </p>
                        <div class="footer-social">
                            <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            <a href="<?php echo SOCIAL_TIKTOK; ?>" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="footer-section">
                        <h4>Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="/">Home</a></li>
                            <li><a href="/pages/services.php">Services</a></li>
                            <li><a href="/pages/gallery.php">Gallery</a></li>
                            <li><a href="/pages/about.php">About Us</a></li>
                            <li><a href="/pages/contact.php">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Services -->
                    <div class="footer-section">
                        <h4>Our Services</h4>
                        <ul class="footer-links">
                            <li><a href="/pages/services.php#full-wrap">Full Vehicle Wraps</a></li>
                            <li><a href="/pages/services.php#partial-wrap">Partial Wraps</a></li>
                            <li><a href="/pages/services.php#commercial">Commercial Wraps</a></li>
                            <li><a href="/pages/services.php#chrome-delete">Chrome Delete</a></li>
                            <li><a href="/pages/services.php#custom-design">Custom Designs</a></li>
                        </ul>
                    </div>

                    <!-- Contact Info -->
                    <div class="footer-section">
                        <h4>Contact Us</h4>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo SITE_ADDRESS; ?></span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <a href="tel:<?php echo preg_replace('/[^0-9]/', '', SITE_PHONE); ?>"><?php echo SITE_PHONE; ?></a>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                            </li>
                        </ul>
                        <div class="footer-hours">
                            <h5>Business Hours</h5>
                            <p>Mon - Fri: 8:00 AM - 6:00 PM</p>
                            <p>Saturday: 9:00 AM - 4:00 PM</p>
                            <p>Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
                    <p class="footer-credit">Crafted with <i class="fas fa-heart"></i> for car enthusiasts</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top" aria-label="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- AOS Animation Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

    <!-- Custom JavaScript -->
    <?php if (!defined('SITE_VERSION')) require_once __DIR__ . '/version.php'; ?>
    <script src="/assets/js/main.js?v=<?php echo SITE_VERSION; ?>"></script>
</body>
</html>
