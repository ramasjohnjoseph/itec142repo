<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="p-6"><div class="bg-white p-4 rounded shadow text-sm text-gray-600">You are not authorized to manage users.</div></div>';
    return;
}

// Fetch users
$stmt = $pdo->query("SELECT user_id, username, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="p-4 md:p-6">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-xl md:text-2xl font-bold text-gray-800">Manage Users</h2>
  </div>

  <div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
      <thead>
        <tr class="text-left bg-gray-50">
          <th class="p-3">Username</th>
          <th class="p-3">Full Name</th>
          <th class="p-3">Email</th>
          <th class="p-3">Role</th>
          <th class="p-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr class="border-t">
            <td class="p-3 font-bold"><?= htmlspecialchars($u['username']) ?></td>
            <td class="p-3"><?= htmlspecialchars($u['full_name'] ?? '-') ?></td>
            <td class="p-3"><?= htmlspecialchars($u['email'] ?? '-') ?></td>
            <td class="p-3"><?= htmlspecialchars($u['role']) ?></td>
            <td class="p-3">
              <?php if ($u['user_id'] !== $_SESSION['user_id']): ?>
                <form method="POST" action="process_user.php" onsubmit="return confirm('Change role?');" class="inline">
                  <input type="hidden" name="action" value="set_role">
                  <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                  <select name="role" class="text-sm p-2 border rounded">
                    <option value="user" <?= $u['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                  </select>
                  <button type="submit" class="ml-2 bg-green-700 text-white px-3 py-1 rounded">Save</button>
                </form>
              <?php else: ?>
                <span class="text-xs text-gray-500">(You)</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>