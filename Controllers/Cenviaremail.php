<?php
  //Variáveis
  if($_SERVER["REQUEST_METHOD"] == "POST"){
  $nome = $_POST['nome'];
  $email = $_POST['email'];
  $mensagem = $_POST['mensagem'];
  $data_envio = date('d/m/Y');
  $hora_envio = date('H:i:s');

  function InserirContato($pdo, $nome, $email, $mensagem, $data_envio,$hora_envio){

    // Caso contrário, insere o novo registro
    $sql = "INSERT INTO ? (nome, email, mensagem, data_envio, hora_envio) VALUES (:nome, :email, :mensagem ,:data_envio, :hora_envio)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':mensagem', $mensagem, PDO::PARAM_STR);
    $stmt->bindParam(':data_envio', $data_envio, PDO::PARAM_STR);
    $stmt->bindParam(':hora_envio', $hora_envio, PDO::PARAM_STR);
    return $stmt->execute();
}

if(InserirEmpresa($conexao, $nome, $email, $mensagem, $data_envio, $hora_envio)){
    echo "Dados inseridos com sucesso!";
} else {
    echo "Erro ao inserir dados";
}
}

  //Compo E-mail
  $arquivo = "
    <html>
      <p><b>Nome: </b>$nome</p>
      <p><b>E-mail: </b>$email</p>
      <p><b>Mensagem: </b>$mensagem</p>
      <p>Este e-mail foi enviado em <b>$data_envio</b> às <b>$hora_envio</b></p>
    </html>
  ";
  
  //Emails para quem será enviado o formulário
  $destino = "12200328@aluno.cotemig.com.br";
  $assunto = "Contato pelo Site";

  //Este sempre deverá existir para garantir a exibição correta dos caracteres
  $headers  = "MIME-Version: 1.0\n";
  $headers .= "Content-type: text/html; charset=iso-8859-1\n";
  $headers .= "From: $nome <$email>";

  //Enviar
  mail($destino, $assunto, $arquivo, $headers);
  
  echo "<meta http-equiv='refresh' content='10;URL=../contato.html'>";

?>