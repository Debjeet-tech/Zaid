<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../lib/auth.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$result = login_user($username, $password);
echo json_encode($result);
