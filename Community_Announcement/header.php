<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<header class="bg-white shadow-sm p-4 sticky top-0 z-40" data-aos="fade-down">
  <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-green-700 flex items-center justify-center text-white font-bold text-lg btn-animate">VSU</div>
      <div class="hidden md:block">
        <div class="text-sm font-semibold text-gray-700">VISAYAS STATE<br/>UNIVERSITY</div>
      </div>
      <nav class="ml-6 hidden md:flex items-center gap-4 text-sm">
     
        <a href="?content=announcement" class="text-gray-600 hover:text-green-700 transition-colors">Announcements</a>
        <a href="?content=events" class="text-gray-600 hover:text-green-700 transition-colors">Events</a>
      </nav>
    </div>

    <div class="flex items-center gap-3">
      <?php if (isset($_SESSION['user_id'])): ?>
        <div class="text-sm text-gray-700 mr-3 hidden sm:block">Hello, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></div>
        <div class="relative">
          <button id="headerAvatarBtn" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 focus:outline-none transition-transform transform hover:scale-110 btn-animate" aria-haspopup="true" aria-expanded="false" title="<?= htmlspecialchars($_SESSION['role'] ?? 'user') ?>">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <i class="fa-solid fa-user-shield text-green-700" aria-hidden="true"></i>
              <span class="sr-only">Admin</span>
            <?php else: ?>
              <i class="fa-regular fa-user" aria-hidden="true"></i>
              <span class="sr-only">User</span>
            <?php endif; ?>
          </button>
          <div id="headerAvatarMenu" class="absolute right-0 mt-2 w-48 bg-white rounded shadow-lg hidden py-2">
            <div class="px-3 py-2 text-xs text-gray-500">Role: <?= htmlspecialchars($_SESSION['role'] ?? 'user') ?></div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
              <a href="?content=manageAnnouncement" class="block px-3 py-2 text-sm hover:bg-gray-50">Manage Announcements</a>
              <a href="?content=manageEvent" class="block px-3 py-2 text-sm hover:bg-gray-50">Manage Events</a>
              <a href="?content=manageUsers" class="block px-3 py-2 text-sm hover:bg-gray-50">Manage Users</a>
            <?php else: ?>
              <a href="/home.php" class="block px-3 py-2 text-sm hover:bg-gray-50">Home</a>
            <?php endif; ?>
            <a href="logout.php" class="block px-3 py-2 text-sm text-red-600 hover:bg-gray-50">Log out</a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php" class="px-4 py-2 bg-green-700 text-white rounded-full text-sm btn-animate">Log in</a>
      <?php endif; ?>

      <button id="sidebarToggle" class="ml-2 md:hidden p-2 rounded text-green-700 border border-green-100 btn-animate" aria-label="Open menu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </div>
  </div>
</header>

<script>
// Header small behavior: avatar menu toggle
const avatarBtn = document.getElementById('headerAvatarBtn');
const avatarMenu = document.getElementById('headerAvatarMenu');
if (avatarBtn) {
  avatarBtn.addEventListener('click', () => {
    avatarMenu.classList.toggle('hidden');
    avatarBtn.setAttribute('aria-expanded', avatarMenu.classList.contains('hidden') ? 'false' : 'true');
  });
  document.addEventListener('click', (e) => {
    if (!avatarBtn.contains(e.target) && !avatarMenu.contains(e.target)) avatarMenu.classList.add('hidden');
  });
}

// Hook sidebar toggle button to existing sidebar logic (if present)
const sidebarToggle = document.getElementById('sidebarToggle');
const localSidebar = document.getElementById('sidebar');
if (sidebarToggle && localSidebar) {
  sidebarToggle.addEventListener('click', () => localSidebar.classList.remove('-translate-x-full'));
}
</script>