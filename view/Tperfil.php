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
        $imagemAtual = $perfilAtual ? $perfilAtual['imagem'] : 'image-removebg-preview.png';

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
    <link rel="stylesheet" href="css/profile.css">
    <style>
        #pfpic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
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
            margin-top: 5%;
            background-color:#394657;
            width:95%;
            margin-left:2.3%;
            border-radius: 0.5ch;
            height: 9vh;
        }
        .saved-profile span{
            color:white;
            font-weight:500;
            margin-left:5%
        }
        .saved-profile img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-left: 5%;

        }
    
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
        .remove-button {
    background-color: transparent; /* Fundo transparente */
    border: none; /* Remove a borda padrão do botão */
    cursor: pointer; /* Aponta o cursor para indicar que é clicável */
    padding: 0; /* Remove o padding para evitar espaçamento indesejado */
    display: flex; /* Alinha o conteúdo dentro do botão */
    align-items: center; /* Centraliza verticalmente */
    justify-content: center; /* Centraliza horizontalmente */
    transition: background-color 0.3s ease; /* Adiciona transição suave ao passar o mouse */
    margin-left: 110%;
}


/* Estilo para a imagem do botão, se necessário */
.remove-button img {
    width: 50px; /* Ajuste o tamanho da imagem do botão */
    height: auto; 
    /* Mantém a proporção da imagem */
}

    </style>
    <title>Tela de Perfil</title>
</head>
<body>
    <header>
        <a href="http://" class="buttomdeal">DEAL</a>
        <div id="configdiv">
        <a href="../controller/AuthController.php" id="sair"  >Sair</a>
        <a href="../view/Tpagperfil.php" id="sair">Explorar</a>
        <div class="config-btn" onclick="toggleDropdown()">
                <img src="css/configbutton.png"  width="50px">
            </div>

            <div class="dropdown" id="configDropdown">
                <a href="#">Mudar Idioma</a>
                <a href="#">Configuração 2</a>
                <form action="" method="post">
                    <input type="hidden" name="delete_account" value="1">
                    <button type="submit" id="delete-account-button">Excluir Conta</button>
                </form>
            </div>
        </div>
    </header>
    <section id="container">
        <section class="leftmenu"> 
            <h1 id="label">Perfis Salvos :</h1>
            <div id="viewsave">
                <?php foreach ($perfisSalvos as $perfil): ?>
                    
                    <div class="saved-profile">
    <img src="<?php echo htmlspecialchars($perfil['imagem']); ?>" alt="<?php echo htmlspecialchars($perfil['nome']); ?>">
    <span><?php echo htmlspecialchars($perfil['nome']); ?></span>
    <a href="http://localhost:3000">
        <h1><img src="css/chatbutton.png" alt="" srcset=""></h1>
    </a>
    <form action="" method="post" class="remove-button-form">
        <input type="hidden" name="remove_perfil_id" value="<?php echo $perfil['id']; ?>">
        <button type="submit" class="remove-button" value="<?php echo $perfil['id']; ?>">
            <img src="css/fechar.png" alt="" srcset="">
        </button>
    </form>
</div>
                    
                <?php endforeach; ?>
            </div>
        </section>
        
        <section class="rightmenu"> 
            <form action="" id="formzin" method="POST" enctype="multipart/form-data">
                <h1 id="perfilziun">Perfil :</h1>
                <div id="pfpic" onclick="document.getElementById('input-pfpic').click()">
                    <img id="profilePic" src="<?php echo htmlspecialchars($perfilAtual['imagem'] ?? 'css/image-removebg-preview.png'); ?>" width="150px" alt="Mudar Foto de Perfil">
                </div>
                
                <input type="file" name="pfpic" id="input-pfpic">
                <h2 id="perfilziuns">Nome :</h2>
                <input type="text" name="nome" class="nome" placeholder="Digite o nome da empresa" id="companyName" value="<?php echo htmlspecialchars($perfilAtual['nome'] ?? ''); ?>">
                <h2 id="perfilziuns">Bio :</h2>
                <textarea name="bio" placeholder="Digite a bio da empresa" class="bio" id="companyBio"><?php echo htmlspecialchars($perfilAtual['bio'] ?? ''); ?></textarea>
                <h2 id="perfilziuns">Localizacao :</h2>
                <input type="text" placeholder="Digite o local da empresa" name="local" class="local" id="companyLocation" value="<?php echo htmlspecialchars($perfilAtual['local'] ?? ''); ?>">
                <input type="submit" class="submit" value="Enviar / Editar" id="submitButton">
            </form>

            
        </section>
    </section>

    <script>
        // Função para mostrar/esconder o dropdown de configurações
        function toggleDropdown() {
            const dropdown = document.getElementById("configDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        }

        // Função para pré-visualizar a imagem do perfil
        document.getElementById('input-pfpic').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePic').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
