<?php
require_once '../includes/db.php';

class CamerasDAO
{
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para listar as câmeras com paginação e filtro
    public function getCameras($user_id, $page = 1, $limit = 10, $filter = '')
    {
        // Calcula o offset para a paginação
        $offset = ($page - 1) * $limit;

        $sql = "SELECT * FROM cameras WHERE fk_user = :user_id AND (name LIKE :filter OR conexao LIKE :filter) LIMIT :offset, :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para contar o número total de câmeras para paginação
    public function countCameras($user_id, $filter = '')
    {
        $sql = "SELECT COUNT(*) FROM cameras WHERE fk_user = :user_id AND (name LIKE :filter OR conexao LIKE :filter)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':filter', "%$filter%", PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // Método para adicionar uma nova câmera
    public function addCamera($user_id, $name, $conexao_type, $conexao)
    {
        $sql = "INSERT INTO cameras (name, conexao_type, conexao, fk_user, state) VALUES (:name, :conexao_type, :conexao, :user_id, 1)";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':conexao_type', $conexao_type);
        $stmt->bindValue(':conexao', $conexao);
        $stmt->bindValue(':user_id', $user_id);

        return $stmt->execute();
    }

    // Método para editar uma câmera
    public function updateCamera($id, $name, $conexao_type, $conexao)
    {

      
        $sql = "UPDATE cameras SET name = :name, conexao_type = :conexao_type, conexao = :conexao WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':conexao_type', $conexao_type);
        $stmt->bindValue(':conexao', $conexao);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Método para excluir uma câmera
    public function deleteCamera($id)
    {
        $sql = "DELETE FROM cameras WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    // Método para obter uma câmera por ID
    public function getCameraById($id)
    {
        $sql = "SELECT * FROM cameras WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obter todas as câmeras associadas ao usuário
    public function getAllCameras($user_id)
    {
        // SQL para buscar todas as câmeras associadas ao usuário
        $sql = "SELECT * FROM cameras WHERE fk_user = :user_id"; // Ajustado para fk_user
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Retorna todas as câmeras
    }
}
