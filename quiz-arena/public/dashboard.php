<?php
require_once __DIR__ . '/../lib/session.php';
ensure_session_started();
$user = $_SESSION['user'] ?? null;
if (!$user) {
  header('Location: index.php#auth');
  exit;
}
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<section class="panel">
  <h2>Dashboard</h2>
  <div id="dashboardStats" class="stats-grid"></div>
  <div class="actions">
    <a class="btn primary" href="ai.php">Play AI Mode</a>
    <a class="btn" href="arena.php">Find a Battle</a>
  </div>
</section>
<script defer src="assets/js/dashboard.js"></script>
<?php include __DIR__ . '/partials/footer.php'; ?>
