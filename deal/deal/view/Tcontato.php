<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela Contato</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>

<?php require ("../model/conexao.php"); ?>

    <h1> Formulario de contato </h1>
    <form action="../controler/enviar-email.php" method="POST">
        <input type="text" name="nome" placeholder="Nome do Contactante">
        <br><br>
        <input type="text" name="email" placeholder="Email do Contactante">
        <br><br>
        <input type="text" name="mensagem" placeholder="Mensagem">
        <input type="submit" name="submit" value="Enviar">
    </form> 

</body>
</html>