<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>

<?php require ("conexao.php"); ?>

    <h1> Login </h1>
    <form action="login.php" method="POST">
        <input type="text" name="usuario" placeholder="Usuario">
        <br><br>
        <input type="password" name="senha" placeholder="Senha">
        <br><br>
        <input type="submit" name="submit" value="Enviar">
    </form> 

</body>
</html>