<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

// ==== PEGAR TOKEN DO HEADER ====
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? null;

if (!$authHeader || !preg_match('/Bearer\s+(\S+)/', $authHeader, $matches)) {
    http_response_code(401);
    echo json_encode(["error" => "Token não enviado","data"=>$headers]);
    exit;
}

$token = $matches[1];

// ==== VALIDAR TOKEN NO BANCO ====
$sql = "SELECT user_id FROM user_tokens WHERE token = :token AND expires_at > NOW()";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':token', $token);
$stmt->execute();
$userSession = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userSession) {
    http_response_code(401);
    echo json_encode(["error" => "Token inválido ou expirado"]);
    exit;
}

$user_id = $userSession['user_id'];

// ==== VALIDAÇÃO DOS DADOS ====
$fk_vacancie = $_POST['fk_vacancie'] ?? null;
$fk_camera   = $_POST['fk_camera'] ?? null;
$state       = $_POST['state'] ?? null;
$placa       = $_POST['placa'] ?? null;

if (!$fk_vacancie || !$fk_camera || ($state !== "0" && $state !== "1")) {
    http_response_code(400);
    echo json_encode(["error" => "Parâmetros inválidos. Envie fk_vacancie, fk_camera e state (0 ou 1)."]);
    exit;
}

if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(["error" => "Nenhum arquivo enviado"]);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/moviments/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
$fileName = 'moviment_' . time() . '_' . uniqid() . '.' . $ext;
$filePath = $uploadDir . $fileName;
$fileUrl  = '/uploads/moviments/' . $fileName;

if (!move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao salvar arquivo"]);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];

try {
    // Sempre INSERIR novo movimento, mesmo que já exista
    $insertSql = "INSERT INTO moviment_vacancies 
                  (ip, fk_camera, fk_vacancie, created_at, state, file_path, placa) 
                  VALUES (:ip, :fk_camera, :fk_vacancie, NOW(), :state, :file_path, :placa)";

    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->bindParam(':ip', $ip);
    $insertStmt->bindParam(':fk_camera', $fk_camera);
    $insertStmt->bindParam(':fk_vacancie', $fk_vacancie);
    $insertStmt->bindParam(':state', $state);
    $insertStmt->bindParam(':file_path', $fileUrl);
    $insertStmt->bindParam(':placa', $placa);
    $insertStmt->execute();

    echo json_encode([
        "success" => true,
        "message" => "Movimentação registrada com sucesso",
        "user_id" => $user_id,
        "data" => [
            "fk_vacancie" => $fk_vacancie,
            "fk_camera" => $fk_camera,
            "state" => $state,
            "file_url" => $fileUrl,
            "placa" => $placa
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao salvar no banco: " . $e->getMessage()]);
}
