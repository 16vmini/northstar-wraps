<?php
/**
 * Version tracking for cache busting
 * Increment these when making changes
 */

// Main site version (CSS, main.js, general changes)
if (!defined('SITE_VERSION')) {
    define('SITE_VERSION', '1.0.3');
}

// Calculator version (calculator.php, calculator.js, pricing-config.json)
if (!defined('CALC_VERSION')) {
    define('CALC_VERSION', '1.0.1');
}
