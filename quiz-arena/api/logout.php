<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/auth.php';

logout_user();
echo json_encode(['ok' => true]);
