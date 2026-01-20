<?php
/**
 * Shared usage tracking for Wrapinator (T-800 and T-1000)
 * Used by both visualize.php (V1) and visualize-v2.php (V2)
 *
 * Uses both session (cookie) and IP-based tracking.
 * IP tracking resets every 24 hours to allow returning users more attempts.
 */

require_once __DIR__ . '/email-sender.php';

/**
 * Get the IP usage log file path
 */
function getIpUsageFile() {
    $log_dir = dirname(__DIR__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    return $log_dir . '/wrapinator_ip_usage.json';
}

/**
 * Get usage count for an IP address (resets after 24 hours)
 * @param string $ip IP address
 * @return array ['count' => int, 'has_email' => bool]
 */
function getIpUsage($ip) {
    $file = getIpUsageFile();
    $data = [];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: [];
    }

    // Check if IP exists and if it's within 24 hours
    if (isset($data[$ip])) {
        $last_reset = $data[$ip]['reset_time'] ?? 0;
        $hours_since_reset = (time() - $last_reset) / 3600;

        if ($hours_since_reset >= 24) {
            // Reset after 24 hours
            return ['count' => 0, 'has_email' => $data[$ip]['has_email'] ?? false];
        }

        return [
            'count' => $data[$ip]['count'] ?? 0,
            'has_email' => $data[$ip]['has_email'] ?? false
        ];
    }

    return ['count' => 0, 'has_email' => false];
}

/**
 * Increment IP usage count
 * @param string $ip IP address
 */
function incrementIpUsage($ip) {
    $file = getIpUsageFile();
    $data = [];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: [];
    }

    // Check if we need to reset (24 hour window)
    $should_reset = false;
    if (isset($data[$ip])) {
        $last_reset = $data[$ip]['reset_time'] ?? 0;
        $hours_since_reset = (time() - $last_reset) / 3600;
        if ($hours_since_reset >= 24) {
            $should_reset = true;
        }
    }

    if (!isset($data[$ip]) || $should_reset) {
        $data[$ip] = [
            'count' => 1,
            'reset_time' => time(),
            'has_email' => false
        ];
    } else {
        $data[$ip]['count']++;
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

/**
 * Mark IP as having submitted email
 * @param string $ip IP address
 */
function markIpHasEmail($ip) {
    $file = getIpUsageFile();
    $data = [];

    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true) ?: [];
    }

    if (!isset($data[$ip])) {
        $data[$ip] = [
            'count' => 0,
            'reset_time' => time(),
            'has_email' => true
        ];
    } else {
        $data[$ip]['has_email'] = true;
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

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
 * Get current usage status (uses higher of session or IP count)
 * @return array Usage info including remaining, limits, etc.
 */
function getWrapinatorStatus() {
    initWrapinatorUsage();
    $limits = getWrapinatorLimits();

    // Get IP-based usage
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ip_usage = getIpUsage($ip);

    // Use the higher count between session and IP
    $used = max($_SESSION['visualizer_count'], $ip_usage['count']);
    $has_email = !empty($_SESSION['visualizer_email']) || $ip_usage['has_email'];

    $remaining = $has_email
        ? $limits['max_limit'] - $used
        : $limits['free_limit'] - $used;

    return [
        'used' => $used,
        'free_limit' => $limits['free_limit'],
        'max_limit' => $limits['max_limit'],
        'has_email' => $has_email,
        'remaining' => max(0, $remaining),
        'needs_email' => $remaining <= 0 && !$has_email
    ];
}

/**
 * Check if user can generate (not over limit)
 * Uses both session and IP tracking to prevent cookie-clearing bypass
 * @return array ['allowed' => bool, 'error' => string|null, 'error_code' => string|null]
 */
function checkWrapinatorUsage() {
    // TEMP: Limits disabled for testing - remove this line to re-enable
    return ['allowed' => true, 'error' => null];

    initWrapinatorUsage();
    $limits = getWrapinatorLimits();

    // Get IP-based usage
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $ip_usage = getIpUsage($ip);

    // Use the higher count between session and IP
    $used = max($_SESSION['visualizer_count'], $ip_usage['count']);
    $has_email = !empty($_SESSION['visualizer_email']) || $ip_usage['has_email'];

    // If over free limit and no email, require email
    if ($used >= $limits['free_limit'] && !$has_email) {
        return [
            'allowed' => false,
            'error' => 'email_required',
            'message' => 'Please enter your email to continue using the Wrapinator',
            'used' => $used,
            'limit' => $limits['free_limit']
        ];
    }

    // If over max limit, block
    if ($used >= $limits['max_limit']) {
        return [
            'allowed' => false,
            'error' => 'limit_reached',
            'message' => 'You have reached the maximum number of visualizations. Come back tomorrow for more!',
            'used' => $used,
            'limit' => $limits['max_limit']
        ];
    }

    return ['allowed' => true, 'error' => null];
}

/**
 * Increment usage counter after successful generation
 * Increments both session and IP-based counters
 * @return array Updated usage status
 */
function incrementWrapinatorUsage() {
    initWrapinatorUsage();
    $_SESSION['visualizer_count']++;

    // Also increment IP-based counter
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip) {
        incrementIpUsage($ip);
    }

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

    // Also mark IP as having submitted email
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($ip) {
        markIpHasEmail($ip);
    }

    // Log the lead
    if (!$log_dir) {
        $log_dir = dirname(__DIR__) . '/logs';
    }
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $lead_log = $log_dir . '/visualizer_leads.log';
    $log_entry = date('Y-m-d H:i:s') . ' | ' . $email . ' | ' . $ip . "\n";
    file_put_contents($lead_log, $log_entry, FILE_APPEND);

    // Send notification email to business
    sendWrapinatorLeadNotification($email);

    // Get current status for accurate remaining count
    $status = getWrapinatorStatus();

    return [
        'success' => true,
        'message' => 'Email saved. You can now continue using the Wrapinator.',
        'remaining' => $status['remaining']
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
