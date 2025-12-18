<main class="p-6 grid grid-cols-4 md:grid-cols-3 gap-6">

<section class="bg-white p-4 rounded shadow col-span-2">
      <h2 class="text-lg font-semibold mb-4 text-green-700">Recent Announcements</h2>



      <div class="space-y-4">
        <?php if (!empty($announcements)): ?>
          <?php foreach ($announcements as $ann): ?>
            <div class="border-b pb-4" data-aos="fade-up">
              <h3 class="text-md font-bold"><?= htmlspecialchars($ann['title']) ?></h3>
              <p class="text-sm text-gray-600"><?= nl2br(htmlspecialchars($ann['content'])) ?></p>
              <!-- ✅ Display image if available -->
              <?php
                // Only show announcement images if the current viewer is admin OR the post was made by an admin
                $canSeeImage = false;
                if (!empty($ann['image'])) {
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        $canSeeImage = true;
                    } elseif (!empty($ann['poster_role']) && $ann['poster_role'] === 'admin') {
                        $canSeeImage = true;
                    }
                }
              ?>

              <?php if ($canSeeImage): ?>
                <div class="mt-2 rounded overflow-hidden h-64">
                  <img src="uploads/announcements/<?= htmlspecialchars($ann['image']) ?>"
                       alt="Announcement image"
                       class="w-full h-full object-cover transition-transform duration-200 hover:scale-105">
                </div>
              <?php endif; ?>

              <?php
                // Reactions: count likes and get current user's reaction if logged in
                $likeCount = 0;
                $userReaction = null;
                if (isset($pdo) && isset($ann['announcement_id'])) {
                    try {
                        $stmtR = $pdo->prepare("SELECT COUNT(*) FROM reactions WHERE announcement_id = ? AND reaction_type = 'like'");
                        $stmtR->execute([$ann['announcement_id']]);
                        $likeCount = (int)$stmtR->fetchColumn();

                        if (isset($_SESSION['user_id'])) {
                            $stmtUR = $pdo->prepare("SELECT reaction_type FROM reactions WHERE announcement_id = ? AND user_id = ? LIMIT 1");
                            $stmtUR->execute([$ann['announcement_id'], $_SESSION['user_id']]);
                            $userReaction = $stmtUR->fetchColumn();
                        }
                    } catch (Exception $e) {
                        // ignore
                    }
                }
              ?>

              <div class="mt-2 flex gap-4 text-sm text-green-700 items-center">
                <span class="text-gray-500">Posted by <?= htmlspecialchars($ann['full_name'] ?? 'Unknown') ?> • <?= htmlspecialchars(date('M j, Y H:i', strtotime($ann['posted_at']))) ?></span>

                <div class="ml-auto flex items-center gap-3">
                  <form method="POST" action="post_reaction.php" class="inline">
                    <input type="hidden" name="target" value="announcement">
                    <input type="hidden" name="id" value="<?= $ann['announcement_id'] ?>">
                    <input type="hidden" name="reaction_type" value="like">
                    <button type="submit" class="flex items-center gap-2 text-sm text-green-700 hover:text-green-800">
                      <i class="fa-regular fa-thumbs-up <?= $userReaction === 'like' ? 'text-green-800' : '' ?>"></i>
                      <span><?= $likeCount ?> Like<?= $likeCount !== 1 ? 's' : '' ?></span>
                    </button>
                  </form>
                </div>
              </div>

              <!-- Comments -->
              <div class="mt-4">
                <?php
                  // Fetch comments for this announcement
                  $stmtC = $pdo->prepare("SELECT c.*, u.full_name FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE c.announcement_id = ? ORDER BY c.commented_at ASC");
                  $stmtC->execute([$ann['announcement_id']]);
                  $comments = $stmtC->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="space-y-3">
                  <?php foreach ($comments as $c): ?>
                    <div class="text-sm bg-gray-50 p-3 rounded">
                      <div class="text-xs text-gray-500 font-bold"><?= htmlspecialchars($c['full_name'] ?? 'User') ?> • <?= htmlspecialchars(date('M j, Y H:i', strtotime($c['commented_at']))) ?></div>
                      <div class="mt-1 text-gray-700"><?= nl2br(htmlspecialchars($c['comment_text'])) ?></div>
                    </div>
                  <?php endforeach; ?>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                  <form method="POST" action="post_comment.php" class="mt-3 flex gap-2">
                    <input type="hidden" name="announcement_id" value="<?= $ann['announcement_id'] ?>">
                    <textarea name="comment_text" placeholder="Write a comment..." required class="flex-1 border rounded px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    <button type="submit" class="bg-green-700 text-white px-4 py-2 rounded btn-animate text-sm">Comment</button>
                  </form>
                <?php else: ?>
                  <div class="mt-3 text-xs text-gray-400">Log in to join the conversation.</div>
                <?php endif; ?>

              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-sm text-gray-600">No announcements yet. Be the first to post one.</p>
        <?php endif; ?>
      </div>
    </section>

    <aside class="bg-white p-4 rounded shadow">
      <h2 class="text-lg font-semibold mb-4 text-green-700">Upcoming Events</h2>
      <ul class="space-y-3 text-sm">
        <?php if (!empty($events)): ?>
          <?php foreach ($events as $ev): ?>
            <li data-aos="fade-up">
              <?php
                // Event reaction summary
                $eventLikes = 0;
                $userEventReaction = null;
                if (isset($pdo) && isset($ev['event_id'])) {
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
                }
              ?>
              <div class="flex items-start justify-between">
                <div>
                  <strong><?= htmlspecialchars($ev['title']) ?></strong><br>
                  <span class="text-gray-600"><?= htmlspecialchars(date('F j, Y', strtotime($ev['event_date']))) ?><?= !empty($ev['location']) ? ' • ' . htmlspecialchars($ev['location']) : '' ?></span>
                  <div class="text-xs text-gray-500">Posted by <?= htmlspecialchars($ev['full_name'] ?? 'Unknown') ?></div>
                </div>

                <div class="ml-4 text-sm text-green-700">
                  <form method="POST" action="post_reaction.php" class="inline">
                    <input type="hidden" name="target" value="event">
                    <input type="hidden" name="id" value="<?= $ev['event_id'] ?>">
                    <input type="hidden" name="reaction_type" value="like">
                    <button type="submit" class="flex items-center gap-2 hover:text-green-800">
                      <i class="fa-regular fa-thumbs-up <?= $userEventReaction === 'like' ? 'text-green-800' : '' ?>"></i>
                      <span><?= $eventLikes ?> Like<?= $eventLikes !== 1 ? 's' : '' ?></span>
                    </button>
                  </form>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li class="text-gray-600">No upcoming events.</li>
        <?php endif; ?>
      </ul>
      <!-- Placeholder for calendar widget -->
      <div class="mt-6">
        <p class="text-sm text-gray-500">[Calendar Widget Placeholder]</p>
      </div>
    </aside>
    </main>