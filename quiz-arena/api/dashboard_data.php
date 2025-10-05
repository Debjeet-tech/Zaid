<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/session.php';
require_once __DIR__ . '/../lib/db.php';
require_login();
$userId = (int)$_SESSION['user']['id'];

$pdo = getPDO();
$total = (int)$pdo->query("SELECT COUNT(*) FROM matches WHERE player1_id = $userId OR player2_id = $userId")->fetchColumn();
$wins = (int)$pdo->query("SELECT COUNT(*) FROM matches WHERE winner_id = $userId")->fetchColumn();
$wr = $total > 0 ? round(($wins/$total)*100, 1) : 0;

echo json_encode(['ok'=>true,'total_matches'=>$total,'wins'=>$wins,'win_rate'=>$wr]);
