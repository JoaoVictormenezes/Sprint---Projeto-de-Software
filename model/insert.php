<?php
require('conexao.php');

if($_SERVER["REQUEST_METHOD"]== "POST"){
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $cnpj = $_POST["cnpj"];

    function InserirEmpresa($pdo, $nome, $senha, $email, $cnpj){
        // Verifica se o CNPJ já existe no banco de dados
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM empresa WHERE cnpj = :cnpj");
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        $stmt->execute();
        $numRows = $stmt->fetchColumn();

        // Se o CNPJ já existe, não permite a inserção
        if($numRows > 0) {
            return false;
        }

        // Caso contrário, insere o novo registro
        $sql = "INSERT INTO empresa (nome, email, senha, cnpj) VALUES (:nome, :email, :senha, :cnpj)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':senha', $senha, PDO::PARAM_STR);
        $stmt->bindParam(':cnpj', $cnpj, PDO::PARAM_STR);
        return $stmt->execute();

        
    }

    if(InserirEmpresa($conexao, $nome, $senha, $email, $cnpj)){
        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados ou CNPJ já cadastrado!";
    }
}
?>
