<?php
// controller/EmpresaController.php

require('../model/EmpresaModel.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $cnpj = $_POST["cnpj"];
    $porte = $_POST['porte'];
    $tipo = $_POST['tipo'];

    // Cria uma instância do model e tenta inserir os dados
    $empresaModel = new EmpresaModel($conexao);

    if ($empresaModel->inserirEmpresa($nome, $senha, $email, $cnpj, $porte, $tipo)) {
        echo "Dados inseridos com sucesso!";
        header('Location: ../view/Tlogin.html');
        exit;
    } else {
        echo "Erro ao inserir dados ou CNPJ já cadastrado!";
    }
}
?>
