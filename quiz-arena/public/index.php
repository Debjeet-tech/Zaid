<?php
require_once __DIR__ . '/../lib/session.php';
ensure_session_started();
$user = $_SESSION['user'] ?? null;
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<section class="hero">
  <h1 class="title glow">Battle of Brains</h1>
  <p class="subtitle">Pick your mode: AI or Real-Time duel</p>
  <div class="cards">
    <a class="card" href="/quiz-arena/public/ai.php">
      <div class="card-icon">🤖</div>
      <div class="card-title">AI Mode</div>
      <div class="card-desc">Practice with timed questions</div>
    </a>
    <a class="card" href="/quiz-arena/public/arena.php">
      <div class="card-icon">⚔️</div>
      <div class="card-title">Real-Time Battle</div>
      <div class="card-desc">Match with another player</div>
    </a>
  </div>
</section>
<section id="auth" class="auth">
  <?php if (!$user): ?>
  <div class="auth-forms">
    <form id="registerForm" class="panel" method="post" action="/quiz-arena/api/register.php">
      <h3>Create Account</h3>
      <input name="username" type="text" placeholder="Username" required />
      <input name="password" type="password" placeholder="Password (min 6)" required />
      <button class="btn primary" type="submit">Register</button>
    </form>
    <form id="loginForm" class="panel" method="post" action="/quiz-arena/api/login.php">
      <h3>Login</h3>
      <input name="username" type="text" placeholder="Username" required />
      <input name="password" type="password" placeholder="Password" required />
      <button class="btn primary" type="submit">Login</button>
    </form>
  </div>
  <?php else: ?>
  <div class="panel">
    <h3>You're logged in</h3>
    <p>Head to <a class="link" href="/quiz-arena/public/dashboard.php">Dashboard</a> to start!</p>
  </div>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
