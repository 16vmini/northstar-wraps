<?php
/**
 * Email Sender - Brevo API Integration
 * Sends transactional emails via Brevo (formerly Sendinblue)
 */

require_once __DIR__ . '/email-config.php';

/**
 * Send an email via Brevo API
 *
 * @param string $to_email Recipient email address
 * @param string $to_name Recipient name
 * @param string $subject Email subject
 * @param string $html_content HTML email body
 * @param string|null $reply_to Optional reply-to email
 * @return array ['success' => bool, 'message' => string, 'message_id' => string|null]
 */
function sendEmailViaBevo($to_email, $to_name, $subject, $html_content, $reply_to = null) {
    $api_key = defined('BREVO_API_KEY') ? BREVO_API_KEY : '';

    // If no API key, fall back to PHP mail
    if (empty($api_key) || $api_key === 'YOUR_API_KEY_HERE') {
        return sendEmailViaPhpMail($to_email, $to_name, $subject, $html_content, $reply_to);
    }

    $url = 'https://api.brevo.com/v3/smtp/email';

    $data = [
        'sender' => [
            'name' => defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'North Star Wrap',
            'email' => defined('EMAIL_FROM_ADDRESS') ? EMAIL_FROM_ADDRESS : 'noreply@northstarwrap.com'
        ],
        'to' => [
            [
                'email' => $to_email,
                'name' => $to_name
            ]
        ],
        'subject' => $subject,
        'htmlContent' => $html_content
    ];

    // Add reply-to if provided
    if ($reply_to) {
        $data['replyTo'] = ['email' => $reply_to];
    }

    $headers = [
        'accept: application/json',
        'api-key: ' . $api_key,
        'content-type: application/json'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    // Log to file for debugging
    $log_dir = dirname(__DIR__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $debug_log = $log_dir . '/brevo_debug.log';

    if ($error) {
        // cURL error - log and fall back to PHP mail
        file_put_contents($debug_log, date('Y-m-d H:i:s') . " | cURL Error: {$error}\n", FILE_APPEND);
        return sendEmailViaPhpMail($to_email, $to_name, $subject, $html_content, $reply_to);
    }

    $result = json_decode($response, true);

    // Log the response
    file_put_contents($debug_log, date('Y-m-d H:i:s') . " | HTTP {$http_code} | To: {$to_email} | Response: {$response}\n", FILE_APPEND);

    if ($http_code >= 200 && $http_code < 300) {
        return [
            'success' => true,
            'message' => 'Email sent successfully via Brevo',
            'message_id' => $result['messageId'] ?? null
        ];
    }

    // API error - log and fall back to PHP mail
    file_put_contents($debug_log, date('Y-m-d H:i:s') . " | FALLBACK to PHP mail due to error\n", FILE_APPEND);
    return sendEmailViaPhpMail($to_email, $to_name, $subject, $html_content, $reply_to);
}

/**
 * Fallback: Send email via PHP mail()
 */
function sendEmailViaPhpMail($to_email, $to_name, $subject, $html_content, $reply_to = null) {
    $from_name = defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'North Star Wrap';
    $from_email = defined('EMAIL_FROM_ADDRESS') ? EMAIL_FROM_ADDRESS : 'noreply@northstarwrap.com';

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . $from_name . ' <' . $from_email . '>',
        'X-Mailer: PHP/' . phpversion()
    ];

    if ($reply_to) {
        $headers[] = 'Reply-To: ' . $reply_to;
    }

    $to = $to_name ? "{$to_name} <{$to_email}>" : $to_email;
    $sent = mail($to, $subject, $html_content, implode("\r\n", $headers));

    return [
        'success' => $sent,
        'message' => $sent ? 'Email sent via PHP mail (fallback)' : 'Failed to send email',
        'message_id' => null
    ];
}

/**
 * Send contact form notification to business
 */
function sendContactNotification($form_data) {
    $to_email = defined('EMAIL_TO_ADDRESS') ? EMAIL_TO_ADDRESS : SITE_EMAIL;
    $to_name = defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'North Star Wrap';

    $subject = "New Quote Request - " . ($form_data['service_display'] ?? 'General Inquiry');

    $html_content = buildNotificationEmail($form_data);

    return sendEmailViaBevo($to_email, $to_name, $subject, $html_content, $form_data['email'] ?? null);
}

/**
 * Send auto-reply to customer
 */
function sendCustomerAutoReply($form_data) {
    $to_email = $form_data['email'];
    $to_name = $form_data['name'];

    $subject = "Thank you for contacting " . (defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : 'North Star Wrap');

    $html_content = buildAutoReplyEmail($form_data);

    return sendEmailViaBevo($to_email, $to_name, $subject, $html_content);
}

/**
 * Build HTML for notification email
 */
function buildNotificationEmail($data) {
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'North Star Wrap';

    return "
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
                <div class='field'><span class='label'>Name:</span> {$data['name']}</div>
                <div class='field'><span class='label'>Email:</span> {$data['email']}</div>
                <div class='field'><span class='label'>Phone:</span> {$data['phone']}</div>
                <div class='field'><span class='label'>Preferred Contact:</span> " . ucfirst($data['preferred_contact'] ?? 'email') . "</div>
            </div>

            <div class='section'>
                <h3>Project Details</h3>
                <div class='field'><span class='label'>Service:</span> {$data['service_display']}</div>
                <div class='field'><span class='label'>Budget Range:</span> {$data['budget_display']}</div>
                <div class='field'><span class='label'>Vehicle:</span> {$data['vehicle_info']}</div>
                <div class='field'><span class='label'>Color/Finish Preference:</span> " . ($data['color_preference'] ?: 'Not specified') . "</div>
            </div>

            <div class='section'>
                <h3>Project Description</h3>
                <div class='message-box'>" . nl2br($data['message']) . "</div>
            </div>

            <div class='section'>
                <h3>Additional Info</h3>
                <div class='field'><span class='label'>How they heard about us:</span> {$data['heard_display']}</div>
                <div class='field'><span class='label'>Submitted:</span> " . date('F j, Y \a\t g:i A') . "</div>
            </div>
        </div>
        <div class='footer'>
            This quote request was submitted via the {$site_name} website.
        </div>
    </div>
</body>
</html>";
}

/**
 * Build HTML for auto-reply email
 */
function buildAutoReplyEmail($data) {
    $site_name = defined('SITE_NAME') ? SITE_NAME : 'North Star Wrap';
    $site_phone = defined('SITE_PHONE') ? SITE_PHONE : '';
    $site_email = defined('SITE_EMAIL') ? SITE_EMAIL : '';
    $site_address = defined('SITE_ADDRESS') ? SITE_ADDRESS : '';
    $host = $_SERVER['HTTP_HOST'] ?? 'northstarwrap.com';

    return "
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
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>{$site_name}</h1>
        </div>
        <div class='content'>
            <p>Hi {$data['name']},</p>

            <p>Thank you for reaching out to {$site_name}! We've received your quote request for <strong>{$data['service_display']}</strong> and are excited to help transform your vehicle.</p>

            <p>One of our team members will review your request and get back to you within <strong>24 hours</strong> during business days.</p>

            <p>In the meantime, feel free to:</p>
            <ul>
                <li>Check out our <a href='https://{$host}/pages/gallery.php'>gallery</a> for inspiration</li>
                <li>Follow us on social media for our latest work</li>
                <li>Give us a call at {$site_phone} if you have any urgent questions</li>
            </ul>

            <p>We look forward to working with you!</p>

            <p>Best regards,<br>The {$site_name} Team</p>
        </div>
        <div class='footer'>
            <p>{$site_address}</p>
            <p>Phone: {$site_phone} | Email: {$site_email}</p>
        </div>
    </div>
</body>
</html>";
}
