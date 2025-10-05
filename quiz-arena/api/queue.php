<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/session.php';
require_once __DIR__ . '/../lib/db.php';
require_login();

$userId = (int)$_SESSION['user']['id'];
$category = $_POST['category'] ?? '';
$difficulty = $_POST['difficulty'] ?? '';

try {
  $pdo = getPDO();
  $pdo->beginTransaction();

  // Try to find an existing queued opponent
  $find = $pdo->prepare("SELECT id, player1_id FROM matches WHERE status = 'queued' AND category = ? AND difficulty = ? AND player1_id <> ? LIMIT 1 FOR UPDATE");
  $find->execute([$category, $difficulty, $userId]);
  $match = $find->fetch();

  if ($match) {
    $matchId = (int)$match['id'];
    $pdo->prepare("UPDATE matches SET player2_id = ?, status = 'active', started_at = NOW() WHERE id = ?")
        ->execute([$userId, $matchId]);

    // Prepare questions for this match
    $stmt = $pdo->prepare("SELECT id FROM questions WHERE category = ? AND difficulty = ? ORDER BY RAND() LIMIT 10");
    $stmt->execute([$category, $difficulty]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($ids as $index => $qid) {
      $pdo->prepare("INSERT INTO match_questions (match_id, question_id, question_index) VALUES (?, ?, ?)")
          ->execute([$matchId, (int)$qid, $index]);
    }
  } else {
    // Create a new queue entry
    $pdo->prepare("INSERT INTO matches (status, category, difficulty, player1_id, started_at) VALUES ('queued', ?, ?, ?, NULL)")
        ->execute([$category, $difficulty, $userId]);
    $matchId = (int)$pdo->lastInsertId();
  }

  $pdo->commit();
  echo json_encode(['ok'=>true, 'match_id'=>$matchId]);
} catch (Throwable $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Queue failed']);
}
