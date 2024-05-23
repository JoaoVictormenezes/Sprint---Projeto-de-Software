<?php
session_start();
if(!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    header('Location: ../login/login.html');
    exit();
}

// Inclui o arquivo de configuração do banco de dados
require_once("conexao.php");

// Verifica se o formulário foi submetido via método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se um arquivo de imagem foi enviado
    if (isset($_FILES["pfpic"])) {
        // Verifica se o arquivo é uma imagem
        $check = getimagesize($_FILES["pfpic"]["tmp_name"]);
        if ($check !== false) {
            // Recupera os valores do formulário
            $nome = $_POST["nome"];
            $bio = $_POST["bio"];
            $local = $_POST["local"];
            
            // Diretório de upload
            $target_dir = "../uploads/";
            $target_file = $target_dir . basename($_FILES["pfpic"]["name"]);
            
            // Move o arquivo para o diretório de upload
            if (move_uploaded_file($_FILES["pfpic"]["tmp_name"], $target_file)) {
                // Obtém o ID do usuário logado
                $id_usuario = isset($_SESSION['id']) ? $_SESSION['id'] : null;
                
                if ($id_usuario !== null) {
                    // Insere os dados no banco de dados com o ID do usuário
                    try {
                        // Prepara a consulta SQL para inserção de perfil
                        $stmt = $conexao->prepare("INSERT INTO perfil (id, nome, imagem, bio, local) VALUES (:id, :nome, :imagem, :bio, :local)");
                        $stmt->bindParam(':id', $id_usuario);
                        $stmt->bindParam(':nome', $nome);
                        $stmt->bindParam(':imagem', $target_file);
                        $stmt->bindParam(':bio', $bio);
                        $stmt->bindParam(':local', $local);
                        
                        // Executa a consulta
                        if ($stmt->execute()) {
                            echo "Perfil criado com sucesso!";
                        } else {
                            echo "Erro ao inserir os dados.";
                        }
                    } catch (PDOException $e) {
                        echo "Erro ao inserir os dados: " . $e->getMessage();
                    }
                } else {
                    echo "ID do usuário não encontrado na sessão.";
                }
            } else {
                echo "Erro ao fazer upload do arquivo.";
            }
        } else {
            echo "O arquivo enviado não é uma imagem.";
        }
    } else {
        echo "Nenhum arquivo foi enviado.";
    }
}
?>
