<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';
// Only admins may manage events
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="p-6"><div class="bg-white p-4 rounded shadow text-sm text-gray-600">You are not authorized to manage events.</div></div>';
    return;
}
$stmt = $pdo->prepare("SELECT e.*, u.full_name FROM events e LEFT JOIN users u ON e.posted_by = u.user_id ORDER BY e.event_date ASC");
$stmt->execute();
$events = $stmt->fetchAll();
?>

<div class="p-4 md:p-6">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manage Events</h2>
    
    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <i class="fa-solid fa-calendar-day text-sm"></i>
            </span>
            <input type="text" id="searchInput" placeholder="Search events..." 
                   class="pl-10 pr-4 py-2 w-full border rounded-full text-sm outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
        </div>
        <button onclick="openModal('event', 'add')" 
                class="bg-green-700 text-white px-6 py-2 rounded-full font-semibold hover:bg-green-800 shadow-md transition text-center">
          + Create Event
        </button>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    <?php foreach ($events as $ev): ?>
      <?php $eventDate = date('M d, Y', strtotime($ev['event_date'])); ?>
      <div class="content-item bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow" data-aos="fade-up"
           data-title="<?= htmlspecialchars($ev['title']) ?>" 
           data-date="<?= $eventDate ?>">
        <div class="p-5">
          <div class="flex items-center justify-between mb-4">
             <span class="bg-green-100 text-green-700 text-[10px] font-extrabold px-2 py-1 rounded-md uppercase tracking-tighter">Upcoming</span>
             <span class="text-xs text-gray-400 font-bold"><?= $eventDate ?></span>
          </div>
          
          <h3 class="text-lg font-bold text-gray-900 mb-2 truncate"><?= htmlspecialchars($ev['title']) ?></h3>
          <p class="text-gray-500 text-xs mb-4 flex items-center gap-2 bg-gray-50 p-2 rounded-lg">
            <i class="fa-solid fa-location-dot text-red-400"></i> <?= htmlspecialchars($ev['location']) ?>
          </p>
          <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed"><?= htmlspecialchars($ev['description']) ?></p>
        </div>

        <div class="bg-gray-50 p-4 flex justify-end gap-3">
            <button onclick="openModal('event', 'edit', <?= $ev['event_id'] ?>, '<?= addslashes($ev['title']) ?>', '<?= addslashes($ev['description']) ?>', '<?= $ev['event_date'] ?>', '<?= addslashes($ev['location']) ?>')" 
                    class="flex-1 sm:flex-none justify-center flex items-center gap-2 p-2 text-blue-600 bg-white border border-blue-100 rounded-xl hover:bg-blue-50 transition shadow-sm btn-animate">
              <i class="fa-regular fa-pen-to-square"></i> <span class="sm:hidden text-xs font-bold uppercase">Edit</span>
            </button>
            <form method="POST" action="process_manage.php" onsubmit="return confirm('Delete?');" class="flex-1 sm:flex-none">
              <input type="hidden" name="type" value="event"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $ev['event_id'] ?>">
              <button type="submit" class="w-full justify-center flex items-center gap-2 p-2 text-red-600 bg-white border border-red-100 rounded-xl hover:bg-red-50 transition shadow-sm btn-animate">
                <i class="fa-regular fa-trash-can"></i> <span class="sm:hidden text-xs font-bold uppercase">Delete</span>
              </button>
            </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>