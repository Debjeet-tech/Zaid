<?php
require_once __DIR__ . '/../lib/session.php';
ensure_session_started();
$user = $_SESSION['user'] ?? null;
if (!$user) {
  header('Location: /quiz-arena/public/index.php#auth');
  exit;
}
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<section class="panel">
  <h2>AI Practice</h2>
  <form id="aiSetup" class="flex-row">
    <select name="category" required>
      <option value="science">Science</option>
      <option value="sports">Sports</option>
      <option value="gk">GK</option>
      <option value="video_games">Video Games</option>
    </select>
    <select name="difficulty" required>
      <option value="easy">Easy</option>
      <option value="medium">Medium</option>
      <option value="hard">Hard</option>
      <option value="extreme">Extreme</option>
    </select>
    <button class="btn primary" type="submit">Start</button>
  </form>
  <div id="aiGame" class="game hidden"></div>
</section>
<script defer src="/quiz-arena/public/assets/js/ai.js"></script>
<?php include __DIR__ . '/partials/footer.php'; ?>
