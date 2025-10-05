<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/session.php';
require_once __DIR__ . '/../lib/db.php';
require_login();

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$matchId = (int)($input['match_id'] ?? 0);
$questionId = (int)($input['question_id'] ?? 0);
$answer = $input['answer'] ?? '';
$timeMs = (int)($input['time_ms'] ?? 0);
$userId = (int)$_SESSION['user']['id'];

try {
  $pdo = getPDO();
  // Validate match and membership
  $m = $pdo->prepare('SELECT status, player1_id, player2_id FROM matches WHERE id = ?');
  $m->execute([$matchId]);
  $match = $m->fetch();
  if (!$match) { echo json_encode(['ok'=>false,'error'=>'Match not found']); exit; }
  if ($match['status'] !== 'active') { echo json_encode(['ok'=>false,'error'=>'Match not active']); exit; }
  if ($match['player1_id'] != $userId && $match['player2_id'] != $userId) { echo json_encode(['ok'=>false,'error'=>'Not your match']); exit; }

  // Prevent duplicate answers per user per question
  $dup = $pdo->prepare('SELECT id FROM answers WHERE match_id = ? AND user_id = ? AND question_id = ?');
  $dup->execute([$matchId, $userId, $questionId]);
  if ($dup->fetch()) { echo json_encode(['ok'=>true]); exit; }

  $q = $pdo->prepare('SELECT correct_option FROM questions WHERE id = ?');
  $q->execute([$questionId]);
  $correct = $q->fetchColumn();
  $isCorrect = $correct === $answer ? 1 : 0;

  $ins = $pdo->prepare('INSERT INTO answers (match_id, user_id, question_id, answer, time_ms, is_correct) VALUES (?, ?, ?, ?, ?, ?)');
  $ins->execute([$matchId, $userId, $questionId, $answer, $timeMs, $isCorrect]);

  echo json_encode(['ok'=>true]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['ok'=>false,'error'=>'Submit failed']);
}
