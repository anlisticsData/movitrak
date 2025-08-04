<?php

use PDO;

class MovimentVacanciesDAO
{
    private $pdo;

    // Construtor que recebe a conexão PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }



   public function getTodosMovimentosDoDiaAtual($placa, $userId)
{


    
    $sql = "
        SELECT
            DATE(m.created_at) AS dia,
            m.id AS movimento_id,
            m.fk_camera,
            m.fk_vacancie,
            m.state,
            m.file_path,
            m.placa,
            m.created_at
        FROM moviment_vacancies m
        INNER JOIN cameras c ON m.fk_camera = c.id
        WHERE m.placa like  :placa
          AND c.fk_user = :user_id
        ORDER BY m.created_at DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':placa',$placa.'%', PDO::PARAM_STR);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}





    // Método para adicionar um movimento (inserir um registro)
    public function insertMoviment($ip, $fk_camera, $fk_vacancie, $file_path)
    {
        $sql = "INSERT INTO moviment_vacancies (ip, fk_camera, fk_vacancie, created_at, state, file_path) 
                VALUES (:ip, :fk_camera, :fk_vacancie, NOW(), 1, :file_path)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':ip', $ip);
        $stmt->bindParam(':fk_camera', $fk_camera);
        $stmt->bindParam(':fk_vacancie', $fk_vacancie);
        $stmt->bindParam(':file_path', $file_path);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Erro ao inserir movimento: " . $e->getMessage();
            return false;
        }
    }





public function getMovimentosUltimosDiasPorUsuario($userId, $dias = 7)
{
    $sql = "
        SELECT 
            m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, 
            m.file_path, m.placa,  -- <-- Aqui incluímos a placa
            v.name AS vaga_name, 
            c.name AS camera_name
        FROM moviment_vacancies m
        LEFT JOIN cameras c ON m.fk_camera = c.id
        LEFT JOIN vacancies v ON m.fk_vacancie = v.id
        WHERE m.created_at >= CURDATE() - INTERVAL :dias DAY
          AND c.fk_user = :user_id
        ORDER BY m.created_at DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // Método para buscar movimentos dos últimos N dias
    public function getMovimentosUltimosDias($dias = 7)
    {
        $sql = "SELECT m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, m.file_path, 
                       v.name as vaga_name, c.name as camera_name 
                FROM moviment_vacancies m
                LEFT JOIN cameras c ON m.fk_camera = c.id
                LEFT JOIN vacancies v ON m.fk_vacancie = v.id
                WHERE m.created_at >= CURDATE() - INTERVAL :dias DAY
                ORDER BY m.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':dias', $dias, PDO::PARAM_INT);
        $stmt->execute();
        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $movements;
    }

    // Método para buscar movimentos recentes (últimos 5 movimentos)
    public function getMovimentosRecentes()
    {
        $sql = "
            SELECT m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, m.file_path,
                   v.name AS vaga_name, c.name AS camera_name, m.placa
            FROM moviment_vacancies m
            INNER JOIN (
                SELECT fk_vacancie, MAX(created_at) AS max_created
                FROM moviment_vacancies
                GROUP BY fk_vacancie
            ) ult ON m.fk_vacancie = ult.fk_vacancie AND m.created_at = ult.max_created
            LEFT JOIN cameras c ON m.fk_camera = c.id
            LEFT JOIN vacancies v ON m.fk_vacancie = v.id
            ORDER BY m.created_at DESC
            LIMIT 5
        ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getMovimentosRecentesPorUsuario($userId)
    {
        $sql = "
       SELECT 
            m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, m.file_path,
            v.name AS vaga_name, c.name AS camera_name, m.placa
        FROM moviment_vacancies m
        LEFT JOIN cameras c ON m.fk_camera = c.id
        LEFT JOIN vacancies v ON m.fk_vacancie = v.id
        INNER JOIN (
            SELECT m2.fk_vacancie, MAX(m2.created_at) AS max_created
            FROM moviment_vacancies m2
            INNER JOIN cameras c2 ON m2.fk_camera = c2.id
            WHERE c2.fk_user = :user_id 
            GROUP BY m2.fk_vacancie
        ) ult ON m.fk_vacancie = ult.fk_vacancie AND m.created_at = ult.max_created
        WHERE c.fk_user = :user_id 
        ORDER BY m.created_at DESC
        LIMIT 5
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




    public function getResumoMovimentosDoDiaPorUsuario($userId)
    {
        $sql = "
        SELECT 
            v.id AS vaga_id,
            v.name AS vaga_name,
            c.name AS camera_name,
            COUNT(m.id) AS total_movimentos,
            (
                SELECT m2.placa 
                FROM moviment_vacancies m2
                WHERE m2.fk_vacancie = v.id 
                  AND DATE(m2.created_at) = CURDATE()
                ORDER BY m2.created_at DESC 
                LIMIT 1
            ) AS ultima_placa,
            (
                SELECT m2.created_at
                FROM moviment_vacancies m2
                WHERE m2.fk_vacancie = v.id 
                  AND DATE(m2.created_at) = CURDATE()
                ORDER BY m2.created_at DESC 
                LIMIT 1
            ) AS ultima_data
        FROM vacancies v
        LEFT JOIN cameras c ON v.fk_camera = c.id
        LEFT JOIN moviment_vacancies m 
            ON v.id = m.fk_vacancie 
           AND DATE(m.created_at) = CURDATE()
        WHERE c.fk_user = :user_id
        GROUP BY v.id, v.name, c.name
        ORDER BY ultima_data DESC
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public function getResumoMovimentosDoDia()
    {
        $sql = "
        SELECT 
            v.id AS vaga_id,
            v.name AS vaga_name,
            c.name AS camera_name,
            COUNT(m.id) AS total_movimentos,
            -- Última placa registrada
            (
                SELECT m2.placa 
                FROM moviment_vacancies m2
                WHERE m2.fk_vacancie = v.id 
                  AND DATE(m2.created_at) = CURDATE()
                ORDER BY m2.created_at DESC 
                LIMIT 1
            ) AS ultima_placa,
            -- Última data/hora do movimento
            (
                SELECT m2.created_at
                FROM moviment_vacancies m2
                WHERE m2.fk_vacancie = v.id 
                  AND DATE(m2.created_at) = CURDATE()
                ORDER BY m2.created_at DESC 
                LIMIT 1
            ) AS ultima_data
        FROM vacancies v
        LEFT JOIN moviment_vacancies m 
            ON v.id = m.fk_vacancie 
           AND DATE(m.created_at) = CURDATE()
        LEFT JOIN cameras c 
            ON v.fk_camera = c.id
        GROUP BY v.id, v.name, c.name
        ORDER BY ultima_data DESC
    ";

        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    // Método para buscar movimentos do dia
    public function getMovimentosDoDia()
    {
        $sql = "SELECT m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, m.file_path, 
                       v.name as vaga_name, c.name as camera_name , m.placa
                FROM moviment_vacancies m
                LEFT JOIN cameras c ON m.fk_camera = c.id
                LEFT JOIN vacancies v ON m.fk_vacancie = v.id
                WHERE DATE(m.created_at) = CURDATE()
                ORDER BY m.created_at DESC";

        $stmt = $this->pdo->query($sql);
        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $movements;
    }

    // Método para buscar movimentos por vaga
    public function getMovementsByVacancy($fk_vacancie)
    {
        $sql = "SELECT m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, m.file_path 
                FROM moviment_vacancies m
                WHERE m.fk_vacancie = :fk_vacancie
                ORDER BY m.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':fk_vacancie', $fk_vacancie);
        $stmt->execute();
        $movements = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $movements;
    }

    // Método para buscar um movimento específico por ID
    public function getMovimentById($id)
    {
        $sql = "SELECT m.id, m.fk_camera, m.fk_vacancie, m.created_at, m.state, m.file_path,
                       v.name as vaga_name, c.name as camera_name
                FROM moviment_vacancies m
                LEFT JOIN cameras c ON m.fk_camera = c.id
                LEFT JOIN vacancies v ON m.fk_vacancie = v.id
                WHERE m.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $moviment = $stmt->fetch(PDO::FETCH_ASSOC);

        return $moviment;
    }

    // Método para atualizar o estado de um movimento
    public function updateMovimentState($id, $state)
    {
        $sql = "UPDATE moviment_vacancies SET state = :state WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Erro ao atualizar estado do movimento: " . $e->getMessage();
            return false;
        }
    }

    // Método para excluir um movimento
    public function deleteMoviment($id)
    {
        $sql = "DELETE FROM moviment_vacancies WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Erro ao excluir movimento: " . $e->getMessage();
            return false;
        }
    }




    public function getVagasStatusPorUsuario($userId)
    {
        // Vagas ocupadas (state = 1)
        $sqlOcupadas = "
        SELECT COUNT(DISTINCT m.fk_vacancie) AS ocupadas
        FROM moviment_vacancies m
        INNER JOIN vacancies v ON m.fk_vacancie = v.id
        INNER JOIN cameras c ON v.fk_camera = c.id
        WHERE m.state = 1 AND c.fk_user = :user_id
    ";
        $stmt = $this->pdo->prepare($sqlOcupadas);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $ocupadas = (int) $stmt->fetch(PDO::FETCH_ASSOC)['ocupadas'];

        // Vagas livres (state = 0)
        $sqlLivres = "
        SELECT COUNT(DISTINCT m.fk_vacancie) AS livres
        FROM moviment_vacancies m
        INNER JOIN vacancies v ON m.fk_vacancie = v.id
        INNER JOIN cameras c ON v.fk_camera = c.id
        WHERE m.state = 0 AND c.fk_user = :user_id
    ";
        $stmt = $this->pdo->prepare($sqlLivres);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $livres = (int) $stmt->fetch(PDO::FETCH_ASSOC)['livres'];

        return ['ocupadas' => $ocupadas, 'livres' => $livres];
    }




    public function getVagasStatus()
    {
        // Vagas ocupadas (onde state = 1, por exemplo, representa vaga ocupada)
        $query = "SELECT COUNT(DISTINCT fk_vacancie) AS ocupadas 
                  FROM moviment_vacancies 
                  WHERE state = 1"; // state = 1 indica vaga ocupada
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $ocupadas = $stmt->fetch(PDO::FETCH_ASSOC)['ocupadas'];

        // Vagas livres (onde state = 0 representa vaga livre)
        $query = "SELECT COUNT(DISTINCT fk_vacancie) AS livres 
                  FROM moviment_vacancies 
                  WHERE state = 0"; // state = 0 indica vaga livre
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $livres = $stmt->fetch(PDO::FETCH_ASSOC)['livres'];

        return ['ocupadas' => $ocupadas, 'livres' => $livres];
    }


    public function getVagaMaiorTempoPorUsuario($userId)
    {
        $sql = "
        SELECT 
            m.fk_vacancie, 
            TIMESTAMPDIFF(HOUR, m.created_at, NOW()) AS tempo_permanencia
        FROM moviment_vacancies m
        INNER JOIN vacancies v ON m.fk_vacancie = v.id
        INNER JOIN cameras c ON v.fk_camera = c.id
        WHERE m.state = 1
          AND c.fk_user = :user_id
        ORDER BY tempo_permanencia DESC
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $vaga = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vaga ? $vaga : null;
    }



    public function getVagaMaiorTempo()
    {
        // Consulta SQL para encontrar a vaga com o maior tempo de permanência
        $sql = "SELECT fk_vacancie, TIMESTAMPDIFF(HOUR, created_at, NOW()) AS tempo_permanencia
                FROM moviment_vacancies
                WHERE state = 1  -- Considera apenas as vagas ocupadas
                ORDER BY tempo_permanencia DESC
                LIMIT 1";  // Retorna a vaga com o maior tempo de permanência

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        // Recupera o resultado
        $vaga = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se uma vaga foi encontrada
        if ($vaga) {
            return $vaga; // Retorna o array com dados da vaga
        }

        // Caso contrário, retorna null para indicar que não há vagas ocupadas
        return null;
    }



    public function getVagaMaiorFrequenciaPorUsuario($userId)
    {
        $sql = "
        SELECT 
            m.fk_vacancie, 
            COUNT(*) AS frequencia
        FROM moviment_vacancies m
        INNER JOIN vacancies v ON m.fk_vacancie = v.id
        INNER JOIN cameras c ON v.fk_camera = c.id
        WHERE c.fk_user = :user_id
        GROUP BY m.fk_vacancie
        ORDER BY frequencia DESC
        LIMIT 1
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $vaga = $stmt->fetch(PDO::FETCH_ASSOC);
        return $vaga ? $vaga : null;
    }



    public function getVagaMaiorFrequencia()
    {
        // Consulta SQL para buscar a vaga com maior frequência de movimentações
        $sql = "SELECT fk_vacancie, COUNT(*) AS frequencia
                FROM moviment_vacancies
                GROUP BY fk_vacancie
                ORDER BY frequencia DESC LIMIT 1";

        // Executa a consulta
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retorna o resultado da consulta
    }


public function getMovimentosPorVaga($vagaId, $limit = 10, $offset = 0)
{
    $sql = "
        SELECT 
            m.id, m.created_at, m.state, m.file_path, m.placa,
            c.name AS camera_name
        FROM moviment_vacancies m
        INNER JOIN (
            SELECT MAX(id) AS latest_id
            FROM moviment_vacancies
            WHERE fk_vacancie = :vaga_id
            GROUP BY placa, DATE(created_at)
        ) latest ON m.id = latest.latest_id
        LEFT JOIN cameras c ON m.fk_camera = c.id
        ORDER BY m.created_at DESC
        LIMIT :limit 
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':vaga_id', $vagaId, PDO::PARAM_INT);
    $stmt->bindValue(':limit',$limit , PDO::PARAM_INT);
   
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function countMovimentosPorVaga($vagaId)
{
    $sql = "
        SELECT COUNT(*) FROM (
            SELECT DATE(created_at) AS data_unica, placa
            FROM moviment_vacancies
            WHERE fk_vacancie = :vaga_id
            GROUP BY placa, DATE(created_at)
        ) AS dias_placas
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':vaga_id', $vagaId, PDO::PARAM_INT);
    $stmt->execute();
    return (int)$stmt->fetchColumn();
}


}
