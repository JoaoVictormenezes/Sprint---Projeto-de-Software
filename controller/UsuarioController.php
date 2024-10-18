<?php
session_start();
include '../model/conexao.php';

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];  // Obtenha o email do POST
    $senha = $_POST['senha'];  // Obtenha a senha do POST

    try {
        // Consulta para verificar o usuário
        $query = "SELECT * FROM empresa WHERE email = :email LIMIT 1";
        $stmt = $conexao->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($usuario); // Para ver o que foi retornado do banco de dados
        

        // Verifique se o usuário existe
        if (!$usuario) {
            echo "Usuário não encontrado.";
        } else {
            // Para verificar a senha
            if ($senha === $usuario['senha']) { // Altere aqui se você estiver usando hash
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['senha'] = $usuario['senha'];
                header('Location: ../view/Tperfil.php'); // Redirecionar para Tperfil.php
                exit();
            } else {
                echo "Senha incorreta.";
            }
        }
    } catch (PDOException $e) {
        die("Erro ao executar a consulta: " . $e->getMessage());
    }
} else {
    echo "Método de requisição inválido.";
}
?>
