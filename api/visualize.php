<?php
/**
 * Wrap Visualizer API Endpoint
 * Handles image generation via OpenAI
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

// Check API key
$api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : '';
if (empty($api_key) || $api_key === 'YOUR_API_KEY_HERE') {
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

// Build the prompt for GPT-4o image editing
$wrap_name = $selected_wrap ? $selected_wrap['name'] : 'Custom';
$wrap_finish = $selected_wrap ? $selected_wrap['finish'] : 'Custom';
$wrap_hex = $selected_wrap ? $selected_wrap['hex'] : '';

if ($selected_wrap) {
    if ($selected_wrap['image']) {
        // Texture/pattern wrap
        $prompt = "Edit this car image: Change ONLY the car's body paint/panels to a {$wrap_name} vinyl wrap with a {$wrap_finish} finish. Keep the EXACT same car, angle, background, lighting, windows, headlights, taillights, wheels, tires, and all other details identical. Only change the body color/texture.";
    } else {
        // Solid color wrap
        $prompt = "Edit this car image: Change ONLY the car's body paint/panels to {$wrap_name} color (hex {$wrap_hex}) with a {$wrap_finish} finish. Keep the EXACT same car, angle, background, lighting, windows, headlights, taillights, wheels, tires, and all other details identical. Only change the body color.";
    }
} else {
    // Custom wrap
    $prompt = "Edit this car image: Apply the pattern/texture from the reference image as a vinyl wrap to ONLY the car's body panels. Keep the EXACT same car, angle, background, lighting, windows, headlights, taillights, wheels, tires, and all other details identical.";
}

// Log for debugging
$log_dir = dirname(__DIR__) . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | Starting visualization | Wrap: {$wrap_name}\n",
    FILE_APPEND);

// Decode the base64 image and save as PNG for the edit API
$car_image_data = preg_replace('/^data:image\/\w+;base64,/', '', $car_image);
$car_image_binary = base64_decode($car_image_data);

// Convert to PNG (required by DALL-E edit API)
$source_image = imagecreatefromstring($car_image_binary);
if (!$source_image) {
    http_response_code(400);
    echo json_encode(['error' => 'Could not process the uploaded image']);
    exit;
}

// Resize to 1024x1024 (required by DALL-E)
// DALL-E edit API requires RGBA format with alpha channel
$resized = imagecreatetruecolor(1024, 1024);
$orig_width = imagesx($source_image);
$orig_height = imagesy($source_image);

// Enable alpha blending and save alpha channel
imagesavealpha($resized, true);
imagealphablending($resized, false);

// Fill with transparent background
$transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
imagefill($resized, 0, 0, $transparent);

// Calculate aspect-ratio-preserving resize
$scale = min(1024 / $orig_width, 1024 / $orig_height);
$new_width = (int)($orig_width * $scale);
$new_height = (int)($orig_height * $scale);
$x_offset = (int)((1024 - $new_width) / 2);
$y_offset = (int)((1024 - $new_height) / 2);

// Enable alpha blending for the copy operation
imagealphablending($resized, true);

// Copy resized image centered
imagecopyresampled($resized, $source_image, $x_offset, $y_offset, 0, 0, $new_width, $new_height, $orig_width, $orig_height);
imagedestroy($source_image);

// Save to temp file as PNG with alpha
$temp_dir = sys_get_temp_dir();
$temp_image_path = $temp_dir . '/car_' . uniqid() . '.png';
imagesavealpha($resized, true);
imagepng($resized, $temp_image_path);
imagedestroy($resized);

file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | Saved temp image: {$temp_image_path}\n",
    FILE_APPEND);

// Build the edit prompt
$edit_prompt = "Change the car body color to {$wrap_name} ({$wrap_finish} finish" . ($wrap_hex ? ", {$wrap_hex}" : "") . ") vinyl wrap. Keep everything else exactly the same - windows, wheels, background, lighting.";

// Try DALL-E 2 edit API with multipart form data
$ch = curl_init();

$post_fields = [
    'model' => 'dall-e-2',
    'image' => new CURLFile($temp_image_path, 'image/png', 'image.png'),
    'prompt' => $edit_prompt,
    'n' => 1,
    'size' => '1024x1024',
    'response_format' => 'b64_json'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.openai.com/v1/images/edits',
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_key
    ],
    CURLOPT_POSTFIELDS => $post_fields
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Clean up temp file
@unlink($temp_image_path);

file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | DALL-E Edit HTTP {$http_code} | Error: {$curl_error} | Response: " . substr($response, 0, 500) . "\n",
    FILE_APPEND);

if ($http_code !== 200) {
    $error_data = json_decode($response, true);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to edit image: ' . ($error_data['error']['message'] ?? 'Unknown error')]);
    exit;
}

$result = json_decode($response, true);
$generated_image = $result['data'][0]['b64_json'] ?? null;

if (!$generated_image) {
    http_response_code(500);
    echo json_encode(['error' => 'No image generated']);
    exit;
}

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
