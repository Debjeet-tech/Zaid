<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/db.php';
$pdo = getPDO();
$stmt = $pdo->query("SELECT u.username, COUNT(m.id) AS wins FROM users u LEFT JOIN matches m ON m.winner_id = u.id GROUP BY u.id ORDER BY wins DESC, u.username ASC LIMIT 50");
$rows = $stmt->fetchAll();
echo json_encode(['ok'=>true,'rows'=>$rows]);
