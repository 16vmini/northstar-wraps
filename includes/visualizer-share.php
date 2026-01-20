<?php
/**
 * Shared image saving/sharing functionality for Wrapinator
 * Used by both visualize.php (V1) and visualize-v2.php (V2)
 */

/**
 * Save generated image for sharing and gallery
 *
 * @param string $image_data Raw image data (not base64)
 * @param string $wrap_name Name of the wrap/pattern
 * @param string $wrap_finish Finish type (e.g., Gloss, Matte) - optional
 * @return array ['share_id' => string, 'error' => string|null]
 */
function saveVisualizerImage($image_data, $wrap_name, $wrap_finish = '') {
    $share_id = bin2hex(random_bytes(8)); // 16 char unique ID
    $upload_dir = dirname(__DIR__) . '/uploads/visualizer';

    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Save the image with pending_ prefix (needs approval for gallery)
    $image_path = $upload_dir . '/pending_' . $share_id . '.png';
    $saved = file_put_contents($image_path, $image_data);

    if ($saved === false) {
        return ['share_id' => null, 'error' => 'Failed to save image'];
    }

    // Save metadata with pending_ prefix
    $metadata = [
        'id' => $share_id,
        'wrap' => $wrap_name,
        'finish' => $wrap_finish,
        'created' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ];
    file_put_contents($upload_dir . '/pending_' . $share_id . '.json', json_encode($metadata, JSON_PRETTY_PRINT));

    // Log the generation
    $log_dir = dirname(__DIR__) . '/logs';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0755, true);
    }
    $log_entry = date('Y-m-d H:i:s') . " | {$share_id} | {$wrap_name}\n";
    file_put_contents($log_dir . '/visualizer_generations.log', $log_entry, FILE_APPEND);

    return ['share_id' => $share_id, 'error' => null];
}
