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
    </style>
    <title>Document</title>
</head>
<body>
     

    <section id="container">
        <section class="leftmenu"> 
             <a href="../controler/logout.php">Sair</a>
            <a href="../view/Tpagperfil.php">Pagina de Perfis</a>
            <div id="viewsave">
                <?php foreach ($perfisSalvos as $perfil): ?>
                    <div class="saved-profile">
                        <img src="<?php echo htmlspecialchars($perfil['imagem']); ?>" alt="<?php echo htmlspecialchars($perfil['nome']); ?>">
                        <span><?php echo htmlspecialchars($perfil['nome']); ?></span>
                        <form action="" method="post" class="remove-button-form">
                            <input type="hidden" name="remove_perfil_id" value="<?php echo $perfil['id']; ?>">
                            <button type="submit" class="remove-button">Remover</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
         </section>
        
        <section class="rightmenu"> 
            <form action="../controler/Cinserirperfil.php" id="formzin" method="POST" enctype="multipart/form-data">

            <!-- Utilize uma div como botão -->
            <div id="pfpic" onclick="document.getElementById('input-pfpic').click()">
                <img id="profilePic" src="image-removebg-preview.png" width="150px" alt="Mudar Foto de Perfil">
            </div>
            
            <!-- Input file oculto para selecionar a imagem -->
            <input type="file" name="pfpic" id="input-pfpic" style="display: none;">

            <input type="text" name="nome" class="nome">

            <textarea name="bio" class="bio"></textarea>

            <input type="text" name="local" class="local">
            
            <input type="submit" class="submit" value="Enviar / Editar">

            </form>
        </section>
    </section>

    <div id="settings-icon">
        <img src="config.png" alt="Configurações">
    </div>

    <div id="dropdown-menu">
        <div class="dropdown-item" id="item1">Item 1</div>
        <div class="dropdown-item" id="item2">Item 2</div>
        <div class="dropdown-item" id="toggle-mode">Modo Claro / Modo Escuro</div>
    </div>


    <script>
         document.getElementById('settings-icon').addEventListener('click', function() {
            const dropdown = document.getElementById('dropdown-menu');
            dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'block' : 'none';
        });

        // claro/escuro
        document.getElementById('toggle-mode').addEventListener('click', function() {
            const body = document.body;
            if (body.classList.contains('light-mode')) {
                body.classList.remove('light-mode');
                body.classList.add('dark-mode');
            } else {
                body.classList.remove('dark-mode');
                body.classList.add('light-mode');
            }
        });

        // dropdown
        window.onclick = function(event) {
            if (!event.target.matches('#settings-icon img')) {
                const dropdown = document.getElementById('dropdown-menu');
                if (dropdown.style.display === 'block') {
                    dropdown.style.display = 'none';

                }
            }
        }

        document.getElementById('input-pfpic').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const reader = new FileReader();

            reader.onload = function(event) {
                document.getElementById('profilePic').src = event.target.result;
            }

            reader.readAsDataURL(file);
        });
    </script>
</body>
</html>
