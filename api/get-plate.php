<?php
require_once '../includes/db.php';

header('Content-Type: application/json');



try {
    $lancamentos = [];
    if (isset($_POST['user'])) {
        $userId = $_POST['user'];
        $plate =  $_POST['plate'] ?? '';
        $lancamentos = $movimentVacanciesDAO->getLancamentosUnicosPorPlacaEUsuario($plate, $userId);
        echo json_encode([
            "success" => true,
            "data" =>    $lancamentos
        ]);
        exit;
    }
    echo json_encode([
        "success" => true,
        "data" =>    $lancamentos
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao salvar no banco: " . $e->getMessage()]);
}
