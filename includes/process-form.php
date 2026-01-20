<?php
/**
 * North Star Wraps - Form Processing
 * Handles contact form submissions with spam protection
 */

session_start();
require_once 'config.php';

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
$rate_limit_file = __DIR__ . '/../logs/rate_limits.json';

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

// Build email content
$email_subject = "New Quote Request - {$service_display}";

$email_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7CB518; color: white; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { background: #f9f9f9; padding: 20px; }
        .section { margin-bottom: 20px; }
        .section h3 { color: #7CB518; margin-bottom: 10px; border-bottom: 2px solid #7CB518; padding-bottom: 5px; }
        .field { margin-bottom: 8px; }
        .label { font-weight: bold; color: #666; }
        .message-box { background: white; padding: 15px; border-left: 4px solid #7CB518; margin-top: 10px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>New Quote Request</h1>
        </div>
        <div class='content'>
            <div class='section'>
                <h3>Contact Information</h3>
                <div class='field'><span class='label'>Name:</span> {$name}</div>
                <div class='field'><span class='label'>Email:</span> {$email}</div>
                <div class='field'><span class='label'>Phone:</span> {$phone}</div>
                <div class='field'><span class='label'>Preferred Contact:</span> " . ucfirst($preferred_contact) . "</div>
            </div>

            <div class='section'>
                <h3>Project Details</h3>
                <div class='field'><span class='label'>Service:</span> {$service_display}</div>
                <div class='field'><span class='label'>Budget Range:</span> {$budget_display}</div>
                <div class='field'><span class='label'>Vehicle:</span> {$vehicle_info}</div>
                <div class='field'><span class='label'>Color/Finish Preference:</span> " . ($color_preference ?: 'Not specified') . "</div>
            </div>

            <div class='section'>
                <h3>Project Description</h3>
                <div class='message-box'>" . nl2br($message) . "</div>
            </div>

            <div class='section'>
                <h3>Additional Info</h3>
                <div class='field'><span class='label'>How they heard about us:</span> {$heard_display}</div>
                <div class='field'><span class='label'>Submitted:</span> " . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
        <div class='footer'>
            This quote request was submitted via the North Star Wraps website.
        </div>
    </div>
</body>
</html>
";

// Email headers
$headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: ' . SITE_NAME . ' Website <noreply@' . $_SERVER['HTTP_HOST'] . '>',
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion()
];

// Send email
$mail_sent = mail(SITE_EMAIL, $email_subject, $email_body, implode("\r\n", $headers));

// Also save to a log file (backup in case email fails)
$log_entry = date('Y-m-d H:i:s') . " | {$name} | {$email} | {$phone} | {$service_display} | {$vehicle_info}\n";
$log_file = __DIR__ . '/../logs/quotes.log';

// Create logs directory if it doesn't exist
$logs_dir = dirname($log_file);
if (!is_dir($logs_dir)) {
    mkdir($logs_dir, 0755, true);
}

file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);

// Send auto-reply to customer
$customer_subject = "Thank you for contacting " . SITE_NAME;
$customer_body = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #7CB518; color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .content { background: #f9f9f9; padding: 30px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .cta { text-align: center; margin: 20px 0; }
        .cta a { background: #7CB518; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>" . SITE_NAME . "</h1>
        </div>
        <div class='content'>
            <p>Hi {$name},</p>

            <p>Thank you for reaching out to " . SITE_NAME . "! We've received your quote request for <strong>{$service_display}</strong> and are excited to help transform your vehicle.</p>

            <p>One of our team members will review your request and get back to you within <strong>24 hours</strong> during business days.</p>

            <p>In the meantime, feel free to:</p>
            <ul>
                <li>Check out our <a href='https://" . $_SERVER['HTTP_HOST'] . "/pages/gallery.php'>gallery</a> for inspiration</li>
                <li>Follow us on social media for our latest work</li>
                <li>Give us a call at " . SITE_PHONE . " if you have any urgent questions</li>
            </ul>

            <p>We look forward to working with you!</p>

            <p>Best regards,<br>The " . SITE_NAME . " Team</p>
        </div>
        <div class='footer'>
            <p>" . SITE_ADDRESS . "</p>
            <p>Phone: " . SITE_PHONE . " | Email: " . SITE_EMAIL . "</p>
        </div>
    </div>
</body>
</html>
";

$customer_headers = [
    'MIME-Version: 1.0',
    'Content-type: text/html; charset=UTF-8',
    'From: ' . SITE_NAME . ' <' . SITE_EMAIL . '>',
    'X-Mailer: PHP/' . phpversion()
];

mail($email, $customer_subject, $customer_body, implode("\r\n", $customer_headers));

// Redirect back to contact page with success message
header('Location: /pages/contact.php?submitted=true');
exit;
