<?php

class VagasDAO
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Criar uma nova vaga
    public function addVaga($name, $fk_camera, $settings, $setting_areas, $state, $plate, $plate_file, $active)
    {
        $sql = "INSERT INTO vacancies (name, fk_camera, settings, setting_areas, state, plate, plate_file, created_at, update_at, active) 
                VALUES (:name, :fk_camera, :settings, :setting_areas, :state, :plate, :plate_file, NOW(), NOW(), :active)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':fk_camera', $fk_camera);
        $stmt->bindParam(':settings', $settings);
        $stmt->bindParam(':setting_areas', $setting_areas);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':plate', $plate);
        $stmt->bindParam(':plate_file', $plate_file);
        $stmt->bindParam(':active', $active);

        return $stmt->execute();
    }
    // Método para obter as vagas filtradas pelo camera_id


    public function getVagasByCamera($user_id, $filter = '', $camera_id = null) {
        // A consulta agora faz a junção correta utilizando fk_user
        $sql = "SELECT v.* FROM vacancies v
                INNER JOIN cameras c ON v.fk_camera = c.id
                WHERE c.fk_user = :user_id 
                AND (v.name LIKE :filter OR v.settings LIKE :filter)";
    
        // Filtro por camera_id, se fornecido
        if ($camera_id !== null) {
            $sql .= " AND v.fk_camera = :camera_id"; // Relacionamento com a FK 'fk_camera'
        }
    
        // Preparando a consulta
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':filter', '%' . $filter . '%', PDO::PARAM_STR);
    
        // Adicionando o filtro camera_id, caso exista
        if ($camera_id !== null) {
            $stmt->bindValue(':camera_id', $camera_id, PDO::PARAM_INT);
        }
    
        // Executando a consulta
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    




    // Obter uma vaga por ID
    public function getVagaById($vaga_id)
    {
        $sql = "SELECT * FROM vacancies WHERE id = :vaga_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':vaga_id', $vaga_id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Atualizar uma vaga
    public function updateVaga($id, $name, $settings, $setting_areas, $state, $plate, $plate_file, $active)
    {
        $sql = "UPDATE vacancies SET 
                    name = :name,
                    settings = :settings,
                    setting_areas = :setting_areas,
                    state = :state,
                    plate = :plate,
                    plate_file = :plate_file,
                    update_at = NOW(),
                    active = :active
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':settings', $settings);
        $stmt->bindParam(':setting_areas', $setting_areas);
        $stmt->bindParam(':state', $state);
        $stmt->bindParam(':plate', $plate);
        $stmt->bindParam(':plate_file', $plate_file);
        $stmt->bindParam(':active', $active);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    // Excluir uma vaga
    public function deleteVaga($vaga_id)
    {
        $sql = "DELETE FROM vacancies WHERE id = :vaga_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':vaga_id', $vaga_id);
        return $stmt->execute();
    }

    // Contar o total de vagas de uma câmera
    public function countVagasByCamera($camera_id)
    {
        $sql = "SELECT COUNT(*) FROM vacancies WHERE fk_camera = :camera_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':camera_id', $camera_id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    // Obter todas as câmeras associadas ao usuário
    public function getAllCameras($user_id)
    {
        // SQL para buscar todas as câmeras associadas ao usuário
        $sql = "SELECT * FROM cameras WHERE user_id = :user_id"; // Supondo que exista a coluna user_id na tabela cameras
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todas as câmeras
    }
}
