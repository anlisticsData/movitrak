<?php
header('Content-Type: application/json');
session_start();

require_once '../includes/db.php';
require_once '../includes/functions.php';

// Só aceita POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
    exit;
}

// Recebe login e senha
$login = validateInput($_POST['login'] ?? '');
$password = validateInput($_POST['password'] ?? '');

if (!$login || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Login e senha são obrigatórios"]);
    exit;
}

// Verifica usuário
$stmt = $pdo->prepare("SELECT id, password FROM users WHERE login = :login");
$stmt->bindParam(':login', $login);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["error" => "Usuário ou senha inválidos"]);
    exit;
}

// ✅ Usuário autenticado, gerar token
$token = bin2hex(random_bytes(32));
$expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

$stmtToken = $pdo->prepare("
    INSERT INTO user_tokens (user_id, token, expires_at)
    VALUES (:uid, :token, :exp)
");
$stmtToken->execute([
    ':uid' => $user['id'],
    ':token' => $token,
    ':exp' => $expires_at
]);

// Retornar JSON
echo json_encode([
    "success" => true,
    "user_id" => $user['id'],
    "token" => $token,
    "expires_at" => $expires_at
]);
