<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Only admins can create events
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_message'] = "Unauthorized: only admins can create events.";
    header('Location: dashboard.php');
    exit;
}

// Handle event creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['event_title']);
    $description = trim($_POST['event_description']);
    $event_date = trim($_POST['event_date']);
    $location = !empty($_POST['event_location']) ? trim($_POST['event_location']) : null;

    try {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, location, posted_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $event_date, $location, $_SESSION['user_id']]);
        $_SESSION['flash_message'] = "Event created successfully.";
    } catch (Exception $e) {
        $_SESSION['flash_message'] = "Error creating event: " . $e->getMessage();
    }
    header('Location: dashboard.php');
    exit;
}

// If not a POST request, redirect back
header('Location: dashboard.php');
exit;
?>
<!-- No HTML needed here since it redirects -->