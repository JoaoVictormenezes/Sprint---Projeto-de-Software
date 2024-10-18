<?php
// model/EmpresaModel.php

require('conexao.php');

class EmpresaModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function inserirEmpresa($nome, $senha, $email, $cnpj, $porte, $tipo) {
        // Verifica se o CNPJ já está cadastrado
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM empresa WHERE cnpj = :cnpj");
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->execute();
        $numRows = $stmt->fetchColumn();

        if ($numRows > 0) {
            return false; // CNPJ já cadastrado
        }

        // Insere a nova empresa
        $sql = "INSERT INTO empresa (nome, email, senha, cnpj, porte, tipo) VALUES (:nome, :email, :senha, :cnpj, :porte, :tipo)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->bindParam(':porte', $porte, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}
?>
