<?php
/**
 * Serve shared visualizer images
 * This allows images to be displayed without exposing the uploads folder directly
 */

// Get share ID
$share_id = preg_replace('/[^a-f0-9]/', '', $_GET['id'] ?? '');

if (empty($share_id)) {
    http_response_code(404);
    exit;
}

// Find the image (check both approved and pending)
$upload_dir = __DIR__ . '/../uploads/visualizer';
$image_file = $upload_dir . '/' . $share_id . '.png'; // Approved
if (!file_exists($image_file)) {
    $image_file = $upload_dir . '/pending_' . $share_id . '.png'; // Pending
}

if (!file_exists($image_file)) {
    http_response_code(404);
    exit;
}

// Serve the image with caching headers
$etag = md5_file($image_file);
$last_modified = filemtime($image_file);

// Check if browser has cached version
if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
    http_response_code(304);
    exit;
}

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $last_modified) {
    http_response_code(304);
    exit;
}

// Set headers
header('Content-Type: image/png');
header('Content-Length: ' . filesize($image_file));
header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
header('ETag: ' . $etag);
header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');

// Output the image
readfile($image_file);
