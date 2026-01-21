<?php
/**
 * Gallery Submission API Endpoint
 * Handles user-initiated gallery submissions from the Wrapinator
 */

session_start();
header('Content-Type: application/json');

// Load configs
require_once __DIR__ . '/../includes/config.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['share_id']) || !isset($input['image'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing share_id or image']);
    exit;
}

$share_id = preg_replace('/[^a-f0-9]/', '', $input['share_id']); // Sanitize
$image_data = $input['image'];
$wrap_name = $input['wrap'] ?? 'Custom';
$model = $input['model'] ?? null;

// Validate share_id format (should be 16 hex chars)
if (strlen($share_id) !== 16) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid share_id format']);
    exit;
}

// Validate image is base64 PNG
if (!preg_match('/^data:image\/png;base64,/', $image_data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid image format']);
    exit;
}

// Extract base64 data
$base64_data = preg_replace('/^data:image\/png;base64,/', '', $image_data);
$decoded_image = base64_decode($base64_data);

if (!$decoded_image) {
    http_response_code(400);
    echo json_encode(['error' => 'Failed to decode image']);
    exit;
}

// Save to uploads directory
$upload_dir = dirname(__DIR__) . '/uploads/visualizer';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Save the image with pending_ prefix (needs admin approval for gallery)
$image_path = $upload_dir . '/pending_' . $share_id . '.png';

// Check if this share_id already exists (prevent duplicates)
if (file_exists($image_path) || file_exists($upload_dir . '/' . $share_id . '.png')) {
    http_response_code(400);
    echo json_encode(['error' => 'This image has already been submitted to the gallery']);
    exit;
}

$saved = file_put_contents($image_path, $decoded_image);

if ($saved === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save image']);
    exit;
}

// Save metadata
$metadata = [
    'id' => $share_id,
    'wrap' => $wrap_name,
    'finish' => '',
    'model' => $model,
    'created' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'email' => $_SESSION['visualizer_email'] ?? null,
    'status' => 'pending'
];
file_put_contents($upload_dir . '/pending_' . $share_id . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

// Log the submission
$log_dir = dirname(__DIR__) . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$log_entry = date('Y-m-d H:i:s') . " | {$share_id} | {$wrap_name} | Gallery submission\n";
file_put_contents($log_dir . '/visualizer_generations.log', $log_entry, FILE_APPEND);

echo json_encode([
    'success' => true,
    'message' => 'Image submitted to gallery for approval',
    'share_id' => $share_id
]);
