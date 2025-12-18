<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$announcementId = isset($_POST['announcement_id']) ? (int)$_POST['announcement_id'] : 0;
$commentText = trim($_POST['comment_text'] ?? '');
$userId = $_SESSION['user_id'];

if (!$announcementId || $commentText === '') {
    $_SESSION['flash_message'] = 'Comment cannot be empty.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
}

try {
    // Optional: ensure announcement exists
    $stmt = $pdo->prepare("SELECT announcement_id FROM announcements WHERE announcement_id = ? LIMIT 1");
    $stmt->execute([$announcementId]);
    if (!$stmt->fetchColumn()) {
        $_SESSION['flash_message'] = 'Announcement not found.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
        exit;
    }

    $ins = $pdo->prepare("INSERT INTO comments (user_id, announcement_id, comment_text) VALUES (?, ?, ?)");
    $ins->execute([$userId, $announcementId, $commentText]);
    $_SESSION['flash_message'] = 'Comment posted.';
} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['flash_message'] = 'Failed to post comment.';
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
exit;
?>