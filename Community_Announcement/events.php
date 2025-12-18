<main class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">

  <section class="bg-white p-4 rounded shadow col-span-2">
    <h2 class="text-lg font-semibold mb-4 text-green-700">Upcoming Events</h2>
    <div class="space-y-4">
      <?php if (!empty($events)): ?>
        <?php foreach ($events as $ev): ?>
          <div class="border-b pb-4" data-aos="fade-up">
            <div class="flex items-start justify-between gap-4">
              <div>
                <h3 class="text-md font-bold"><?= htmlspecialchars($ev['title']) ?></h3>
                <div class="text-xs text-gray-500"><?= htmlspecialchars(date('F j, Y', strtotime($ev['event_date']))) ?><?= !empty($ev['location']) ? ' • ' . htmlspecialchars($ev['location']) : '' ?></div>
                <p class="text-sm text-gray-700 mt-2"><?= nl2br(htmlspecialchars($ev['description'])) ?></p>
                <div class="mt-2 text-xs text-gray-500">Posted by <?= htmlspecialchars($ev['full_name'] ?? 'Unknown') ?></div>
              </div>

              <div class="flex flex-col items-end gap-3">
                <?php
                  // Reaction count (likes)
                  $eventLikes = 0;
                  $userEventReaction = null;
                  try {
                    $stmtER = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE event_id = ? AND reaction_type = 'like'");
                    $stmtER->execute([$ev['event_id']]);
                    $eventLikes = (int)$stmtER->fetchColumn();

                    if (isset($_SESSION['user_id'])) {
                        $stmtEur = $pdo->prepare("SELECT reaction_type FROM reactions WHERE event_id = ? AND user_id = ? LIMIT 1");
                        $stmtEur->execute([$ev['event_id'], $_SESSION['user_id']]);
                        $userEventReaction = $stmtEur->fetchColumn();
                    }
                  } catch (Exception $e) {}
                ?>

                <form method="POST" action="post_reaction.php" class="inline">
                  <input type="hidden" name="target" value="event">
                  <input type="hidden" name="id" value="<?= $ev['event_id'] ?>">
                  <input type="hidden" name="reaction_type" value="like">
                  <button type="submit" class="flex items-center gap-2 text-sm text-green-700 hover:text-green-800 btn-animate">
                    <i class="fa-regular fa-thumbs-up <?= $userEventReaction === 'like' ? 'text-green-800' : '' ?>"></i>
                    <span><?= $eventLikes ?> Like<?= $eventLikes !== 1 ? 's' : '' ?></span>
                  </button>
                </form>

                <div class="text-xs text-gray-400">Event</div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-sm text-gray-600">No upcoming events found.</p>
      <?php endif; ?>
    </div>
  </section>

  <aside class="bg-white p-4 rounded shadow">
    <h2 class="text-lg font-semibold mb-4 text-green-700">Announcements</h2>
    <ul class="space-y-3 text-sm">
      <?php if (!empty($announcements)): ?>
        <?php foreach (array_slice($announcements,0,5) as $ann): ?>
          <li class="mb-3"> <strong><?= htmlspecialchars($ann['title']) ?></strong><br>
            <span class="text-gray-600 text-xs">Posted by <?= htmlspecialchars($ann['full_name'] ?? 'Unknown') ?> • <?= htmlspecialchars(date('M j, Y', strtotime($ann['posted_at']))) ?></span>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <li class="text-gray-600">No announcements yet.</li>
      <?php endif; ?>
    </ul>
  </aside>

</main>