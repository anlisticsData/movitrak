<?php
require_once '../includes/db.php';
require_once '../includes/dao/MovimentVacanciesDAO.php';
header('Content-Type: application/json');



try {
    $lancamentos = [];
    if (isset($_POST['user'])) {
        $userId = $_POST['user'];
        $plate =  $_POST['plate'] ?? '';
        $movimentVacanciesDAO =  new MovimentVacanciesDAO($pdo);
        $lancamentosData = $movimentVacanciesDAO->getTodosMovimentosDoDiaAtual($plate, $userId);

        $lancamentos=[];
        foreach($lancamentosData as $key => $row){
            $lancamentos[]=[
                "dia"=>$row['dia'],
                "movimento_id"=>$row['movimento_id'],
                "fk_camera"=> $row['fk_camera'],
                "fk_vacancie"=> $row['fk_vacancie'],
                "state"=>$row['state'],
                "file_path"=> $row['file_path'],
                "placa"=>$row['placa'],
                "created_at"=> $row['created_at']
            ];
        }






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
