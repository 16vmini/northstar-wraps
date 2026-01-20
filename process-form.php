<?php
/**
 * North Star Wraps - Form Processing
 * Handles contact form submissions with spam protection
 */

session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/email-sender.php';

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/contact.php');
    exit;
}

/**
 * SPAM PROTECTION MEASURES
 */

// 1. Honeypot spam check - bots fill hidden fields
if (!empty($_POST['website'])) {
    // Bot detected, silently redirect (don't let them know)
    sleep(2); // Slow down bots
    header('Location: /pages/contact.php?submitted=true');
    exit;
}

// 2. CSRF Token validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header('Location: /pages/contact.php?error=' . urlencode('Session expired. Please try again.'));
    exit;
}

// 3. Time-based check - form submitted too quickly (bots are fast)
$form_time = isset($_POST['form_time']) ? (int)$_POST['form_time'] : 0;
$time_diff = time() - $form_time;
if ($time_diff < 3) {
    // Form submitted in less than 3 seconds - likely a bot
    sleep(3);
    header('Location: /pages/contact.php?submitted=true');
    exit;
}

// 4. Rate limiting - max 3 submissions per hour per IP
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_file = __DIR__ . '/logs/rate_limits.json';

// Create logs directory if needed
$logs_dir = dirname($rate_limit_file);
if (!is_dir($logs_dir)) {
    mkdir($logs_dir, 0755, true);
}

// Load existing rate limits
$rate_limits = [];
if (file_exists($rate_limit_file)) {
    $rate_limits = json_decode(file_get_contents($rate_limit_file), true) ?: [];
}

// Clean old entries (older than 1 hour)
$one_hour_ago = time() - 3600;
foreach ($rate_limits as $stored_ip => $timestamps) {
    $rate_limits[$stored_ip] = array_filter($timestamps, function($t) use ($one_hour_ago) {
        return $t > $one_hour_ago;
    });
    if (empty($rate_limits[$stored_ip])) {
        unset($rate_limits[$stored_ip]);
    }
}

// Check current IP
$ip_submissions = isset($rate_limits[$ip]) ? count($rate_limits[$ip]) : 0;
if ($ip_submissions >= 3) {
    header('Location: /pages/contact.php?error=' . urlencode('Too many submissions. Please try again later.'));
    exit;
}

// Record this submission
if (!isset($rate_limits[$ip])) {
    $rate_limits[$ip] = [];
}
$rate_limits[$ip][] = time();
file_put_contents($rate_limit_file, json_encode($rate_limits), LOCK_EX);

// Clear CSRF token after use (one-time use)
unset($_SESSION['csrf_token']);

// Sanitize and validate input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Remove non-numeric characters for validation
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10;
}

// Collect and sanitize form data
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$preferred_contact = sanitize($_POST['preferred_contact'] ?? 'email');
$service = sanitize($_POST['service'] ?? '');
$budget = sanitize($_POST['budget'] ?? '');
$vehicle_year = sanitize($_POST['vehicle_year'] ?? '');
$vehicle_make = sanitize($_POST['vehicle_make'] ?? '');
$vehicle_model = sanitize($_POST['vehicle_model'] ?? '');
$color_preference = sanitize($_POST['color_preference'] ?? '');
$message = sanitize($_POST['message'] ?? '');
$how_heard = sanitize($_POST['how_heard'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !validateEmail($email)) {
    $errors[] = 'Valid email is required';
}

if (empty($phone) || !validatePhone($phone)) {
    $errors[] = 'Valid phone number is required';
}

if (empty($service)) {
    $errors[] = 'Please select a service';
}

if (empty($message)) {
    $errors[] = 'Please describe your project';
}

// If validation fails, redirect with error
if (!empty($errors)) {
    $error_message = urlencode(implode('. ', $errors));
    header("Location: /pages/contact.php?error={$error_message}");
    exit;
}

// Format service name
$service_names = [
    'full-wrap' => 'Full Vehicle Wrap',
    'partial-wrap' => 'Partial Wrap',
    'commercial' => 'Commercial/Fleet Graphics',
    'ppf' => 'Paint Protection Film',
    'chrome-delete' => 'Chrome Delete',
    'custom-design' => 'Custom Design',
    'other' => 'Other/Not Sure'
];
$service_display = $service_names[$service] ?? $service;

// Format budget
$budget_ranges = [
    'under-1000' => 'Under £1,000',
    '1000-2500' => '£1,000 - £2,500',
    '2500-5000' => '£2,500 - £5,000',
    '5000-10000' => '£5,000 - £10,000',
    'over-10000' => '£10,000+',
    'not-sure' => 'Not Sure Yet'
];
$budget_display = $budget_ranges[$budget] ?? 'Not specified';

// Format how heard
$heard_options = [
    'google' => 'Google Search',
    'instagram' => 'Instagram',
    'facebook' => 'Facebook',
    'tiktok' => 'TikTok',
    'referral' => 'Friend/Family Referral',
    'saw-vehicle' => 'Saw a Wrapped Vehicle',
    'other' => 'Other'
];
$heard_display = $heard_options[$how_heard] ?? 'Not specified';

// Build vehicle info string
$vehicle_info = trim("{$vehicle_year} {$vehicle_make} {$vehicle_model}");
if (empty($vehicle_info)) {
    $vehicle_info = 'Not specified';
}

// Prepare form data for email functions
$form_data = [
    'name' => $name,
    'email' => $email,
    'phone' => $phone,
    'preferred_contact' => $preferred_contact,
    'service_display' => $service_display,
    'budget_display' => $budget_display,
    'vehicle_info' => $vehicle_info,
    'color_preference' => $color_preference,
    'message' => $message,
    'heard_display' => $heard_display
];

// Save to log file (backup in case email fails)
$log_entry = date('Y-m-d H:i:s') . " | {$name} | {$email} | {$phone} | {$service_display} | {$vehicle_info}\n";
$log_file = __DIR__ . '/logs/quotes.log';

// Create logs directory if it doesn't exist
$logs_dir = dirname($log_file);
if (!is_dir($logs_dir)) {
    mkdir($logs_dir, 0755, true);
}

file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Send notification email to business (via Brevo or fallback to PHP mail)
$notification_result = sendContactNotification($form_data);

// Send auto-reply to customer
$autoreply_result = sendCustomerAutoReply($form_data);

// Log email results
$email_log = date('Y-m-d H:i:s') . " | Notification: " . ($notification_result['success'] ? 'OK' : 'FAIL') .
             " | AutoReply: " . ($autoreply_result['success'] ? 'OK' : 'FAIL') . "\n";
file_put_contents(__DIR__ . '/logs/email.log', $email_log, FILE_APPEND | LOCK_EX);

// Redirect back to contact page with success message
header('Location: /pages/contact.php?submitted=true');
exit;
