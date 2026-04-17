<?php
/**
 * North Star Wrap - Configuration File
 */

// Site Configuration
define('SITE_NAME', 'North Star Wrap');
define('SITE_TAGLINE', 'Premium Vehicle Wrapping Services');
define('SITE_EMAIL', 'northstarwrap@yahoo.com');
define('SITE_PHONE', '07300 365782');
define('SITE_ADDRESS', 'Unit G2, Manorway Business Park, Swanscombe, Kent DA10 0PP');

// Social Media Links
define('SOCIAL_FACEBOOK', 'https://facebook.com/northstarwrap');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/northstarwrap');
define('SOCIAL_TIKTOK', 'https://tiktok.com/@northstarwrap');

// Business Hours
$business_hours = [
    'Monday' => '8:00 AM - 6:00 PM',
    'Tuesday' => '8:00 AM - 6:00 PM',
    'Wednesday' => '8:00 AM - 6:00 PM',
    'Thursday' => '8:00 AM - 6:00 PM',
    'Friday' => '8:00 AM - 6:00 PM',
    'Saturday' => '9:00 AM - 4:00 PM',
    'Sunday' => 'Closed'
];

// Services offered
$services = [
    [
        'id' => 'full-wrap',
        'name' => 'Full Vehicle Wraps',
        'short_desc' => 'Complete color transformation for your entire vehicle.',
        'description' => 'Transform your vehicle with a complete color change. Our full wraps cover every visible panel, giving your car, truck, or SUV an entirely new look while protecting the original paint underneath.',
        'icon' => 'fa-car',
        'image' => '/assets/images/gallery/vivaro-6.jpg',
        'price_from' => 2500
    ],
    [
        'id' => 'partial-wrap',
        'name' => 'Partial Wraps',
        'short_desc' => 'Accent wraps for hoods, roofs, mirrors, and more.',
        'description' => 'Add style without committing to a full wrap. Perfect for hoods, roofs, mirrors, spoilers, or any combination of panels. Create a custom two-tone look or add racing stripes.',
        'icon' => 'fa-palette',
        'image' => '/assets/images/gallery/vw-t5-camper-1.jpg',
        'price_from' => 500
    ],
    [
        'id' => 'commercial',
        'name' => 'Commercial & Fleet',
        'short_desc' => 'Turn your vehicles into mobile billboards.',
        'description' => 'Make your business stand out with professional vehicle graphics. From single work vans to entire fleets, we create eye-catching designs that advertise your brand wherever you go.',
        'icon' => 'fa-truck',
        'image' => '/assets/images/gallery/vivaro-8.jpg',
        'price_from' => 1500
    ],
    [
        'id' => 'chevron-kits',
        'name' => 'Chevron Kits',
        'short_desc' => 'Hi-viz rear chevron kits for work and utility vans.',
        'description' => 'Red and yellow reflective chevron kits for work vans, utility vehicles and fleets. Cut and fitted to suit the vehicle, using hi-viz reflective film so you stand out on site and on the road.',
        'icon' => 'fa-exclamation-triangle',
        'image' => '/assets/images/services/chevron-kit.jpg',
        'price_from' => null
    ],
    [
        'id' => 'chrome-delete',
        'name' => 'Chrome Delete',
        'short_desc' => 'Sleek blackout packages for trim and accents.',
        'description' => 'Modernize your vehicle by replacing shiny chrome trim with sleek satin or gloss black vinyl. Popular for window trim, grilles, badges, and door handles.',
        'icon' => 'fa-adjust',
        'price_from' => 300
    ],
    [
        'id' => 'custom-design',
        'name' => 'Custom Designs',
        'short_desc' => 'Bring your unique vision to life.',
        'description' => 'Have something special in mind? Our design team can create custom graphics, patterns, liveries, or artistic wraps that make your vehicle truly one-of-a-kind.',
        'icon' => 'fa-pencil-ruler',
        'image' => '/assets/images/gallery/ice-hockey-helmet-1.jpg',
        'price_from' => null
    ]
];

// Wrap materials/finishes available
$finishes = [
    'Gloss' => 'High shine, mirror-like finish',
    'Matte' => 'Flat, non-reflective modern look',
    'Satin' => 'Subtle sheen between gloss and matte',
    'Metallic' => 'Contains metal flakes for sparkle',
    'Chrome' => 'Mirror-finish reflective surface',
    'Carbon Fiber' => 'Textured weave pattern',
    'Brushed Metal' => 'Directional metallic texture',
    'Color Shift' => 'Changes color based on viewing angle'
];

// Helper function to get current page
function getCurrentPage() {
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page === 'index' ? 'home' : $page;
}

// Helper function to check if current page
function isCurrentPage($pageName) {
    return getCurrentPage() === $pageName;
}
