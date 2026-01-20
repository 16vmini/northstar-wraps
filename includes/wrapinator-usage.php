<?php
/**
 * Shared usage tracking for Wrapinator (T-800 and T-1000)
 * Used by both visualize.php (V1) and visualize-v2.php (V2)
 */

require_once __DIR__ . '/email-sender.php';

/**
 * Initialize session usage tracking
 */
function initWrapinatorUsage() {
    if (!isset($_SESSION['visualizer_count'])) {
        $_SESSION['visualizer_count'] = 0;
    }
    if (!isset($_SESSION['visualizer_email'])) {
        $_SESSION['visualizer_email'] = '';
    }
}

/**
 * Get usage limits
 * @return array ['free_limit' => int, 'max_limit' => int]
 */
function getWrapinatorLimits() {
    return [
        'free_limit' => defined('VISUALIZER_FREE_LIMIT') ? VISUALIZER_FREE_LIMIT : 2,
        'max_limit' => defined('VISUALIZER_MAX_LIMIT') ? VISUALIZER_MAX_LIMIT : 10
    ];
}

/**
 * Get current usage status
 * @return array Usage info including remaining, limits, etc.
 */
function getWrapinatorStatus() {
    initWrapinatorUsage();
    $limits = getWrapinatorLimits();

    $remaining = $_SESSION['visualizer_email']
        ? $limits['max_limit'] - $_SESSION['visualizer_count']
        : $limits['free_limit'] - $_SESSION['visualizer_count'];

    return [
        'used' => $_SESSION['visualizer_count'],
        'free_limit' => $limits['free_limit'],
        'max_limit' => $limits['max_limit'],
        'has_email' => !empty($_SESSION['visualizer_email']),
        'remaining' => max(0, $remaining),
        'needs_email' => $remaining <= 0 && empty($_SESSION['visualizer_email'])
    ];
}

/**
 * Check if user can generate (not over limit)
 * @return array ['allowed' => bool, 'error' => string|null, 'error_code' => string|null]
 */
function checkWrapinatorUsage() {
    initWrapinatorUsage();
    $limits = getWrapinatorLimits();

    // If over free limit and no email, require email
    if ($_SESSION['visualizer_count'] >= $limits['free_limit'] && empty($_SESSION['visualizer_email'])) {
        return [
            'allowed' => false,
            'error' => 'email_required',
            'message' => 'Please enter your email to continue using the Wrapinator',
            'used' => $_SESSION['visualizer_count'],
            'limit' => $limits['free_limit']
        ];
    }

    // If over max limit, block
    if ($_SESSION['visualizer_count'] >= $limits['max_limit']) {
        return [
            'allowed' => false,
            'error' => 'limit_reached',
            'message' => 'You have reached the maximum number of visualizations. Please contact us for more.',
            'used' => $_SESSION['visualizer_count'],
            'limit' => $limits['max_limit']
        ];
    }

    return ['allowed' => true, 'error' => null];
}

/**
 * Increment usage counter after successful generation
 * @return array Updated usage status
 */
function incrementWrapinatorUsage() {
    initWrapinatorUsage();
    $_SESSION['visualizer_count']++;
    return getWrapinatorStatus();
}

/**
 * Save email and extend usage limit
 * @param string $email User's email address
 * @param string $log_dir Directory for logging
 * @return array Result with remaining count
 */
function saveWrapinatorEmail($email, $log_dir = null) {
    initWrapinatorUsage();
    $limits = getWrapinatorLimits();

    $_SESSION['visualizer_email'] = $email;

    // Log the lead
    if (!$log_dir) {
        $log_dir = dirname(__DIR__) . '/logs';
    }
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $lead_log = $log_dir . '/visualizer_leads.log';
    $log_entry = date('Y-m-d H:i:s') . ' | ' . $email . ' | ' . $_SERVER['REMOTE_ADDR'] . "\n";
    file_put_contents($lead_log, $log_entry, FILE_APPEND);

    // Send notification email to business
    sendWrapinatorLeadNotification($email);

    return [
        'success' => true,
        'message' => 'Email saved. You can now continue using the Wrapinator.',
        'remaining' => $limits['max_limit'] - $_SESSION['visualizer_count']
    ];
}

/**
 * Send email notification when a new Wrapinator lead is captured
 * @param string $email The lead's email address
 */
function sendWrapinatorLeadNotification($email) {
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'North Star Wrap';
    $site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'northstarwrap.com');

    $subject = "New Wrapinator Lead - {$email}";

    $html_content = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1a1a1a; color: #fff; padding: 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; }
            .content { padding: 30px 20px; background: #f9f9f9; }
            .lead-box { background: #fff; border-left: 4px solid #7cb518; padding: 20px; margin: 20px 0; }
            .lead-box h3 { margin-top: 0; color: #7cb518; }
            .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>{$site_name}</h1>
            </div>
            <div class='content'>
                <h2>New Wrapinator Lead!</h2>
                <p>Someone has entered their email to continue using the Wrapinator tool.</p>

                <div class='lead-box'>
                    <h3>Lead Details</h3>
                    <p><strong>Email:</strong> {$email}</p>
                    <p><strong>Date:</strong> " . date('j M Y, g:i a') . "</p>
                    <p><strong>IP Address:</strong> " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "</p>
                </div>

                <p>This person is interested in vehicle wrapping - consider following up with them!</p>
            </div>
            <div class='footer'>
                <p>This notification was sent from the Wrapinator on {$site_url}</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Send to business email(s)
    $to_email = defined('SITE_EMAIL') ? SITE_EMAIL : 'info@northstarwrap.com';
    sendEmailViaBevo($to_email, $site_name, $subject, $html_content, $email);
}
