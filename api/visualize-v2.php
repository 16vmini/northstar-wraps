<?php
/**
 * Wrap Visualizer V2 API Endpoint
 * Testing multi-image approach for custom wrap patterns
 * Uses image merge/style transfer models on Replicate
 */

session_start();
header('Content-Type: application/json');

// Load configs
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/api-config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['car_image']) || !isset($input['wrap_image'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing car_image or wrap_image']);
    exit;
}

$car_image = $input['car_image'];
$wrap_image = $input['wrap_image'];
$custom_prompt = $input['prompt'] ?? '';

// Validate images (must be base64)
if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $car_image)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid car image format']);
    exit;
}

if (!preg_match('/^data:image\/(jpeg|jpg|png|webp);base64,/', $wrap_image)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid wrap image format']);
    exit;
}

// Check Replicate API key
$replicate_key = defined('REPLICATE_API_KEY') ? REPLICATE_API_KEY : '';
if (empty($replicate_key) || $replicate_key === 'YOUR_API_KEY_HERE') {
    http_response_code(500);
    echo json_encode(['error' => 'Visualizer is not configured']);
    exit;
}

// Log for debugging
$log_dir = dirname(__DIR__) . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

/**
 * Approach: Use FLUX Kontext Pro with a composite image
 * Combine car image and wrap pattern side by side, then prompt the AI
 * to apply the pattern from the right to the car on the left
 */

// Create composite image with car on left, pattern on right
function createCompositeImage($car_base64, $wrap_base64) {
    // Remove data URI prefix
    $car_data = preg_replace('/^data:image\/\w+;base64,/', '', $car_base64);
    $wrap_data = preg_replace('/^data:image\/\w+;base64,/', '', $wrap_base64);

    $car_img = imagecreatefromstring(base64_decode($car_data));
    $wrap_img = imagecreatefromstring(base64_decode($wrap_data));

    if (!$car_img || !$wrap_img) {
        return null;
    }

    $car_width = imagesx($car_img);
    $car_height = imagesy($car_img);
    $wrap_width = imagesx($wrap_img);
    $wrap_height = imagesy($wrap_img);

    // Scale pattern to match car height while maintaining aspect ratio
    $pattern_display_height = $car_height;
    $pattern_display_width = (int)($wrap_width * ($pattern_display_height / $wrap_height));

    // Limit pattern width to not be larger than 40% of car width
    if ($pattern_display_width > $car_width * 0.4) {
        $pattern_display_width = (int)($car_width * 0.4);
        $pattern_display_height = (int)($wrap_height * ($pattern_display_width / $wrap_width));
    }

    // Create composite canvas
    $total_width = $car_width + $pattern_display_width + 20; // 20px gap
    $total_height = max($car_height, $pattern_display_height);

    $composite = imagecreatetruecolor($total_width, $total_height);

    // White background
    $white = imagecolorallocate($composite, 255, 255, 255);
    imagefill($composite, 0, 0, $white);

    // Place car on left
    $car_y = ($total_height - $car_height) / 2;
    imagecopy($composite, $car_img, 0, (int)$car_y, 0, 0, $car_width, $car_height);

    // Place pattern on right (scaled)
    $pattern_x = $car_width + 20;
    $pattern_y = ($total_height - $pattern_display_height) / 2;
    imagecopyresampled(
        $composite, $wrap_img,
        $pattern_x, (int)$pattern_y,
        0, 0,
        $pattern_display_width, $pattern_display_height,
        $wrap_width, $wrap_height
    );

    // Add a thin border around the pattern
    $border_color = imagecolorallocate($composite, 200, 200, 200);
    imagerectangle($composite, $pattern_x - 1, (int)$pattern_y - 1,
                   $pattern_x + $pattern_display_width, (int)$pattern_y + $pattern_display_height, $border_color);

    // Convert to base64 PNG
    ob_start();
    imagepng($composite, null, 6);
    $output = ob_get_clean();

    imagedestroy($car_img);
    imagedestroy($wrap_img);
    imagedestroy($composite);

    return 'data:image/png;base64,' . base64_encode($output);
}

// Create the composite image
$composite_image = createCompositeImage($car_image, $wrap_image);

if (!$composite_image) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create composite image']);
    exit;
}

// Build prompt for FLUX Kontext
$default_prompt = "Look at the pattern shown on the right side of this image. Apply that exact pattern/design as a vinyl wrap to the car shown on the left side. The wrap should cover the car's body panels (hood, doors, fenders, roof). Keep the car's shape, wheels, windows, lights, and overall form exactly the same. Only change the body color/pattern to match the reference pattern on the right. Output only the wrapped car, not the reference pattern.";

$prompt = !empty($custom_prompt)
    ? $custom_prompt . " Apply the pattern shown on the right to the car on the left. Keep everything else about the car the same."
    : $default_prompt;

file_put_contents($log_dir . '/visualizer_v2_debug.log',
    date('Y-m-d H:i:s') . " | Starting V2 | Prompt: {$prompt}\n",
    FILE_APPEND);

// Try FLUX Kontext Pro with composite image
$ch = curl_init();

$payload = [
    'input' => [
        'prompt' => $prompt,
        'input_image' => $composite_image,
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
        'Prefer: wait'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

file_put_contents($log_dir . '/visualizer_v2_debug.log',
    date('Y-m-d H:i:s') . " | HTTP {$http_code} | Error: {$curl_error} | Response: " . substr($response, 0, 500) . "\n",
    FILE_APPEND);

if ($http_code !== 200 && $http_code !== 201) {
    $error_data = json_decode($response, true);
    $error_msg = $error_data['detail'] ?? ($error_data['error'] ?? 'Unknown error');
    http_response_code(500);
    echo json_encode(['error' => 'Failed to start generation: ' . $error_msg]);
    exit;
}

$result = json_decode($response, true);
$status = $result['status'] ?? '';
$output = $result['output'] ?? null;
$prediction_id = $result['id'] ?? null;

// Poll if not completed
if ($status !== 'succeeded' && $status !== 'failed') {
    $get_url = $result['urls']['get'] ?? "https://api.replicate.com/v1/predictions/{$prediction_id}";
    $max_attempts = 90; // Allow longer for complex generation
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
                http_response_code(500);
                echo json_encode(['error' => 'Generation failed: ' . $error_msg]);
                exit;
            }
        }
    }

    if ($status !== 'succeeded') {
        http_response_code(500);
        echo json_encode(['error' => 'Generation timed out']);
        exit;
    }
}

if ($status === 'failed') {
    $error_msg = $result['error'] ?? 'Generation failed';
    http_response_code(500);
    echo json_encode(['error' => 'Generation failed: ' . $error_msg]);
    exit;
}

// Get output URL
$output_url = is_array($output) ? ($output[0] ?? null) : $output;

if (!$output_url) {
    http_response_code(500);
    echo json_encode(['error' => 'No image generated']);
    exit;
}

// Download generated image
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

$generated_image = base64_encode($image_data);

file_put_contents($log_dir . '/visualizer_v2_debug.log',
    date('Y-m-d H:i:s') . " | SUCCESS | Image generated\n",
    FILE_APPEND);

// Return result
echo json_encode([
    'success' => true,
    'image' => 'data:image/png;base64,' . $generated_image,
    'debug' => [
        'model' => 'flux-kontext-pro',
        'prompt' => $prompt,
        'approach' => 'composite_image'
    ]
]);
