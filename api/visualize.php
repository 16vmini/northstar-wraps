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
require_once __DIR__ . '/../includes/wrapinator-usage.php';

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

    // Add brand watermark in top-right corner (more visible)
    $brand_text = 'NORTH STAR WRAP';
    $brand_font_size = 4; // Larger font
    $brand_width = imagefontwidth($brand_font_size) * strlen($brand_text);
    $brand_height = imagefontheight($brand_font_size);
    $brand_padding = 8;
    $brand_x = $width - $brand_width - $padding - $brand_padding;
    $brand_y = $padding;

    // Solid dark background for visibility
    $brand_bg = imagecolorallocate($image, 30, 30, 30);
    imagefilledrectangle($image, $brand_x - $brand_padding, $brand_y - $brand_padding/2,
                         $width - $padding + $brand_padding, $brand_y + $brand_height + $brand_padding/2, $brand_bg);

    // Green accent bar on left of brand
    $accent_color = imagecolorallocate($image, 124, 181, 24);
    imagefilledrectangle($image, $brand_x - $brand_padding, $brand_y - $brand_padding/2,
                         $brand_x - $brand_padding + 4, $brand_y + $brand_height + $brand_padding/2, $accent_color);

    $brand_color = imagecolorallocate($image, 255, 255, 255);
    imagestring($image, $brand_font_size, $brand_x, $brand_y, $brand_text, $brand_color);

    // Convert back to base64
    ob_start();
    imagepng($image);
    $output = ob_get_clean();
    imagedestroy($image);

    return base64_encode($output);
}

/**
 * Fix image orientation based on EXIF data
 * Phone cameras often store images rotated with EXIF metadata
 */
function fixImageOrientation($base64_data_uri) {
    // Extract the base64 data and mime type
    if (!preg_match('/^data:image\/(\w+);base64,(.+)$/', $base64_data_uri, $matches)) {
        return $base64_data_uri;
    }

    $mime_type = $matches[1];
    $base64_data = $matches[2];
    $image_data = base64_decode($base64_data);

    // Only JPEGs have EXIF data
    if (!in_array($mime_type, ['jpeg', 'jpg'])) {
        return $base64_data_uri;
    }

    // Create image from string
    $image = imagecreatefromstring($image_data);
    if (!$image) {
        return $base64_data_uri;
    }

    // Try to read EXIF data
    // We need to write to a temp file because exif_read_data needs a file path
    $temp_file = tempnam(sys_get_temp_dir(), 'exif_');
    file_put_contents($temp_file, $image_data);

    $exif = @exif_read_data($temp_file);
    @unlink($temp_file);

    if (!$exif || !isset($exif['Orientation'])) {
        imagedestroy($image);
        return $base64_data_uri;
    }

    $orientation = $exif['Orientation'];

    // Apply rotation based on EXIF orientation
    switch ($orientation) {
        case 3: // 180 degrees
            $image = imagerotate($image, 180, 0);
            break;
        case 6: // 90 degrees CW (this is likely the issue)
            $image = imagerotate($image, -90, 0);
            break;
        case 8: // 90 degrees CCW
            $image = imagerotate($image, 90, 0);
            break;
        default:
            // No rotation needed (orientation 1) or unsupported
            imagedestroy($image);
            return $base64_data_uri;
    }

    // Convert back to base64
    ob_start();
    imagejpeg($image, null, 90);
    $output = ob_get_clean();
    imagedestroy($image);

    return 'data:image/jpeg;base64,' . base64_encode($output);
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

// Initialize session tracking (shared function handles this)
initWrapinatorUsage();

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

    // Use shared function to save email, log lead, and send notification
    $result = saveWrapinatorEmail($email);
    echo json_encode($result);
    exit;
}

// Handle status check (before rate limit check)
if (isset($input['action']) && $input['action'] === 'status') {
    echo json_encode(getWrapinatorStatus());
    exit;
}

// Check usage limits using shared function
$usage_check = checkWrapinatorUsage();
if (!$usage_check['allowed']) {
    http_response_code($usage_check['error'] === 'email_required' ? 403 : 429);
    echo json_encode($usage_check);
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

// Validate car image (must be base64) - accept common formats including HEIC from iOS
if (!preg_match('/^data:image\/(jpeg|jpg|png|webp|heic|heif);base64,/', $car_image) &&
    !preg_match('/^data:application\/octet-stream;base64,/', $car_image)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image format. Please upload a JPEG, PNG, or WebP image.']);
    exit;
}

// Fix EXIF orientation before sending to API
// This prevents rotated images from phone cameras
$car_image = fixImageOrientation($car_image);

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
    // Custom wrap - Note: FLUX Kontext doesn't use the uploaded pattern directly,
    // it interprets the prompt. Custom patterns have limited accuracy.
    $prompt = "Apply a colorful custom vinyl wrap pattern to the car's body panels. Make it look like a professional vehicle wrap with a bold, eye-catching design. Keep the car's shape, wheels, windows, and background exactly the same.";
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

// Save the image to uploads folder
$share_id = bin2hex(random_bytes(8)); // 16 char unique ID
$upload_dir = dirname(__DIR__) . '/uploads/visualizer';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Save the image with pending_ prefix (needs approval for gallery)
$image_path = $upload_dir . '/pending_' . $share_id . '.png';
file_put_contents($image_path, base64_decode($generated_image));

// Save metadata with pending_ prefix
$metadata = [
    'id' => $share_id,
    'wrap' => $selected_wrap ? $selected_wrap['name'] : 'Custom',
    'wrap_id' => $wrap,
    'finish' => $selected_wrap ? $selected_wrap['finish'] : 'Custom',
    'model' => 'T-800',
    'created' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'],
    'email' => $_SESSION['visualizer_email'] ?? null,
    'status' => 'pending'
];
file_put_contents($upload_dir . '/pending_' . $share_id . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

// Log to gallery index
$gallery_log = $upload_dir . '/gallery.log';
$log_entry = date('Y-m-d H:i:s') . " | {$share_id} | " . ($selected_wrap ? $selected_wrap['name'] : 'Custom') . "\n";
file_put_contents($gallery_log, $log_entry, FILE_APPEND | LOCK_EX);

// Increment usage counter using shared function
$usage_status = incrementWrapinatorUsage();

// Return the result with share ID
echo json_encode([
    'success' => true,
    'image' => 'data:image/png;base64,' . $generated_image,
    'wrap' => $selected_wrap ? $selected_wrap['name'] : 'Custom',
    'share_id' => $share_id,
    'used' => $usage_status['used'],
    'remaining' => $usage_status['remaining'],
    'needs_email' => $usage_status['needs_email']
]);
