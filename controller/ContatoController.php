<?php
require('../model/conexao.php');
require('../model/ContatoModel.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $mensagem = $_POST['mensagem'];
    $data_envio = date('d/m/Y');
    $hora_envio = date('H:i:s');

    $contatoModel = new ContatoModel($conexao);

    if ($contatoModel->inserirContato($nome, $email, $mensagem, $data_envio, $hora_envio)) {
        echo "Dados inseridos com sucesso!";
    } else {
        echo "Erro ao inserir dados.";
    }

    // Enviar e-mail
    $arquivo = "
        <html>
            <p><b>Nome: </b>$nome</p>
            <p><b>E-mail: </b>$email</p>
            <p><b>Mensagem: </b>$mensagem</p>
            <p>Este e-mail foi enviado em <b>$data_envio</b> às <b>$hora_envio</b></p>
        </html>
    ";

    // Destino e assunto
    $destino = "12200328@aluno.cotemig.com.br";
    $assunto = "Contato pelo Site";

    // Cabeçalhos para garantir a exibição correta dos caracteres
    $headers  = "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\n";
    $headers .= "From: $nome <$email>";

    // Enviar o e-mail
    mail($destino, $assunto, $arquivo, $headers);

    // Redirecionar para a página de contato
    echo "<meta http-equiv='refresh' content='10;URL=../view/contato.html'>";
}
?>
