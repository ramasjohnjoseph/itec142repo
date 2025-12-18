<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$userId = $_SESSION['user_id'];
$target = $_POST['target'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$reaction_type = $_POST['reaction_type'] ?? 'like';

if (!in_array($target, ['announcement','event']) || !$id) {
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
    exit;
}

try {
    if ($target === 'announcement') {
        $stmt = $pdo->prepare("SELECT reaction_id, reaction_type FROM reactions WHERE user_id = ? AND announcement_id = ? LIMIT 1");
        $stmt->execute([$userId, $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['reaction_type'] === $reaction_type) {
                // toggle off
                $del = $pdo->prepare("DELETE FROM reactions WHERE reaction_id = ?");
                $del->execute([$row['reaction_id']]);
            } else {
                $upd = $pdo->prepare("UPDATE reactions SET reaction_type = ?, reacted_at = CURRENT_TIMESTAMP WHERE reaction_id = ?");
                $upd->execute([$reaction_type, $row['reaction_id']]);
            }
        } else {
            $ins = $pdo->prepare("INSERT INTO reactions (user_id, announcement_id, reaction_type) VALUES (?, ?, ?)");
            $ins->execute([$userId, $id, $reaction_type]);
        }
    } else {
        $stmt = $pdo->prepare("SELECT reaction_id, reaction_type FROM reactions WHERE user_id = ? AND event_id = ? LIMIT 1");
        $stmt->execute([$userId, $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['reaction_type'] === $reaction_type) {
                $del = $pdo->prepare("DELETE FROM reactions WHERE reaction_id = ?");
                $del->execute([$row['reaction_id']]);
            } else {
                $upd = $pdo->prepare("UPDATE reactions SET reaction_type = ?, reacted_at = CURRENT_TIMESTAMP WHERE reaction_id = ?");
                $upd->execute([$reaction_type, $row['reaction_id']]);
            }
        } else {
            $ins = $pdo->prepare("INSERT INTO reactions (user_id, event_id, reaction_type) VALUES (?, ?, ?)");
            $ins->execute([$userId, $id, $reaction_type]);
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'dashboard.php'));
exit;
?>