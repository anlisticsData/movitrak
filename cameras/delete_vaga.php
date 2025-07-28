<?php
 

// Verificar sessão
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sessão expirada. Faça login novamente.']);
    exit;
}

// Conectar ao banco
require_once '../includes/db.php';
require_once '../includes/dao/VagasDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $vagaId = intval($_POST['id']);
    $vagaDAO = new VagasDAO($pdo);

    $deleted = $vagaDAO->deleteVaga($vagaId);

    if ($deleted) {
        echo json_encode(['success' => true, 'message' => 'Vaga excluída com sucesso!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir a vaga.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
