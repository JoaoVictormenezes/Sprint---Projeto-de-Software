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

    <h1> Formulário de Contato </h1>
    <form action="../controler/enviar-email.php" method="POST">
        <input type="text" name="nome" placeholder="Nome do Contactante" id="nome">
        <br><br>
        <input type="text" name="email" placeholder="Email do Contactante" id="email">
        <br><br>
        <input type="text" name="mensagem" placeholder="Mensagem" id="mensagem">
        <br><br>
        <input type="submit" name="submit" value="Enviar">
        <button type="button" onclick="limparCampos()">Limpar</button>
        <a href="../index.html" class="btn btn-default">Voltar</a>
    </form> 

    <script>
        function limparCampos() {
            document.getElementById('nome').value = '';
            document.getElementById('email').value = '';
            document.getElementById('mensagem').value = '';
        }
    </script>

</body>
</html>
