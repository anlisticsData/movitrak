<?php

class UsersDAO {

    // Função para buscar usuário por login
    public function getUserByLogin($login) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE login = :login");
            $stmt->execute(['login' => $login]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar usuário: " . $e->getMessage();
            return null;
        }
    }

    // Função para criar um novo usuário
    public function createUser($fk_companie, $name, $login, $password, $role, $link_site, $file_logo) {
        global $pdo;

        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (fk_companie, name, login, password, role, link_site, file_logo, state) 
                                   VALUES (:fk_companie, :name, :login, :password, :role, :link_site, :file_logo, 1)");
            $stmt->execute([
                'fk_companie' => $fk_companie,
                'name' => $name,
                'login' => $login,
                'password' => $hashedPassword,
                'role' => $role,
                'link_site' => $link_site,
                'file_logo' => $file_logo
            ]);

            if ($stmt->rowCount() > 0) {
                return $pdo->lastInsertId();
            } else {
                throw new Exception("Erro ao criar o usuário: Nenhuma linha afetada.");
            }

        } catch (PDOException $e) {
            echo "Erro ao criar usuário: " . $e->getMessage();
            return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    // Função para buscar usuário por ID
    public function getUserById($user_id) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Erro ao buscar usuário: " . $e->getMessage();
            return null;
        }
    }

    // Método para atualizar o nome do usuário
    public function updateUserName($user_id, $name) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET name = :name WHERE id = :user_id");
            $stmt->execute([
                'name' => $name,
                'user_id' => $user_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erro ao atualizar nome: " . $e->getMessage();
            return false;
        }
    }

    // Método para atualizar a senha do usuário
    public function updateUserPassword($user_id, $password) {
        global $pdo;
        
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
            $stmt->execute([
                'password' => $hashedPassword,
                'user_id' => $user_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erro ao atualizar senha: " . $e->getMessage();
            return false;
        }
    }

    // Método para atualizar o avatar do usuário
    public function updateUserAvatar($user_id, $file_logo) {
        global $pdo;
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET file_logo = :file_logo WHERE id = :user_id");
            $stmt->execute([
                'file_logo' => $file_logo,
                'user_id' => $user_id
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo "Erro ao atualizar avatar: " . $e->getMessage();
            return false;
        }
    }
}

