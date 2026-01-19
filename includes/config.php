<?php
/**
 * North Star Wraps - Configuration File
 */

// Site Configuration
define('SITE_NAME', 'North Star Wraps');
define('SITE_TAGLINE', 'Premium Vehicle Wrapping Services');
define('SITE_EMAIL', 'info@northstarwraps.com');
define('SITE_PHONE', '(555) 123-4567');
define('SITE_ADDRESS', '123 Auto Drive, Your City, ST 12345');

// Social Media Links
define('SOCIAL_FACEBOOK', 'https://facebook.com/northstarwraps');
define('SOCIAL_INSTAGRAM', 'https://instagram.com/northstarwraps');
define('SOCIAL_TIKTOK', 'https://tiktok.com/@northstarwraps');

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
        'price_from' => 2500
    ],
    [
        'id' => 'partial-wrap',
        'name' => 'Partial Wraps',
        'short_desc' => 'Accent wraps for hoods, roofs, mirrors, and more.',
        'description' => 'Add style without committing to a full wrap. Perfect for hoods, roofs, mirrors, spoilers, or any combination of panels. Create a custom two-tone look or add racing stripes.',
        'icon' => 'fa-palette',
        'price_from' => 500
    ],
    [
        'id' => 'commercial',
        'name' => 'Commercial & Fleet',
        'short_desc' => 'Turn your vehicles into mobile billboards.',
        'description' => 'Make your business stand out with professional vehicle graphics. From single work vans to entire fleets, we create eye-catching designs that advertise your brand wherever you go.',
        'icon' => 'fa-truck',
        'price_from' => 1500
    ],
    [
        'id' => 'ppf',
        'name' => 'Paint Protection Film',
        'short_desc' => 'Invisible protection against chips, scratches, and debris.',
        'description' => 'Shield your vehicle\'s paint with premium clear protection film. Self-healing technology keeps your car looking showroom fresh while guarding against rock chips, bug splatter, and minor scratches.',
        'icon' => 'fa-shield-alt',
        'price_from' => 800
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
