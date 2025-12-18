<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$action = $_POST['action'] ?? '';
if ($action === 'set_role') {
    $targetUser = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $newRole = $_POST['role'] === 'admin' ? 'admin' : 'user';

    if (!$targetUser) {
        $_SESSION['flash_message'] = 'Invalid user.';
        header('Location: dashboard.php?content=manageUsers');
        exit;
    }

    // Protect: ensure at least one admin remains
    if ($newRole === 'user') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE role = 'admin'");
        $stmt->execute();
        $adminCount = (int)$stmt->fetchColumn();

        // If the target user is currently admin and is the last admin, prevent demotion
        $stmt2 = $pdo->prepare("SELECT role FROM users WHERE user_id = ? LIMIT 1");
        $stmt2->execute([$targetUser]);
        $current = $stmt2->fetchColumn();

        if ($current === 'admin' && $adminCount <= 1) {
            $_SESSION['flash_message'] = 'Cannot demote the last admin.';
            header('Location: dashboard.php?content=manageUsers');
            exit;
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE user_id = ?");
    $stmt->execute([$newRole, $targetUser]);
    $_SESSION['flash_message'] = 'User role updated.';
}

header('Location: dashboard.php?content=manageUsers');
exit;
?>