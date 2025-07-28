<?php
class CompaniesDAO {

    public function getCompanyByName($name) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM companies WHERE name = :name");
        $stmt->execute(['name' => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createCompany($name) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO companies (name, isactive) VALUES (:name, 1)");
        $stmt->execute(['name' => $name]);
        return $pdo->lastInsertId();
    }
}
?>
