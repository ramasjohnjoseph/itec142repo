<?php
function fetchAnnouncements($pdo, $limit = 10) {
    // Include the poster's role so we can control visibility of images for non-admin viewers
    $stmt = $pdo->prepare("SELECT a.*, u.full_name, u.role AS poster_role FROM announcements a LEFT JOIN users u ON a.posted_by = u.user_id ORDER BY a.posted_at DESC LIMIT :limit");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function fetchUpcomingEvents($pdo, $limit = 10) {
    // Include poster role for events as well
    $stmt = $pdo->prepare("SELECT e.*, u.full_name, u.role AS poster_role FROM events e LEFT JOIN users u ON e.posted_by = u.user_id WHERE e.event_date >= CURDATE() ORDER BY e.event_date ASC LIMIT :limit");
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}