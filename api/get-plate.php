<?php
require_once '../includes/db.php';
require_once '../includes/dao/MovimentVacanciesDAO.php';
header('Content-Type: application/json');

/**
 * Extrai uma placa no padrão Mercosul (ex: ABC1D23) de uma string.
 *
 * @param string $string A string de onde extrair a placa.
 * @return string A placa extraída ou "desconhecida" se não for encontrada.
 */
function extrairPlacaMercosul(string $string): string
{
    if (preg_match('/[A-Z]{3}[0-9][A-Z0-9][0-9]{2}/', strtoupper($string), $matches)) {
        return $matches[0];
    }

    return "desconhecida";
}

try {
    $lancamentos = [];

    if (isset($_POST['user'])) {
        $userId = $_POST['user'];
        $plate = $_POST['plate'] ?? '';

        $movimentVacanciesDAO = new MovimentVacanciesDAO($pdo);
        $lancamentosData = $movimentVacanciesDAO->getTodosMovimentosDoDiaAtual($plate, $userId);

        $placasAgrupadas = [];

        foreach ($lancamentosData as $row) {
            $placa = extrairPlacaMercosul($row['placa']);
            $hora = date('H:i:s', strtotime($row['created_at']));

            // Se ainda não existe, inicia o grupo
            if (!isset($placasAgrupadas[$placa])) {
                $placasAgrupadas[$placa] = [
                    "placa" => $placa,
                    "dia" => $row['dia'],
                    "primeira_hora" => $hora,
                    "ultima_hora" => $hora,
                    "movimentos" => [],
                ];
            }

            // Atualiza menor e maior hora
            if ($hora < $placasAgrupadas[$placa]['primeira_hora']) {
                $placasAgrupadas[$placa]['primeira_hora'] = $hora;
            }

            if ($hora > $placasAgrupadas[$placa]['ultima_hora']) {
                $placasAgrupadas[$placa]['ultima_hora'] = $hora;
            }

            // Adiciona movimento (temporariamente, sem as horas ainda)
            $placasAgrupadas[$placa]['movimentos'][] = [
                "movimento_id" => $row['movimento_id'],
                "fk_camera" => $row['fk_camera'],
                "fk_vacancie" => $row['fk_vacancie'],
                "state" => $row['state'],
                "file_path" => $row['file_path'],
                "created_at" => $row['created_at'],
                "hora" => $hora // armazenar hora separada para fácil visualização
            ];
        }

        // Agora adicionamos a menor e maior hora dentro de cada movimento
        foreach ($placasAgrupadas as &$grupo) {
            foreach ($grupo['movimentos'] as &$mov) {
                $mov['primeira_hora'] = $grupo['primeira_hora'];
                $mov['ultima_hora'] = $grupo['ultima_hora'];
            }
        }

        // Reorganiza para retorno sem chaves associativas
        $lancamentos = array_values($placasAgrupadas);

        echo json_encode([
            "success" => true,
            "data" => $lancamentos
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "data" => $lancamentos
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro ao salvar no banco: " . $e->getMessage()]);
}
