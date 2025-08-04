<?php
require_once '../includes/db.php';
require_once '../includes/dao/MovimentVacanciesDAO.php';
header('Content-Type: application/json');



/**
 * Extrai uma placa no padrão Mercosul (ex: ABC1D23) de uma string.
 *
 * @param string $string A string de onde extrair a placa.
 * @return string|null A placa extraída ou null se não for encontrada.
 */
function extrairPlacaMercosul(string $string): ?string
{
    if (preg_match('/[A-Z]{3}[0-9][A-Z0-9][0-9]{2}/', $string, $matches)) {
        return $matches[0];
    }

    return "desconhecia";
}




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
                "placa"=>extrairPlacaMercosul($row['placa']),
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
