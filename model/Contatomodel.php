<?php
class ContatoModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function inserirContato($nome, $email, $mensagem, $data_envio, $hora_envio) {
        $sql = "INSERT INTO contato (nome, email, mensagem, data_envio, hora_envio) 
                VALUES (:nome, :email, :mensagem, :data_envio, :hora_envio)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':mensagem', $mensagem, PDO::PARAM_STR);
        $stmt->bindParam(':data_envio', $data_envio, PDO::PARAM_STR);
        $stmt->bindParam(':hora_envio', $hora_envio, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
?>
