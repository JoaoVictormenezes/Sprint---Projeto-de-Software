<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
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
                    try {
                        // Verifica se o perfil já existe
                        $stmt = $conexao->prepare("SELECT COUNT(*) FROM perfil WHERE id = :id");
                        $stmt->bindParam(':id', $id_usuario);
                        $stmt->execute();
                        $perfil_existe = $stmt->fetchColumn();

                        if ($perfil_existe > 0) {
                            // Atualiza o perfil existente
                            $stmt = $conexao->prepare("UPDATE perfil SET nome = :nome, imagem = :imagem, bio = :bio, local = :local WHERE id = :id");
                            $stmt->bindParam(':id', $id_usuario);
                            $stmt->bindParam(':nome', $nome);
                            $stmt->bindParam(':imagem', $target_file);
                            $stmt->bindParam(':bio', $bio);
                            $stmt->bindParam(':local', $local);

                            if ($stmt->execute()) {
                                echo "Perfil atualizado com sucesso!";
                            } else {
                                echo "Erro ao atualizar os dados.";
                            }
                        } else {
                            // Insere um novo perfil
                            $stmt = $conexao->prepare("INSERT INTO perfil (id, nome, imagem, bio, local) VALUES (:id, :nome, :imagem, :bio, :local)");
                            $stmt->bindParam(':id', $id_usuario);
                            $stmt->bindParam(':nome', $nome);
                            $stmt->bindParam(':imagem', $target_file);
                            $stmt->bindParam(':bio', $bio);
                            $stmt->bindParam(':local', $local);

                            if ($stmt->execute()) {
                                echo "Perfil criado com sucesso!";
                            } else {
                                echo "Erro ao inserir os dados.";
                            }
                        }
                    } catch (PDOException $e) {
                        echo "Erro ao inserir ou atualizar os dados: " . $e->getMessage();
                    }
                }
            } else {
                echo "Erro ao mover o arquivo.";
            }
        } else {
            echo "O arquivo não é uma imagem.";
        }
    }
}
?>
