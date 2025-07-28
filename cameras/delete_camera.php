<?php
// Incluindo dependências
require_once '../includes/db.php';
require_once '../includes/dao/CamerasDAO.php';
require_once '../includes/auth.php';

// Verifica se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado.']);
    exit;
}

$user_id = $_SESSION['user_id'];  // ID do usuário autenticado
$camerasDAO = new CamerasDAO($pdo);

// Verifica se o parâmetro 'id' foi enviado
if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $camera_id = intval($_POST['id']);

    // Verifica se a câmera existe e pertence ao usuário
    $camera = $camerasDAO->getCameraById($camera_id, $user_id);  // Verifica se a câmera pertence ao usuário

    if ($camera) {
        // Exclui a câmera
        if ($camerasDAO->deleteCamera($camera_id, $user_id)) {
            echo json_encode(['success' => true, 'message' => 'Câmera excluída com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir a câmera.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Câmera não encontrada ou você não tem permissão para excluir.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID inválido.']);
}
?>
