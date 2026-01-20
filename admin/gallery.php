<?php
/**
 * Admin Gallery Moderation Panel
 * Approve or reject pending Wrapinator images
 */
session_start();
require_once '../includes/config.php';

// Simple password protection (change this password!)
$admin_password = 'northstar2024';

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = 'Invalid password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: /admin/gallery');
    exit;
}

// Check if logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Login - North Star Wrap</title>
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #1a1a1a; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
            .login-box { background: #fff; padding: 40px; border-radius: 16px; width: 100%; max-width: 400px; margin: 20px; }
            h1 { font-size: 1.5rem; margin-bottom: 20px; text-align: center; }
            input[type="password"] { width: 100%; padding: 15px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; margin-bottom: 15px; }
            button { width: 100%; padding: 15px; background: #7cb518; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; }
            button:hover { background: #6a9c15; }
            .error { color: #dc2626; text-align: center; margin-bottom: 15px; }
        </style>
    </head>
    <body>
        <div class="login-box">
            <h1>Gallery Admin</h1>
            <?php if (isset($login_error)): ?>
                <p class="error"><?php echo $login_error; ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter admin password" required autofocus>
                <button type="submit">Login</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Handle approve/reject actions
$upload_dir = __DIR__ . '/../uploads/visualizer';
$message = '';

if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = preg_replace('/[^a-f0-9]/', '', $_POST['id']);
    $action = $_POST['action'];

    $pending_image = $upload_dir . '/pending_' . $id . '.png';
    $pending_meta = $upload_dir . '/pending_' . $id . '.json';
    $approved_image = $upload_dir . '/' . $id . '.png';
    $approved_meta = $upload_dir . '/' . $id . '.json';

    if ($action === 'approve' && file_exists($pending_image)) {
        // Rename files (remove pending_ prefix)
        rename($pending_image, $approved_image);
        if (file_exists($pending_meta)) {
            $meta = json_decode(file_get_contents($pending_meta), true);
            $meta['status'] = 'approved';
            $meta['approved_at'] = date('Y-m-d H:i:s');
            file_put_contents($approved_meta, json_encode($meta, JSON_PRETTY_PRINT));
            unlink($pending_meta);
        }
        $message = 'Image approved successfully';
    } elseif ($action === 'reject' && file_exists($pending_image)) {
        // Delete both files
        unlink($pending_image);
        if (file_exists($pending_meta)) {
            unlink($pending_meta);
        }
        $message = 'Image rejected and deleted';
    }
}

// Get pending images
$pending_images = [];
if (is_dir($upload_dir)) {
    $files = glob($upload_dir . '/pending_*.png');
    foreach ($files as $file) {
        $filename = basename($file);
        $id = str_replace(['pending_', '.png'], '', $filename);
        $meta_file = $upload_dir . '/pending_' . $id . '.json';
        $meta = file_exists($meta_file) ? json_decode(file_get_contents($meta_file), true) : [];
        $pending_images[] = [
            'id' => $id,
            'file' => $file,
            'meta' => $meta,
            'created' => $meta['created'] ?? date('Y-m-d H:i:s', filemtime($file))
        ];
    }
    // Sort by newest first
    usort($pending_images, function($a, $b) {
        return strtotime($b['created']) - strtotime($a['created']);
    });
}

// Get approved images count
$approved_count = count(glob($upload_dir . '/[!p]*.png') ?: []);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Moderation - North Star Wrap Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; min-height: 100vh; }

        .admin-header {
            background: #1a1a1a;
            color: #fff;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 { font-size: 1.3rem; }
        .admin-header a { color: #9ca3af; text-decoration: none; }
        .admin-header a:hover { color: #fff; }

        .container { max-width: 1200px; margin: 0 auto; padding: 30px 20px; }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
        }

        .stat-card h3 { color: #666; font-size: 0.9rem; margin-bottom: 5px; }
        .stat-card .number { font-size: 2rem; font-weight: 700; color: #1a1a1a; }
        .stat-card.pending .number { color: #f59e0b; }
        .stat-card.approved .number { color: #7cb518; }

        .message {
            background: #d1fae5;
            color: #065f46;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i { color: #f59e0b; }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .image-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .image-card-body { padding: 15px; }

        .image-meta {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 15px;
        }

        .image-meta strong { color: #1a1a1a; }

        .image-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-approve { background: #7cb518; color: #fff; }
        .btn-approve:hover { background: #6a9c15; }
        .btn-reject { background: #dc2626; color: #fff; }
        .btn-reject:hover { background: #b91c1c; }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 15px;
        }

        .gallery-link {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background: #1a1a1a;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
        }

        .gallery-link:hover { background: #333; }

        @media (max-width: 640px) {
            .stats { flex-direction: column; }
            .image-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <h1><i class="fas fa-images"></i> Gallery Moderation</h1>
        <div>
            <a href="/admin/gallery" style="margin-right: 15px;"><i class="fas fa-sync-alt"></i> Refresh</a>
            <a href="?logout=1"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="message">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card pending">
                <h3>Pending Review</h3>
                <div class="number"><?php echo count($pending_images); ?></div>
            </div>
            <div class="stat-card approved">
                <h3>Approved in Gallery</h3>
                <div class="number"><?php echo $approved_count; ?></div>
            </div>
        </div>

        <h2 class="section-title"><i class="fas fa-clock"></i> Pending Images</h2>

        <?php if (empty($pending_images)): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No pending images</h3>
                <p>All images have been reviewed. Check back later for new submissions.</p>
            </div>
        <?php else: ?>
            <div class="image-grid">
                <?php foreach ($pending_images as $image): ?>
                    <div class="image-card">
                        <img src="/api/share-image.php?id=<?php echo $image['id']; ?>" alt="Pending image">
                        <div class="image-card-body">
                            <div class="image-meta">
                                <p><strong>Wrap:</strong> <?php echo htmlspecialchars($image['meta']['wrap'] ?? 'Unknown'); ?></p>
                                <p><strong>Created:</strong> <?php echo htmlspecialchars($image['created']); ?></p>
                                <?php if (!empty($image['meta']['email'])): ?>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($image['meta']['email']); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="image-actions">
                                <form method="POST" style="flex: 1;">
                                    <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-approve" style="width: 100%;">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                                <form method="POST" style="flex: 1;" onsubmit="return confirm('Delete this image permanently?');">
                                    <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-reject" style="width: 100%;">
                                        <i class="fas fa-trash"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="/pages/wrapinator-gallery" class="gallery-link" target="_blank">
            <i class="fas fa-external-link-alt"></i> View Public Gallery
        </a>
    </div>
</body>
</html>
