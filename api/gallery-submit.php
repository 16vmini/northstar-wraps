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

// Check uploads directory
$upload_dir = dirname(__DIR__) . '/uploads/visualizer';
$metadata_file = $upload_dir . '/pending_' . $share_id . '.json';

// Check if metadata file exists (image was generated)
if (!file_exists($metadata_file)) {
    http_response_code(400);
    echo json_encode(['error' => 'Image not found. Please generate an image first.']);
    exit;
}

// Load existing metadata
$metadata = json_decode(file_get_contents($metadata_file), true);

// Check if already submitted to gallery
if (!empty($metadata['submitted_to_gallery'])) {
    http_response_code(400);
    echo json_encode(['error' => 'This image has already been submitted to the gallery']);
    exit;
}

// Update metadata to mark as submitted to gallery
$metadata['submitted_to_gallery'] = true;
$metadata['submitted_at'] = date('Y-m-d H:i:s');
if ($model) {
    $metadata['model'] = $model;
}
file_put_contents($metadata_file, json_encode($metadata, JSON_PRETTY_PRINT));

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
