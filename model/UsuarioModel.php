<?php
class UsuarioModel {
    private $conexao;

    // Construtor para inicializar a conexão
    public function __construct($conexao) {
        $this->conexao = $conexao;
    }

    // Método para verificar o usuário
    public function verificarUsuario($email, $senha) {
        // Consulta SQL para verificar o usuário
        $query = "SELECT * FROM empresa WHERE email = :email";

        // Preparar a consulta
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Buscar o usuário
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar se o usuário foi encontrado e se a senha está correta
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario; // Retornar os dados do usuário
        } else {
            return null; // Retornar nulo se as credenciais forem inválidas
        }
    }
}
?>
