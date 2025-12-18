<?php
require 'config.php';
require 'functions.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Flash message handling
$message = "";
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// Fetch announcements (with fallback if function fails)
$announcements = fetchAnnouncements($pdo, 10);
if (empty($announcements)) {
    try {
        $stmt = $pdo->prepare("SELECT a.*, u.full_name FROM announcements a LEFT JOIN users u ON a.posted_by = u.user_id ORDER BY a.posted_at DESC LIMIT 10");
        $stmt->execute();
        $announcements = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $announcements = [];
    }
}

// Fetch upcoming events (with fallback)
$events = fetchUpcomingEvents($pdo, 10);
if (empty($events)) {
    try {
        $stmt = $pdo->prepare("SELECT e.*, u.full_name FROM events e LEFT JOIN users u ON e.posted_by = u.user_id WHERE e.event_date >= CURDATE() ORDER BY e.event_date ASC LIMIT 10");
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $events = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Community Bulletin Board System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- AOS for scroll animations -->
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
  <style>

    .burger div:nth-child(1).open { transform: rotate(-90deg) translateY(10px); }
    .burger div:nth-child(3).open { transform: rotate(90deg) translateY(-10px); }

    /* Modal dialog animation helpers */
    .modal-dialog { transform-origin: center center; transition: transform .28s ease, opacity .28s ease; }
    .modal-open .modal-dialog { opacity: 1 !important; transform: scale(1) !important; }

    /* Buttons and cards micro-interactions */
    .btn-animate { transition: transform .18s ease, box-shadow .18s ease; }
    .btn-animate:active { transform: scale(.98); }
    .content-item { transition: transform .22s ease, box-shadow .22s ease; }
    .content-item:hover { transform: translateY(-6px); box-shadow: 0 10px 20px rgba(0,0,0,0.06); }

    /* Respect user motion preferences */
    @media (prefers-reduced-motion: reduce) {
      *, *::before, *::after { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; }
    }

  </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans">
  <!-- Header (shared) -->
  <?php include __DIR__ . '/header.php'; ?>

  <!-- Left Sidebar Panel -->
  <div id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-green-50 to-white shadow-xl transform -translate-x-full transition-transform duration-300 ease-in-out z-30 border-r border-gray-200">
    <div class="p-6">
      <button id="closeSidebar" class="absolute top-4 right-4 text-green-600 hover:text-green-800 text-2xl font-bold transition-colors duration-200">&times;</button>
      <h2 class="text-xl font-bold mb-6 text-green-800 border-b border-green-200 pb-2">Menu</h2>
      <nav class="space-y-3">
        <a href="?content=announcement" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-100 hover:text-green-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
          <span class="mr-3 text-lg"><img src="icons/dashboard.png" alt="Dashboard"></span> Dashboard
        </a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
          <a href="?content=manageEvent" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-100 hover:text-green-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
            <span class="mr-3 text-lg"><img src="icons/events.png" alt="Events"></span> Manage Events
          </a>
          <a href="?content=manageAnnouncement" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-100 hover:text-green-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
            <span class="mr-3 text-lg"><img src="icons/announce.png" alt="Announcements"></span> Manage Announcements
          </a>
          <a href="?content=manageUsers" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-100 hover:text-green-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
            <span class="mr-3 text-lg"><img src="icons/userlogs.png" alt="Users"></span> Manage Users
          </a>
          <a href="?content=userLogs" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-100 hover:text-green-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
            <span class="mr-3 text-lg"><img src="icons/userlogs.png" alt="Logs"></span> User Logs
          </a>
        <?php else: ?>
          <a href="/home.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-green-100 hover:text-green-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
            <span class="mr-3 text-lg"><i class="fa-solid fa-house"></i></span> Home
          </a>
        <?php endif; ?>
        <a href="logout.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-red-100 hover:text-red-900 rounded-lg transition-all duration-200 shadow-sm font-semibold">
          <span class="mr-3 text-lg"><img src="icons/logout.png" alt="Logout"></span> Log Out
        </a>
      </nav>
    </div>
  </div>

  <!-- Overlay for sidebar -->
  <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-20"></div>

  <?php if ($message): ?>
    <div class="max-w-4xl mx-auto mt-4 px-4">
      <div class="bg-white p-3 rounded shadow text-sm text-green-700"><?= htmlspecialchars($message) ?></div>
    </div>
  <?php endif; ?>

  
  <!-- Main Content -->
  <main class="p-6 gap-6">
    <!-- Quick post placeholder (same as Announcements) -->
    <div class="max-w-4xl mx-auto mb-6" data-aos="fade-up">
      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="mb-4 bg-white border rounded-xl p-4 shadow-sm">
          <div class="flex items-start gap-3">
              <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-green-700 to-green-500 flex items-center justify-center text-white"> 
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                  <path d="M12 2c-2.76 0-5 2.24-5 5 0 2.76 2.24 5 5 5s5-2.24 5-5c0-2.76-2.24-5-5-5z" fill="white"/>
                </svg>
              </div>
            <div class="flex-1">
              <button onclick="openModal('announcement','add')" class="w-full text-left border rounded-xl px-4 py-3 text-gray-600 hover:border-green-200 focus:outline-none btn-animate">What's on your mind?</button>
              <div class="mt-3 flex gap-3">
                <button onclick="openModal('announcement','add')" class="px-4 py-2 bg-green-700 text-white rounded-full text-sm btn-animate">Post Announcement</button>
                <button onclick="openModal('event','add')" class="px-4 py-2 bg-blue-500 text-white rounded-full text-sm btn-animate">Create Event</button>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="mb-4 bg-white border rounded-xl p-4 shadow-sm text-gray-600">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 transition-transform transform hover:scale-110">
              <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <i class="fa-solid fa-user-shield text-green-700" aria-hidden="true"></i>
                <span class="sr-only">Admin</span>
              <?php else: ?>
                <i class="fa-regular fa-user" aria-hidden="true"></i>
                <span class="sr-only">User</span>
              <?php endif; ?>
            </div>
            <div class="flex-1">
              <div class="w-full text-left border rounded-xl px-4 py-3 text-gray-400">What's on your mind?</div>
              <div class="mt-2 text-xs text-gray-400">Only admins can post announcements or create events.</div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <?php
    if (isset($_GET['content'])) {
        $content = $_GET['content'];
        if ($content === "announcement") {
            include './announcements.php';
        } elseif ($content === "events") {
            include './events.php';
        } elseif ($content === "manageEvent") {
            include './manage_events.php';
        } elseif ($content === "manageAnnouncement") {
            include './manage_announcements.php';
        } elseif ($content === "manageUsers") {
            include './manage_users.php';
        } else {
            include './announcements.php'; // Default
        }
    } else {
        include './announcements.php'; // Default
    }
    ?>
  </main>

  <?php include 'footer.php'; ?>
  <?php include 'modals.php'; ?>
  <?php include 'scripts.php'; ?>
</body>
</html>