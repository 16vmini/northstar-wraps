<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    // Load version numbers
    require_once __DIR__ . '/version.php';

    // SEO Meta - Page specific or defaults
    $meta_description = isset($page_description) ? $page_description : SITE_NAME . ' - ' . SITE_TAGLINE . '. Professional vehicle wrapping, full colour changes, partial wraps, chrome delete and commercial fleet graphics.';
    $meta_title = isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE;
    $meta_image = isset($page_image) ? $page_image : '/assets/images/og-image.jpg';
    $canonical_url = isset($canonical) ? $canonical : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    ?>

    <!-- Primary Meta Tags -->
    <title><?php echo $meta_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="car wrap, vehicle wrap, vinyl wrap, colour change, chrome delete, commercial fleet wrap, vehicle graphics, matte wrap, gloss wrap, satin wrap">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo $canonical_url; ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $canonical_url; ?>">
    <meta property="og:title" content="<?php echo $meta_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo $meta_image; ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:locale" content="en_GB">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $meta_title; ?>">
    <meta name="twitter:description" content="<?php echo $meta_description; ?>">
    <meta name="twitter:image" content="<?php echo $meta_image; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    <link rel="apple-touch-icon" href="/assets/images/apple-touch-icon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Rajdhani:wght@300;400;500;600;700&family=Open+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AOS Animation Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="/assets/css/style.css?v=<?php echo SITE_VERSION; ?>">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-left">
                    <a href="tel:<?php echo preg_replace('/[^0-9]/', '', SITE_PHONE); ?>">
                        <i class="fas fa-phone"></i> <?php echo SITE_PHONE; ?>
                    </a>
                    <a href="mailto:<?php echo SITE_EMAIL; ?>">
                        <i class="fas fa-envelope"></i> <?php echo SITE_EMAIL; ?>
                    </a>
                </div>
                <div class="top-bar-right">
                    <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo SOCIAL_TIKTOK; ?>" target="_blank" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <header class="main-header">
        <div class="container">
            <nav class="navbar">
                <a href="/" class="logo">
                    <img src="/assets/images/logo.png" alt="<?php echo SITE_NAME; ?> Logo">
                </a>

                <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">
                    <span class="hamburger"></span>
                </button>

                <ul class="nav-menu">
                    <li><a href="/" class="<?php echo isCurrentPage('home') ? 'active' : ''; ?>">Home</a></li>
                    <li><a href="/pages/services.php" class="<?php echo isCurrentPage('services') ? 'active' : ''; ?>">Services</a></li>
                    <li><a href="/pages/gallery.php" class="<?php echo isCurrentPage('gallery') ? 'active' : ''; ?>">Gallery</a></li>
                    <li><a href="/pages/calculator.php" class="<?php echo isCurrentPage('calculator') ? 'active' : ''; ?>">Price Calculator</a></li>
                    <li><a href="/pages/about.php" class="<?php echo isCurrentPage('about') ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="/pages/contact.php" class="<?php echo isCurrentPage('contact') ? 'active' : ''; ?>">Contact</a></li>
                    <li><a href="/pages/contact.php" class="btn btn-primary nav-cta">Get a Quote</a></li>
                </ul>
            </nav>
        </div>
    </header>
