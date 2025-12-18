<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';
// Only admins may manage announcements
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="p-6"><div class="bg-white p-4 rounded shadow text-sm text-gray-600">You are not authorized to manage announcements.</div></div>';
    return;
}
$stmt = $pdo->prepare("SELECT a.*, u.full_name FROM announcements a LEFT JOIN users u ON a.posted_by = u.user_id ORDER BY a.posted_at DESC");
$stmt->execute();
$announcements = $stmt->fetchAll();
?>

<div class="p-4 md:p-6">
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manage Announcements</h2>
    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
        <div class="relative w-full sm:w-64">
            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                <i class="fa-solid fa-magnifying-glass text-sm"></i>
            </span>
            <input type="text" id="searchInput" placeholder="Search..." 
                   class="pl-10 pr-4 py-2 w-full border rounded-full text-sm outline-none focus:ring-2 focus:ring-green-500 shadow-sm">
        </div>

    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
    <?php foreach ($announcements as $ann): ?>
      <?php $d = date('M d, Y', strtotime($ann['posted_at'])); ?>
      <div class="content-item bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow" data-aos="fade-up"
           data-title="<?= htmlspecialchars($ann['title']) ?>" 
           data-date="<?= $d ?>">
        
        <div class="w-full h-48 sm:h-56 bg-gray-200 overflow-hidden">
            <?php if (!empty($ann['image'])): ?>
                <img src="uploads/announcements/<?= htmlspecialchars($ann['image']) ?>" 
                     class="w-full h-full object-cover" 
                     alt="Image">
            <?php else: ?>
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 border-b">
                    <i class="fa-regular fa-image text-2xl mb-2"></i>
                    <span class="text-xs italic uppercase font-bold tracking-tighter">No Photo</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="p-5">
          <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded">Posted <?= $d ?></span>
          <h3 class="text-lg font-bold text-gray-900 mb-2 mt-3 leading-tight truncate"><?= htmlspecialchars($ann['title']) ?></h3>
          <p class="text-gray-600 text-sm line-clamp-3 leading-relaxed"><?= htmlspecialchars($ann['content']) ?></p>
        </div>

        <div class="bg-gray-50 p-4 flex justify-end gap-3 border-t border-gray-100">
            <button onclick="openModal('announcement', 'edit', <?= $ann['announcement_id'] ?>, '<?= addslashes($ann['title']) ?>', '<?= addslashes($ann['content']) ?>', '', '', '<?= $ann['image'] ?>')" 
                    class="flex-1 sm:flex-none justify-center flex items-center gap-2 p-2 text-blue-600 bg-white border border-blue-100 rounded-xl hover:bg-blue-50 transition shadow-sm btn-animate">
              <i class="fa-regular fa-pen-to-square"></i> <span class="sm:hidden text-xs font-bold uppercase">Edit</span>
            </button>
            
            <form method="POST" action="process_manage.php" onsubmit="return confirm('Delete this?');" class="flex-1 sm:flex-none">
              <input type="hidden" name="type" value="announcement"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $ann['announcement_id'] ?>">
              <button type="submit" class="w-full justify-center flex items-center gap-2 p-2 text-red-600 bg-white border border-red-100 rounded-xl hover:bg-red-50 transition shadow-sm btn-animate">
                <i class="fa-regular fa-trash-can"></i> <span class="sm:hidden text-xs font-bold uppercase">Delete</span>
              </button>
            </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>