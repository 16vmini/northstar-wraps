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

// Get input
$input = json_decode(file_get_contents('php://input'), true);

// Handle email submission
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

// Handle status check
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

// Build the prompt
if ($selected_wrap) {
    $wrap_name = $selected_wrap['name'];
    $wrap_finish = $selected_wrap['finish'];
    $wrap_hex = $selected_wrap['hex'];

    if ($selected_wrap['image']) {
        // Texture/pattern wrap
        $prompt = "Change the car body color in this image to a {$wrap_name} ({$wrap_finish} finish) vinyl wrap. Keep the windows, headlights, taillights, wheels, and tires unchanged. The wrap should cover all painted body panels realistically. Maintain the same lighting, angle, and background.";
    } else {
        // Solid color wrap
        $prompt = "Change the car body color in this image to {$wrap_name} (hex color {$wrap_hex}, {$wrap_finish} finish). Keep the windows, headlights, taillights, wheels, and tires unchanged. The wrap should cover all painted body panels realistically. Maintain the same lighting, angle, and background.";
    }
} else {
    // Custom wrap image provided
    $prompt = "Apply the pattern/texture from the second image as a vinyl wrap to the car body in the first image. Keep the windows, headlights, taillights, wheels, and tires unchanged. The wrap should cover all painted body panels realistically. Maintain the same lighting, angle, and background.";
}

// Prepare the API request
$messages = [
    [
        'role' => 'user',
        'content' => [
            [
                'type' => 'text',
                'text' => $prompt
            ],
            [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $car_image
                ]
            ]
        ]
    ]
];

// Add wrap image if custom texture provided
if ($wrap_image) {
    $messages[0]['content'][] = [
        'type' => 'image_url',
        'image_url' => [
            'url' => $wrap_image
        ]
    ];
}

// Call OpenAI API (using gpt-4o for image understanding + DALL-E for generation)
// Note: For actual image editing, we'll use the images/edit endpoint or gpt-image-1
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.openai.com/v1/images/edits',
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_key,
    ]
]);

// For DALL-E image edit, we need to send as multipart form
// First, decode and save the car image temporarily
$temp_dir = sys_get_temp_dir();
$car_image_data = preg_replace('/^data:image\/\w+;base64,/', '', $car_image);
$car_image_binary = base64_decode($car_image_data);
$temp_image = $temp_dir . '/car_' . uniqid() . '.png';
file_put_contents($temp_image, $car_image_binary);

// Use the chat completions endpoint with vision for better results
$chat_data = [
    'model' => 'gpt-4o',
    'messages' => $messages,
    'max_tokens' => 1000
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
    CURLOPT_POSTFIELDS => json_encode($chat_data),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]
]);

// Actually, let's use DALL-E 3 for image generation based on the description
// We'll have GPT-4o analyze the car first, then generate with DALL-E
$analysis_prompt = "Describe this car in detail for image generation: make, model, body style, current color, angle, background, lighting. Be specific and concise.";

$analysis_data = [
    'model' => 'gpt-4o',
    'messages' => [
        [
            'role' => 'user',
            'content' => [
                ['type' => 'text', 'text' => $analysis_prompt],
                ['type' => 'image_url', 'image_url' => ['url' => $car_image]]
            ]
        ]
    ],
    'max_tokens' => 500
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
    CURLOPT_POSTFIELDS => json_encode($analysis_data),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]
]);

$analysis_response = curl_exec($ch);
$analysis_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Log for debugging
$log_dir = dirname(__DIR__) . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | Analysis HTTP {$analysis_http_code} | Response: " . substr($analysis_response, 0, 500) . "\n",
    FILE_APPEND);

if ($analysis_http_code !== 200) {
    $error_data = json_decode($analysis_response, true);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to analyze image: ' . ($error_data['error']['message'] ?? 'Unknown error')]);
    @unlink($temp_image);
    curl_close($ch);
    exit;
}

$analysis_result = json_decode($analysis_response, true);
$car_description = $analysis_result['choices'][0]['message']['content'] ?? '';

// Now generate with DALL-E 3
if ($selected_wrap) {
    $generation_prompt = "Photorealistic image of {$car_description} BUT with the body color changed to {$wrap_name} ({$wrap_finish} finish vinyl wrap, hex {$wrap_hex}). Keep exact same angle, background, lighting. Windows, lights, wheels unchanged. Professional automotive photography style.";
} else {
    $generation_prompt = "Photorealistic image of {$car_description} BUT with a custom vinyl wrap pattern applied to the body. Keep exact same angle, background, lighting. Windows, lights, wheels unchanged. Professional automotive photography style.";
}

$dalle_data = [
    'model' => 'dall-e-3',
    'prompt' => $generation_prompt,
    'n' => 1,
    'size' => '1024x1024',
    'quality' => 'hd',
    'response_format' => 'b64_json'
];

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.openai.com/v1/images/generations',
    CURLOPT_POSTFIELDS => json_encode($dalle_data),
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $api_key,
        'Content-Type: application/json'
    ]
]);

$dalle_response = curl_exec($ch);
$dalle_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// Clean up temp file
@unlink($temp_image);

// Log
file_put_contents($log_dir . '/visualizer_debug.log',
    date('Y-m-d H:i:s') . " | DALL-E HTTP {$dalle_http_code} | Error: {$curl_error}\n",
    FILE_APPEND);

if ($dalle_http_code !== 200) {
    $error_data = json_decode($dalle_response, true);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate image: ' . ($error_data['error']['message'] ?? 'Unknown error')]);
    exit;
}

$dalle_result = json_decode($dalle_response, true);
$generated_image = $dalle_result['data'][0]['b64_json'] ?? null;

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
