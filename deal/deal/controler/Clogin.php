<?php
require('../model/conexao.php');
session_start();

if(isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Preparar a consulta SQL
    $sql = "SELECT * FROM empresa WHERE email = :email AND senha = :senha";
    $stmt = $conexao->prepare($sql);

    // Executar a consulta
    $stmt->execute(array(':email' => $email, ':senha' => $senha));

    // Verificar se encontrou um usuário
    if($stmt->rowCount() == 1) {
        // Recuperar o ID do usuário
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id_usuario = $row['id']; // Supondo que o ID esteja na coluna 'id'

        // Definir as variáveis de sessão
        $_SESSION['id'] = $id_usuario;
        $_SESSION['email'] = $email;
        $_SESSION['senha'] = $senha;

        // Redirecionar para a página de perfil
        header('Location: ../view/Tperfil');
        exit(); // Certifique-se de sair após o redirecionamento
    } else {
        // Usuário não encontrado, redirecionar de volta para a página de login
        header('Location: ../view/Tlogin.html');
        exit(); // Certifique-se de sair após o redirecionamento
    }
} else {
    // Redirecionar de volta para a página inicial se não houver dados enviados
    header('Location: ../index.html');
    exit(); // Certifique-se de sair após o redirecionamento
}

?>
