<?php
/**
 * Wrapinator Leads Digest
 * Run monthly via cron or manually to send lead summary and archive log
 *
 * Usage: php send-leads-digest.php
 * Or visit: /admin/send-leads-digest.php (password protected)
 */

// Allow CLI or web access
$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    session_start();
    require_once '../includes/config.php';

    // Simple password protection for web access
    $admin_password = 'northstar2024';

    if (isset($_POST['password'])) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['leads_admin'] = true;
        }
    }

    if (!isset($_SESSION['leads_admin'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Send Leads Digest</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
                .box { max-width: 400px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; }
                input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
                button { width: 100%; padding: 12px; background: #7cb518; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
            </style>
        </head>
        <body>
            <div class="box">
                <h2>Send Leads Digest</h2>
                <form method="POST">
                    <input type="password" name="password" placeholder="Admin password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} else {
    require_once __DIR__ . '/../includes/config.php';
}

require_once __DIR__ . '/../includes/email-sender.php';

// Config
$log_dir = __DIR__ . '/../logs';
$leads_file = $log_dir . '/visualizer_leads.log';
$archive_dir = $log_dir . '/archive';

// Output helper
function output($msg) {
    global $is_cli;
    if ($is_cli) {
        echo $msg . "\n";
    } else {
        echo "<p>" . htmlspecialchars($msg) . "</p>";
    }
}

if (!$is_cli) {
    echo "<!DOCTYPE html><html><head><title>Leads Digest</title>";
    echo "<style>body{font-family:Arial,sans-serif;padding:40px;max-width:800px;margin:0 auto;}</style>";
    echo "</head><body><h1>Wrapinator Leads Digest</h1>";
}

// Check if log file exists
if (!file_exists($leads_file)) {
    output("No leads file found at: $leads_file");
    output("Nothing to send.");
    exit;
}

// Read leads
$leads_content = file_get_contents($leads_file);
$lines = array_filter(explode("\n", trim($leads_content)));

if (empty($lines)) {
    output("Leads file is empty. Nothing to send.");
    exit;
}

output("Found " . count($lines) . " leads to process.");

// Parse leads
$leads = [];
foreach ($lines as $line) {
    $parts = explode(' | ', $line);
    if (count($parts) >= 2) {
        $leads[] = [
            'date' => $parts[0] ?? '',
            'email' => $parts[1] ?? '',
            'ip' => $parts[2] ?? ''
        ];
    }
}

// Build email content
$site_name = defined('SITE_NAME') ? SITE_NAME : 'North Star Wrap';
$lead_count = count($leads);
$month_name = date('F Y');

$html_content = "
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 700px; margin: 0 auto; padding: 20px; }
        .header { background: #1a1a1a; color: #fff; padding: 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px 20px; background: #f9f9f9; }
        .summary { background: #7cb518; color: #fff; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        .summary h2 { margin: 0; font-size: 36px; }
        .summary p { margin: 5px 0 0; opacity: 0.9; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #1a1a1a; color: #fff; }
        tr:hover { background: #f5f5f5; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>{$site_name}</h1>
        </div>
        <div class='content'>
            <div class='summary'>
                <h2>{$lead_count}</h2>
                <p>Wrapinator Leads - {$month_name}</p>
            </div>

            <p>Here are all the email addresses collected from users who wanted to continue using the Wrapinator tool:</p>

            <table>
                <tr>
                    <th>Date</th>
                    <th>Email</th>
                    <th>IP Address</th>
                </tr>";

foreach ($leads as $lead) {
    $html_content .= "
                <tr>
                    <td>{$lead['date']}</td>
                    <td><a href='mailto:{$lead['email']}'>{$lead['email']}</a></td>
                    <td>{$lead['ip']}</td>
                </tr>";
}

$html_content .= "
            </table>

            <p style='margin-top: 20px;'><strong>Tip:</strong> These are warm leads - they're interested in vehicle wrapping! Consider sending them a follow-up email with your services and any current promotions.</p>
        </div>
        <div class='footer'>
            <p>This digest was generated on " . date('j M Y, g:i a') . "</p>
        </div>
    </div>
</body>
</html>
";

// Send the email
$to_email = defined('SITE_EMAIL') ? SITE_EMAIL : 'info@northstarwrap.com';
$subject = "Wrapinator Leads Digest - {$lead_count} leads ({$month_name})";

output("Sending digest email to: $to_email");

$result = sendEmailViaBevo($to_email, $site_name, $subject, $html_content);

if ($result['success']) {
    output("Email sent successfully!");

    // Archive the log file
    if (!is_dir($archive_dir)) {
        mkdir($archive_dir, 0755, true);
    }

    $archive_filename = 'visualizer_leads_' . date('Y-m') . '.log';
    $archive_path = $archive_dir . '/' . $archive_filename;

    // Append to existing archive if it exists (in case run multiple times in same month)
    if (file_exists($archive_path)) {
        file_put_contents($archive_path, $leads_content . "\n", FILE_APPEND);
    } else {
        copy($leads_file, $archive_path);
    }

    // Clear the main log file
    file_put_contents($leads_file, '');

    output("Log archived to: logs/archive/$archive_filename");
    output("Main log file cleared.");

} else {
    output("Failed to send email: " . $result['message']);
}

if (!$is_cli) {
    echo "<p><a href='/admin/gallery'>Back to Gallery Admin</a></p>";
    echo "</body></html>";
}
