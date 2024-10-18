<?php
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    header('Location: ../login/login.html');
    exit();
}

$logado = $_SESSION['email'];

$host = 'localhost';
$dbname = 'syssec';
$username = 'root';
$password = '';

try {
    $conexao = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Alternar salvar/remover perfil
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['perfil_id'])) {
        $perfil_id = $_POST['perfil_id'];

        // Verificar se o perfil já está salvo
        $queryVerificar = "SELECT COUNT(*) FROM perfis_salvos WHERE user_email = :user_email AND perfil_id = :perfil_id";
        $stmtVerificar = $conexao->prepare($queryVerificar);
        $stmtVerificar->bindParam(':user_email', $logado, PDO::PARAM_STR);
        $stmtVerificar->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
        $stmtVerificar->execute();
        $perfilSalvo = $stmtVerificar->fetchColumn();

        if ($perfilSalvo) {
            // Remover o perfil salvo
            $queryRemover = "DELETE FROM perfis_salvos WHERE user_email = :user_email AND perfil_id = :perfil_id";
            $stmtRemover = $conexao->prepare($queryRemover);
            $stmtRemover->bindParam(':user_email', $logado, PDO::PARAM_STR);
            $stmtRemover->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
            $stmtRemover->execute();
        } else {
            // Salvar o perfil
            $querySalvar = "INSERT INTO perfis_salvos (user_email, perfil_id) VALUES (:user_email, :perfil_id)";
            $stmtSalvar = $conexao->prepare($querySalvar);
            $stmtSalvar->bindParam(':user_email', $logado, PDO::PARAM_STR);
            $stmtSalvar->bindParam(':perfil_id', $perfil_id, PDO::PARAM_INT);
            $stmtSalvar->execute();
        }
    }

    // Consulta SQL para buscar os perfis, excluindo o perfil do usuário logado e filtrando por tipo de empresa
    // Primeiro, buscamos o tipo de empresa do usuário logado
    $queryTipo = "SELECT tipo FROM empresa WHERE email = :logado";
    $stmtTipo = $conexao->prepare($queryTipo);
    $stmtTipo->bindParam(':logado', $logado, PDO::PARAM_STR);
    $stmtTipo->execute();
    $tipoUsuario = $stmtTipo->fetchColumn();

    // Definindo o tipo oposto
    $tipoOposto = ($tipoUsuario == 'concessionaria') ? 'seguradora' : 'concessionaria';

    // Consulta para obter os perfis permitidos
    $query = "SELECT p.id, p.nome, p.imagem, p.bio, p.local, e.porte,
                     CASE WHEN ps.perfil_id IS NOT NULL THEN 1 ELSE 0 END AS salvo 
              FROM perfil p
              INNER JOIN empresa e ON p.id = e.id
              LEFT JOIN perfis_salvos ps ON ps.perfil_id = p.id AND ps.user_email = :logado
              WHERE e.email != :logado AND e.tipo = :tipoOposto";
    
    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':logado', $logado, PDO::PARAM_STR);
    $stmt->bindParam(':tipoOposto', $tipoOposto, PDO::PARAM_STR);
    $stmt->execute();
    $perfis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/pagperfis.css">
    <title>Lista de Perfis</title>
    
</head>
<body>
    <h1>
        <H1>Perfis Cadastrados :</H1>
        <button onclick="window.location.href='Tperfil.php'" class="back-button" id="voltar">Voltar</button>
    </h1>
    
    <?php foreach ($perfis as $perfil): ?>
        <div class="perfil">
            <img src="<?php echo htmlspecialchars($perfil['imagem']); ?>" alt="<?php echo htmlspecialchars($perfil['nome']); ?>">
            <div class="perfil-info">
            <form action="" method="post" class="save-button-form">
                    <input type="hidden" name="perfil_id" value="<?php echo $perfil['id']; ?>">
                    <button type="submit" class="save-button <?php echo $perfil['salvo'] ? 'saved' : ''; ?>">
                        <?php echo $perfil['salvo'] ? 'Remover' : 'Salvar'; ?>
                    </button>
                </form>
                <h2><?php echo htmlspecialchars($perfil['nome']); ?></h2>
                <p><?php echo htmlspecialchars($perfil['bio']); ?></p>
                <p><?php echo htmlspecialchars($perfil['local']); ?></p>
                <p><strong>Porte:</strong> <?php echo htmlspecialchars($perfil['porte']); ?></p>
                
            </div>
        </div>
    <?php endforeach; ?>

    <script>
        document.querySelectorAll('.save-button-form').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                var button = form.querySelector('.save-button');
                var formData = new FormData(form);

                fetch('', {
                    method: 'POST',
                    body: formData
                }).then(response => {
                    if (response.ok) {
                        button.classList.toggle('saved');
                        button.textContent = button.classList.contains('saved') ? 'Remover' : 'Salvar';
                    } else {
                        alert('Ocorreu um erro ao salvar o perfil.');
                    }
                }).catch(error => {
                    alert('Ocorreu um erro ao salvar o perfil.');
                });
            });
        });
    </script>

    <style>
        .back-button {
            font-size: 14px;
            color: #fff;
            background: #007e98;
            border: none;
            margin-left: 20px;
            padding: 5px 10px;
            font-weight: 600;
            transition: 0.3s ease;
            cursor: pointer;
        }
    </style>
</body>
</html>
