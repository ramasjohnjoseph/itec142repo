<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $type = $_POST['type']; 
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'];

    // Only admins can manage events and announcements
    if (in_array($type, ['event', 'announcement']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
        $_SESSION['flash_message'] = 'Unauthorized: only admins can manage events and announcements.';
        header('Location: dashboard.php?content=' . ($type === 'event' ? 'manageEvent' : 'manageAnnouncement'));
        exit;
    }

    if ($action === 'delete') {
        $id = $_POST['id'];
        if ($type === 'event') {
            // Admins can delete any event, others can only delete their own
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ?");
                $stmt->execute([$id]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM events WHERE event_id = ? AND posted_by = ?");
                $stmt->execute([$id, $userId]);
            }
        } else {
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ?");
                $stmt->execute([$id]);
            } else {
                $stmt = $pdo->prepare("DELETE FROM announcements WHERE announcement_id = ? AND posted_by = ?");
                $stmt->execute([$id, $userId]);
            }
        }
    } else {
        // ADD or EDIT logic
        if ($type === 'event') {
            $title = trim($_POST['event_title']);
            $desc = trim($_POST['event_description']);
            $date = $_POST['event_date'];
            $loc = trim($_POST['event_location']);

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, location, posted_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $desc, $date, $loc, $userId]);
            } else {
                $id = $_POST['id'];
                // Admins can edit any event, otherwise require ownership
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                    $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, location=? WHERE event_id=?");
                    $stmt->execute([$title, $desc, $date, $loc, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, location=? WHERE event_id=? AND posted_by=?");
                    $stmt->execute([$title, $desc, $date, $loc, $id, $userId]);
                }
            }
        } elseif ($type === 'announcement') {
            $title = trim($_POST['announcement_title']);
            $content = trim($_POST['announcement_content']);
            $img = null;

            if (!empty($_FILES['announcement_image']['name'])) {
                $tmp = $_FILES['announcement_image']['tmp_name'];
                $info = @getimagesize($tmp);
                $img = time() . '_' . basename($_FILES['announcement_image']['name']);
                $uploadsDir = __DIR__ . '/uploads/announcements';
                if (!is_dir($uploadsDir)) mkdir($uploadsDir, 0755, true);
                $dest = $uploadsDir . '/' . $img;

                if ($info !== false) {
                    $origW = $info[0];
                    $origH = $info[1];
                    $type = $info[2];
                    $maxW = 1200; $maxH = 800;
                    $ratio = min($maxW / $origW, $maxH / $origH, 1);
                    $newW = max(1, (int)($origW * $ratio));
                    $newH = max(1, (int)($origH * $ratio));

                    $srcImg = null;
                    switch ($type) {
                        case IMAGETYPE_JPEG: $srcImg = imagecreatefromjpeg($tmp); break;
                        case IMAGETYPE_PNG: $srcImg = imagecreatefrompng($tmp); break;
                        case IMAGETYPE_GIF: $srcImg = imagecreatefromgif($tmp); break;
                        case IMAGETYPE_WEBP: $srcImg = imagecreatefromwebp($tmp); break;
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
                        move_uploaded_file($tmp, $dest);
                    }
                } else {
                    move_uploaded_file($tmp, $dest);
                }
            }

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO announcements (title, content, posted_by, image) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $content, $userId, $img]);
            } else {
                $id = $_POST['id'];
                if ($img) {
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, image=? WHERE announcement_id=?");
                        $stmt->execute([$title, $content, $img, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, image=? WHERE announcement_id=? AND posted_by=?");
                        $stmt->execute([$title, $content, $img, $id, $userId]);
                    }
                } else {
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=? WHERE announcement_id=?");
                        $stmt->execute([$title, $content, $id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=? WHERE announcement_id=? AND posted_by=?");
                        $stmt->execute([$title, $content, $id, $userId]);
                    }
                }
            }
        }
    }
}
// Dynamic redirect based on what you were managing
$redirect = ($type === 'event') ? 'manageEvent' : 'manageAnnouncement';
header("Location: dashboard.php?content=" . $redirect);
exit;