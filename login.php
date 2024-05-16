<?php
    session_start();
if(isset($_POST['submit']) && !empty($_POST['usuario']) && !empty($_POST['senha']))
{
    include_once('conexao.php');
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];
    //TESTES
    //print_r('Email: ' . $usuario );
    //print_r('<br>');
    //print_r('Senha: ' . $senha );

    $sql = "SELECT * FROM USUARIOS WHERE email = '$usuario' AND senha = '$senha'" ;
    $result = $conexao->query($sql);
    //TESTES
    //print_r($result);
    //print_r($sql);

    if(mysqli_num_rows($result) < 1)
    {
        unset($_SESSION['usuario']);
        unset($_SESSION['senha']);
        header('Location: login.php');
    }
    else
    {
        $_SESSION['usuario'] = $usuario;
        $_SESSION['senha'] = $senha;
        header('Location: sistema.php');
    }
}
else
{
    header('Location: TelaLogin.php');
}

?>