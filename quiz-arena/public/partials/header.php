<?php
require_once __DIR__ . '/../../lib/session.php';
ensure_session_started();
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quiz Arena</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <script defer src="assets/js/app.js"></script>
</head>
<body>
  <nav class="navbar">
    <div class="brand">⚡ Quiz Arena</div>
    <div class="nav-actions">
      <?php if ($user): ?>
        <span class="hello">Hi, <?php echo htmlspecialchars($user['username']); ?>!</span>
        <a class="btn" href="dashboard.php">Dashboard</a>
        <a class="btn" href="leaderboard.php">Leaderboard</a>
        <form class="inline" method="post" action="../api/logout.php">
          <button class="btn danger" type="submit">Logout</button>
        </form>
      <?php else: ?>
        <a class="btn" href="index.php#auth">Login</a>
      <?php endif; ?>
    </div>
  </nav>
  <main class="container">
