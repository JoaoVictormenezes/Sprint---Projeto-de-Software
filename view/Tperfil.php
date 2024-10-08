<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    header('Location: ../view/Tlogin.html');
    exit();
}

$logado = $_SESSION['email'];

// Inclua o arquivo de conexão
include '../model/conexao.php';

try {
    // Processar a remoção de perfis salvos
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_perfil_id'])) {
        $perfil_id = $_POST['remove_perfil_id'];
        
        $queryRemover = "DELETE FROM perfis_salvos WHERE user_email = :logado AND perfil_id = :perfil_id";
        $stmtRemover = $conexao->prepare($queryRemover);
        $stmtRemover->bindParam(':logado', $logado, PDO::PARAM_STR);
        $stmtRemover->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
        $stmtRemover->execute();
    }

    // Deletar a conta do usuário logado
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
        $queryDeletar = "DELETE FROM empresa WHERE email = :logado";
        $stmtDeletar = $conexao->prepare($queryDeletar);
        $stmtDeletar->bindParam(':logado', $logado, PDO::PARAM_STR);
        $stmtDeletar->execute();

        session_destroy();
        header('Location: ../index.html');
        exit();
    }

    // Consulta para buscar informações do perfil, se existir
    $queryPerfil = "SELECT * FROM perfil WHERE id = (SELECT id FROM empresa WHERE email = :logado)";
    $stmtPerfil = $conexao->prepare($queryPerfil);
    $stmtPerfil->bindParam(':logado', $logado, PDO::PARAM_STR);
    $stmtPerfil->execute();
    $perfilAtual = $stmtPerfil->fetch(PDO::FETCH_ASSOC);

    // Processar a inserção/atualização do perfil
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pfpic'])) {
        $nome = $_POST['nome'];
        $bio = $_POST['bio'];
        $local = $_POST['local'];
        $imagemAtual = $perfilAtual ? $perfilAtual['imagem'] : 'image-removebg-preview.png'; // Imagem padrão caso não exista

        // Se uma nova imagem for enviada
        if ($_FILES['pfpic']['error'] == UPLOAD_ERR_OK) {
            $imagem = '../uploads/' . basename($_FILES['pfpic']['name']);
            move_uploaded_file($_FILES['pfpic']['tmp_name'], $imagem);
        } else {
            // Mantém a imagem atual se nenhuma nova for enviada
            $imagem = $imagemAtual;
        }

        if ($perfilAtual) {
            // Atualiza o perfil existente
            $queryAtualizar = "UPDATE perfil SET nome = :nome, imagem = :imagem, bio = :bio, local = :local WHERE id = (SELECT id FROM empresa WHERE email = :logado)";
            $stmtAtualizar = $conexao->prepare($queryAtualizar);
            $stmtAtualizar->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmtAtualizar->bindParam(':imagem', $imagem, PDO::PARAM_STR);
            $stmtAtualizar->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmtAtualizar->bindParam(':local', $local, PDO::PARAM_STR);
            $stmtAtualizar->bindParam(':logado', $logado, PDO::PARAM_STR);
            $stmtAtualizar->execute();
        } else {
            // Insere um novo perfil
            $queryInserir = "INSERT INTO perfil (id, nome, imagem, bio, local) VALUES ((SELECT id FROM empresa WHERE email = :logado), :nome, :imagem, :bio, :local)";
            $stmtInserir = $conexao->prepare($queryInserir);
            $stmtInserir->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmtInserir->bindParam(':imagem', $imagem, PDO::PARAM_STR);
            $stmtInserir->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmtInserir->bindParam(':local', $local, PDO::PARAM_STR);
            $stmtInserir->bindParam(':logado', $logado, PDO::PARAM_STR);
            $stmtInserir->execute();
        }

        // Recarrega os dados do perfil
        $stmtPerfil->execute();
        $perfilAtual = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
    }

    // Consulta SQL para buscar os perfis salvos pelo usuário logado
    $query = "SELECT p.id, p.nome, p.imagem 
              FROM perfil p
              INNER JOIN perfis_salvos ps ON p.id = ps.perfil_id
              WHERE ps.user_email = :logado";

    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':logado', $logado, PDO::PARAM_STR);
    $stmt->execute();
    $perfisSalvos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="profile.css">
    <style>
        #pfpic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            position: relative;
        }
        #profilePic {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        #input-pfpic {
            display: none;
        }
        .saved-profile {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .saved-profile img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .saved-profile form {
            margin-left: 10px;
        }
        /* Estilo para o botão de deletar conta */
        #delete-account-button {
            background-color: #ff4d4d;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        #delete-account-button:hover {
            background-color: #ff1a1a;
        }
    </style>
    <title>Document</title>
</head>
<body>
    <section id="container">
        <section class="leftmenu"> 
            <a href="../controler/logout.php" id="logout">Sair</a>
            <a href="../view/Tpagperfil.php" id="profilePage">Pagina de Perfis</a>
            <div id="viewsave">
                <?php foreach ($perfisSalvos as $perfil): ?>
                    <div class="saved-profile">
                        <img src="<?php echo htmlspecialchars($perfil['imagem']); ?>" alt="<?php echo htmlspecialchars($perfil['nome']); ?>">
                        <span><?php echo htmlspecialchars($perfil['nome']); ?></span>
                        <a href="http://localhost:3000">Iniciar Chat</a>
                        


                        <form action="" method="post" class="remove-button-form">
                            <input type="hidden" name="remove_perfil_id" value="<?php echo $perfil['id']; ?>">
                            <button type="submit" class="remove-button">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <section class="rightmenu"> 
            <!-- Formulário já existente -->
            <form action="" id="formzin" method="POST" enctype="multipart/form-data">
                <div id="pfpic" onclick="document.getElementById('input-pfpic').click()">
                    <img id="profilePic" src="<?php echo htmlspecialchars($perfilAtual['imagem'] ?? 'image-removebg-preview.png'); ?>" width="150px" alt="Mudar Foto de Perfil">
                </div>
                <input type="file" name="pfpic" id="input-pfpic" style="display: none;">
                <input type="text" name="nome" class="nome" placeholder="digite o nome da empresa" id="companyName" value="<?php echo htmlspecialchars($perfilAtual['nome'] ?? ''); ?>">
                <textarea name="bio" placeholder="Digite a bio da empresa" class="bio" id="companyBio"><?php echo htmlspecialchars($perfilAtual['bio'] ?? ''); ?></textarea>
                <input type="text" placeholder="digite o local da empresa" name="local" class="local" id="companyLocation" value="<?php echo htmlspecialchars($perfilAtual['local'] ?? ''); ?>">
                <input type="submit" class="submit" value="Enviar / Editar" id="submitButton">
            </form>

            <!-- Botão de configurações e dropdown -->
            <div class="config-btn" onclick="toggleDropdown()">
                <img src="config.png" alt="Configurações" width="30px">
            </div>

            <div class="dropdown" id="configDropdown">
            <a href="#">Mudar Idioma</a>
            <a href="#">Configuração 2</a>
            <a href="#">Configuração 3</a>
            </div>
        </section>  
    </section>

    <script>
        // Função para abrir e fechar o dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('configDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Fechar o dropdown se o usuário clicar fora dele
        window.onclick = function(event) {
            if (!event.target.closest('.config-btn')) {
                const dropdown = document.getElementById('configDropdown');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';
                }
            }
        };



    </script>
    </section>
    <script>
        // Visualizar a imagem selecionada antes do upload
        document.getElementById('input-pfpic').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profilePic').src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                // Se não houver arquivo, mantenha a imagem atual
                document.getElementById('profilePic').src = "<?php echo htmlspecialchars($perfilAtual['imagem'] ?? 'image-removebg-preview.png'); ?>";
            }
        });
    </script>
</body>
</html>
