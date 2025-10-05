<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/session.php';
require_once __DIR__ . '/../lib/db.php';
require_login();

$userId = (int)$_SESSION['user']['id'];
$matchId = (int)($_GET['match_id'] ?? 0);
$pdo = getPDO();

// Fetch match state
$stmt = $pdo->prepare("SELECT * FROM matches WHERE id = ?");
$stmt->execute([$matchId]);
$match = $stmt->fetch();
if (!$match) { echo json_encode(['ok'=>false,'error'=>'Match not found']); exit; }
if ($match['player1_id'] != $userId && $match['player2_id'] != $userId) { echo json_encode(['ok'=>false,'error'=>'Not your match']); exit; }

if ($match['status'] === 'queued') {
  echo json_encode(['ok'=>true,'status_text'=>'Waiting for opponent...']);
  exit;
}

// Determine progress: advance only after both players answered each question
$qcount = (int)$pdo->query("SELECT COUNT(*) FROM match_questions WHERE match_id = $matchId")->fetchColumn();
$idxStmt = $pdo->prepare("
  SELECT mq.question_index
  FROM match_questions mq
  LEFT JOIN answers a
    ON a.match_id = mq.match_id AND a.question_id = mq.question_id
  WHERE mq.match_id = ?
  GROUP BY mq.question_index
  HAVING COUNT(a.id) < 2
  ORDER BY mq.question_index ASC
  LIMIT 1
");
$idxStmt->execute([$matchId]);
$indexRow = $idxStmt->fetch();
$index = $indexRow ? (int)$indexRow['question_index'] : $qcount;

if ($index >= $qcount && $qcount > 0) {
  // Completed: decide winner by score, time tiebreaker
  $scoreStmt = $pdo->prepare("SELECT user_id, SUM(is_correct) AS score, SUM(time_ms) AS time_ms FROM answers WHERE match_id = ? GROUP BY user_id");
  $scoreStmt->execute([$matchId]);
  $scores = $scoreStmt->fetchAll();
  $p1 = ['score'=>0,'time_ms'=>PHP_INT_MAX];
  $p2 = ['score'=>0,'time_ms'=>PHP_INT_MAX];
  foreach ($scores as $row) {
    if ((int)$row['user_id'] === (int)$match['player1_id']) $p1 = ['score'=>(int)$row['score'],'time_ms'=>(int)$row['time_ms']];
    if ((int)$row['user_id'] === (int)$match['player2_id']) $p2 = ['score'=>(int)$row['score'],'time_ms'=>(int)$row['time_ms']];
  }
  $winnerId = null;
  if ($p1['score'] > $p2['score']) $winnerId = (int)$match['player1_id'];
  else if ($p2['score'] > $p1['score']) $winnerId = (int)$match['player2_id'];
  else if ($p1['time_ms'] < $p2['time_ms']) $winnerId = (int)$match['player1_id'];
  else if ($p2['time_ms'] < $p1['time_ms']) $winnerId = (int)$match['player2_id'];

  $pdo->prepare("UPDATE matches SET status='completed', winner_id = ?, ended_at = NOW() WHERE id = ?")
      ->execute([$winnerId, $matchId]);

  $winnerText = $winnerId ? 'Winner: ' . ($winnerId === (int)$match['player1_id'] ? 'Player 1' : 'Player 2') : 'Draw!';
  echo json_encode(['ok'=>true,'completed'=>true,'winner_text'=>$winnerText]);
  exit;
}

// Next question for this index
$qStmt = $pdo->prepare("SELECT q.id, q.question_text, q.option_a, q.option_b, q.option_c, q.option_d, q.correct_option FROM match_questions mq JOIN questions q ON q.id = mq.question_id WHERE mq.match_id = ? AND mq.question_index = ?");
$qStmt->execute([$matchId, $index]);
$question = $qStmt->fetch();
if (!$question) { echo json_encode(['ok'=>true,'status_text'=>'Preparing questions...']); exit; }

echo json_encode(['ok'=>true,'status_text'=>'Battle in progress','question'=>$question,'index'=>$index,'total'=>$qcount]);
