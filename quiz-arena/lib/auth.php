<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session.php';

function register_user(string $username, string $password): array {
    $username = trim($username);
    if ($username === '' || strlen($password) < 6) {
        return ['ok' => false, 'error' => 'Invalid username or password too short'];
    }
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        return ['ok' => false, 'error' => 'Username already taken'];
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $pdo->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (?, ?, NOW())')
        ->execute([$username, $hash]);
    $id = (int)$pdo->lastInsertId();
    ensure_session_started();
    $_SESSION['user'] = ['id' => $id, 'username' => $username];
    return ['ok' => true, 'user' => $_SESSION['user']];
}

function login_user(string $username, string $password): array {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'error' => 'Invalid credentials'];
    }
    ensure_session_started();
    $_SESSION['user'] = ['id' => (int)$user['id'], 'username' => $user['username']];
    return ['ok' => true, 'user' => $_SESSION['user']];
}

function logout_user(): void {
    ensure_session_started();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
?>
