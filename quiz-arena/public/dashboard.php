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
  <h2>Dashboard</h2>
  <div id="dashboardStats" class="stats-grid"></div>
  <div class="actions">
    <a class="btn primary" href="/quiz-arena/public/ai.php">Play AI Mode</a>
    <a class="btn" href="/quiz-arena/public/arena.php">Find a Battle</a>
  </div>
</section>
<script defer src="/quiz-arena/public/assets/js/dashboard.js"></script>
<?php include __DIR__ . '/partials/footer.php'; ?>
