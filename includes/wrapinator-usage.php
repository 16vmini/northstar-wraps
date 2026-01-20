<?php
/**
 * Shared usage tracking for Wrapinator (T-800 and T-1000)
 * Used by both visualize.php (V1) and visualize-v2.php (V2)
 */

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

    // Log the lead if log directory provided
    if ($log_dir && is_dir($log_dir)) {
        $lead_log = $log_dir . '/visualizer_leads.log';
        $log_entry = date('Y-m-d H:i:s') . ' | ' . $email . ' | ' . $_SERVER['REMOTE_ADDR'] . "\n";
        file_put_contents($lead_log, $log_entry, FILE_APPEND);
    }

    return [
        'success' => true,
        'message' => 'Email saved. You can now continue using the Wrapinator.',
        'remaining' => $limits['max_limit'] - $_SESSION['visualizer_count']
    ];
}
