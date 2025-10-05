<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/session.php';
require_once __DIR__ . '/../lib/db.php';
ensure_session_started();
if (empty($_SESSION['user'])) { http_response_code(401); echo json_encode(['ok'=>false,'error'=>'Not authenticated']); exit; }

$category = $_GET['category'] ?? 'science';
$difficulty = $_GET['difficulty'] ?? 'easy';
$limit = (int)($_GET['limit'] ?? 10);
$limit = max(1, min(20, $limit));

try {
  $pdo = getPDO();
  $stmt = $pdo->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d, correct_option FROM questions WHERE category = ? AND difficulty = ? ORDER BY RAND() LIMIT $limit");
  $stmt->execute([$category, $difficulty]);
  $questions = $stmt->fetchAll();
  echo json_encode(['ok'=>true,'questions'=>$questions]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Failed to fetch questions']);
}
