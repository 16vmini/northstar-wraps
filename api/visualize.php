<?php
/**
 * Wrap Visualizer API Endpoint
 * Handles image generation via Replicate FLUX Kontext Pro
 */

session_start();
header('Content-Type: application/json');

// Load configs
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api-config.php';

/**
 * Add watermark to generated image
 */
function addWatermark($base64_image) {
    // Decode the image
    $image_data = base64_decode($base64_image);
    $image = imagecreatefromstring($image_data);

    if (!$image) {
        return $base64_image; // Return original if can't process
    }

    $width = imagesx($image);
    $height = imagesy($image);

    // Create watermark text
    $watermark_text = 'northstarwrap.com';

    // Try to use a nicer font, fallback to built-in
    $font_size = 5; // Built-in font size (1-5)
    $text_width = imagefontwidth($font_size) * strlen($watermark_text);
    $text_height = imagefontheight($font_size);

    // Position: bottom right with padding
    $padding = 15;
    $x = $width - $text_width - $padding;
    $y = $height - $text_height - $padding;

    // Semi-transparent white background
    $bg_color = imagecolorallocatealpha($image, 0, 0, 0, 80);
    imagefilledrectangle($image, $x - 10, $y - 5, $width - $padding + 5, $height - $padding + 5, $bg_color);

    // White text
    $text_color = imagecolorallocate($image, 255, 255, 255);
    imagestring($image, $font_size, $x, $y, $watermark_text, $text_color);

    // Also add a small logo/brand in corner
    $brand_text = 'NORTH STAR WRAP';
    $brand_width = imagefontwidth(3) * strlen($brand_text);
    $brand_x = $padding;
    $brand_y = $height - imagefontheight(3) - $padding;

    // Green brand color background
    $brand_bg = imagecolorallocatealpha($image, 124, 181, 24, 40);
    imagefilledrectangle($image, $brand_x - 8, $brand_y - 5, $brand_x + $brand_width + 8, $brand_y + imagefontheight(3) + 5, $brand_bg);

    $brand_color = imagecolorallocate($image, 255, 255, 255);
    imagestring($image, 3, $brand_x, $brand_y, $brand_text, $brand_color);

    // Convert back to base64
    ob_start();
    imagepng($image);
    $output = ob_get_clean();
    imagedestroy($image);

    return base64_encode($output);
}

/**
 * Upload image to a temporary hosting service and get URL
 * Replicate needs a URL, not base64
 */
function uploadImageForReplicate($base64_data) {
    // Remove data URL prefix if present
    $base64_data = preg_replace('/^data:image\/\w+;base64,/', '', $base64_data);

    // Use imgbb.com free API for temporary image hosting
    // Or we can use data URI directly with Replicate (they support it)
    // Let's try data URI first as it's simpler
    return 'data:image/png;base64,' . $base64_data;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Initialize session tracking
if (!isset($_SESSION['visualizer_count'])) {
    $_SESSION['visualizer_count'] = 0;
}
if (!isset($_SESSION['visualizer_email'])) {
    $_SESSION['visualizer_email'] = null;
}

// Check rate limits
$free_limit = defined('VISUALIZER_FREE_LIMIT') ? VISUALIZER_FREE_LIMIT : 2;
$max_limit = defined('VISUALIZER_MAX_LIMIT') ? VISUALIZER_MAX_LIMIT : 10;

// Get input FIRST before checking rate limits
// This allows email submission and status checks to work regardless of limits
$input = json_decode(file_get_contents('php://input'), true);

// Handle email submission (before rate limit check)
if (isset($input['action']) && $input['action'] === 'submit_email') {
    $email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid email address']);
        exit;
    }

    $_SESSION['visualizer_email'] = $email;

    // Log the lead
    $log_dir = dirname(__DIR__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $lead_log = $log_dir . '/visualizer_leads.log';
    $log_entry = date('Y-m-d H:i:s') . " | {$email} | IP: {$_SERVER['REMOTE_ADDR']}\n";
    file_put_contents($lead_log, $log_entry, FILE_APPEND | LOCK_EX);

    echo json_encode([
        'success' => true,
        'message' => 'Email saved. You can now continue using the visualizer.',
        'remaining' => $max_limit - $_SESSION['visualizer_count']
    ]);
    exit;
}

// Handle status check (before rate limit check)
if (isset($input['action']) && $input['action'] === 'status') {
    echo json_encode([
        'used' => $_SESSION['visualizer_count'],
        'free_limit' => $free_limit,
        'max_limit' => $max_limit,
        'has_email' => !empty($_SESSION['visualizer_email']),
        'remaining' => $_SESSION['visualizer_email']
            ? $max_limit - $_SESSION['visualizer_count']
            : $free_limit - $_SESSION['visualizer_count']
    ]);
    exit;
}

// Now check rate limits (only for visualization requests)
// If over free limit and no email, require email
if ($_SESSION['visualizer_count'] >= $free_limit && !$_SESSION['visualizer_email']) {
    http_response_code(403);
    echo json_encode([
        'error' => 'email_required',
        'message' => 'Please enter your email to continue using the visualizer',
        'used' => $_SESSION['visualizer_count'],
        'limit' => $free_limit
    ]);
    exit;
}

// If over max limit, block
if ($_SESSION['visualizer_count'] >= $max_limit) {
    http_response_code(429);
    echo json_encode([
        'error' => 'limit_reached',
        'message' => 'You have reached the maximum number of visualizations. Please contact us for more.',
        'used' => $_SESSION['visualizer_count'],
        'limit' => $max_limit
    ]);
    exit;
}

// Handle visualization request
if (!isset($input['car_image']) || !isset($input['wrap'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing car_image or wrap selection']);
    exit;
}

$car_image = $input['car_image']; // Base64 encoded image
$wrap = $input['wrap']; // Wrap ID or custom image
$wrap_image = $input['wrap_image'] ?? null; // Optional custom wrap image (base64)

// Validate car image (must be base64)
if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $car_image)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image format. Please upload a JPEG, PNG, or WebP image.']);
    exit;
}

// Check Replicate API key
$replicate_key = defined('REPLICATE_API_KEY') ? REPLICATE_API_KEY : '';
if (empty($replicate_key) || $replicate_key === 'YOUR_API_KEY_HERE') {
    http_response_code(500);
    echo json_encode(['error' => 'Visualizer is not configured. Please contact the administrator.']);
    exit;
}

// Load wrap data
$wraps_file = dirname(__DIR__) . '/assets/wraps/wraps.json';
$wraps_data = json_decode(file_get_contents($wraps_file), true);

// Find the selected wrap
$selected_wrap = null;
foreach ($wraps_data['categories'] as $category) {
    foreach ($category['wraps'] as $w) {
        if ($w['id'] === $wrap) {
            $selected_wrap = $w;
            break 2;
        }
    }
}

if (!$selected_wrap && !$wrap_image) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid wrap selection']);
    exit;
}

// Build the prompt for FLUX Kontext
$wrap_name = $selected_wrap ? $selected_wrap['name'] : 'Custom';
$wrap_finish = $selected_wrap ? $selected_wrap['finish'] : 'Custom';
$wrap_hex = $selected_wrap ? $selected_wrap['hex'] : '';

// FLUX Kontext works best with direct instructions
if ($selected_wrap) {
    if ($selected_wrap['image']) {
        // Texture/pattern wrap
        $prompt = "Change the car's body color to {$wrap_name} with a {$wrap_finish} finish. Keep everything else exactly the same.";
    } else {
        // Solid color wrap
        $prompt = "Change the car's body paint to {$wrap_name} color ({$wrap_hex}) with a {$wrap_finish} finish. Keep everything else exactly the same - same car, same angle, same background, same wheels.";
    }
} else {
    // Custom wrap
    $prompt = "Change the car's body color to the custom wrap pattern. Keep everything else exactly the same.";
}

// Log for debugging
$log_dir = dirname(__DIR__) . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | Starting visualization | Wrap: {$wrap_name} | Prompt: {$prompt}\n",
    FILE_APPEND);

// Prepare the image - Replicate accepts data URIs
$image_uri = $car_image;

// Step 1: Create prediction on Replicate
$ch = curl_init();

$payload = [
    'version' => 'flux-kontext-pro',  // Use the model identifier
    'input' => [
        'prompt' => $prompt,
        'input_image' => $image_uri,
        'aspect_ratio' => 'match_input_image',
        'output_format' => 'png',
        'safety_tolerance' => 2
    ]
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.replicate.com/v1/models/black-forest-labs/flux-kontext-pro/predictions',
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $replicate_key,
        'Content-Type: application/json',
        'Prefer: wait'  // Wait for result synchronously (up to 60s)
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | Replicate create HTTP {$http_code} | Error: {$curl_error} | Response: " . substr($response, 0, 1000) . "\n",
    FILE_APPEND);

if ($http_code !== 200 && $http_code !== 201) {
    $error_data = json_decode($response, true);
    $error_msg = $error_data['detail'] ?? ($error_data['error'] ?? 'Unknown error');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to start image generation: ' . $error_msg]);
    exit;
}

$result = json_decode($response, true);

// Check if we got the result immediately (with Prefer: wait header)
$status = $result['status'] ?? '';
$output = $result['output'] ?? null;
$prediction_id = $result['id'] ?? null;

// If not completed, poll for result
if ($status !== 'succeeded' && $status !== 'failed') {
    $get_url = $result['urls']['get'] ?? "https://api.replicate.com/v1/predictions/{$prediction_id}";
    $max_attempts = 60; // Max 60 seconds
    $attempt = 0;

    while ($attempt < $max_attempts) {
        sleep(1);
        $attempt++;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $get_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $replicate_key,
                'Content-Type: application/json'
            ]
        ]);

        $poll_response = curl_exec($ch);
        $poll_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($poll_code === 200) {
            $poll_result = json_decode($poll_response, true);
            $status = $poll_result['status'] ?? '';

            if ($status === 'succeeded') {
                $output = $poll_result['output'] ?? null;
                break;
            } elseif ($status === 'failed') {
                $error_msg = $poll_result['error'] ?? 'Generation failed';
                file_put_contents($log_dir . '/visualizer_debug.log',
                    date('Y-m-d H:i:s') . " | Replicate failed: {$error_msg}\n",
                    FILE_APPEND);
                http_response_code(500);
                echo json_encode(['error' => 'Image generation failed: ' . $error_msg]);
                exit;
            }
            // Still processing, continue polling
        }
    }

    if ($status !== 'succeeded') {
        http_response_code(500);
        echo json_encode(['error' => 'Image generation timed out. Please try again.']);
        exit;
    }
}

// Handle failed status from immediate response
if ($status === 'failed') {
    $error_msg = $result['error'] ?? 'Generation failed';
    http_response_code(500);
    echo json_encode(['error' => 'Image generation failed: ' . $error_msg]);
    exit;
}

file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | Replicate succeeded | Output: " . json_encode($output) . "\n",
    FILE_APPEND);

// Get the output image URL
$output_url = is_array($output) ? ($output[0] ?? null) : $output;

if (!$output_url) {
    http_response_code(500);
    echo json_encode(['error' => 'No image generated']);
    exit;
}

// Download the generated image
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $output_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_FOLLOWLOCATION => true
]);
$image_data = curl_exec($ch);
$download_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($download_code !== 200 || !$image_data) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to download generated image']);
    exit;
}

// Convert to base64
$generated_image = base64_encode($image_data);

// Add watermark
$generated_image = addWatermark($generated_image);

// Increment usage counter
$_SESSION['visualizer_count']++;

// Calculate remaining
$remaining = $_SESSION['visualizer_email']
    ? $max_limit - $_SESSION['visualizer_count']
    : $free_limit - $_SESSION['visualizer_count'];

// Return the result
echo json_encode([
    'success' => true,
    'image' => 'data:image/png;base64,' . $generated_image,
    'wrap' => $selected_wrap ? $selected_wrap['name'] : 'Custom',
    'used' => $_SESSION['visualizer_count'],
    'remaining' => max(0, $remaining),
    'needs_email' => $remaining <= 0 && !$_SESSION['visualizer_email']
]);
