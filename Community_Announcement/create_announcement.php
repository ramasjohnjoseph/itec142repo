<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only admins can create announcements
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Unauthorized: only admins can create announcements.";
    header('Location: dashboard.php');
    exit;
}

// Handle announcement creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['announcement_title']);
    $content = trim($_POST['announcement_content']);
    $imgFilename = null;

    if (!empty($_FILES['announcement_image']) && $_FILES['announcement_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['announcement_image'];
        if ($file['size'] <= 5 * 1024 * 1024) { // 5MB
            $info = @getimagesize($file['tmp_name']);
            if ($info !== false) {
                $ext = image_type_to_extension($info[2], false);
                $uploadsDir = __DIR__ . '/uploads/announcements';
                if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
                $imgFilename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
                $dest = $uploadsDir . '/' . $imgFilename;

                // Resize image to fit within 1200x800 while keeping aspect ratio
                $origW = $info[0];
                $origH = $info[1];
                $type = $info[2];
                $maxW = 1200; $maxH = 800;
                $ratio = min($maxW / $origW, $maxH / $origH, 1);
                $newW = max(1, (int)($origW * $ratio));
                $newH = max(1, (int)($origH * $ratio));

                $srcImg = null;
                switch ($type) {
                    case IMAGETYPE_JPEG: $srcImg = imagecreatefromjpeg($file['tmp_name']); break;
                    case IMAGETYPE_PNG: $srcImg = imagecreatefrompng($file['tmp_name']); break;
                    case IMAGETYPE_GIF: $srcImg = imagecreatefromgif($file['tmp_name']); break;
                    case IMAGETYPE_WEBP: $srcImg = imagecreatefromwebp($file['tmp_name']); break;
                }

                if ($srcImg) {
                    $dstImg = imagecreatetruecolor($newW, $newH);
                    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
                        imagealphablending($dstImg, false);
                        imagesavealpha($dstImg, true);
                        $transparent = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
                        imagefilledrectangle($dstImg, 0, 0, $newW, $newH, $transparent);
                    }
                    imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
                    switch ($type) {
                        case IMAGETYPE_JPEG: imagejpeg($dstImg, $dest, 85); break;
                        case IMAGETYPE_PNG: imagepng($dstImg, $dest, 6); break;
                        case IMAGETYPE_GIF: imagegif($dstImg, $dest); break;
                        case IMAGETYPE_WEBP: imagewebp($dstImg, $dest, 85); break;
                    }
                    imagedestroy($srcImg);
                    imagedestroy($dstImg);
                } else {
                    // Fallback
                    move_uploaded_file($file['tmp_name'], $dest);
                }
            }
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO announcements (title, content, posted_by, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $_SESSION['user_id'], $imgFilename]);
        $_SESSION['flash_message'] = "Announcement created successfully.";
    } catch (Exception $e) {
        $_SESSION['flash_message'] = "Error creating announcement: " . $e->getMessage();
    }
    header('Location: dashboard.php');
    exit;
}

// If not a POST request, redirect back
header('Location: dashboard.php');
exit;
?>
<!-- No HTML needed here since it redirects -->