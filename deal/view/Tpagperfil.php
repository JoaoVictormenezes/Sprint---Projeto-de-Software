<?php
// Verifica se o usuário está logado
session_start();
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    header('Location: ../login/login.html');
    exit();
}

// Obtém o email do usuário logado da sessão
$logado = $_SESSION['email'];

$host = 'localhost';
$dbname = 'syssec';
$username = 'root';
$password = '';

try {
    // Conexão com o banco de dados
    $conexao = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consulta SQL para buscar os perfis, excluindo o perfil do usuário logado
    $query = "SELECT p.id, p.nome, p.imagem, p.bio, p.local FROM perfil p
              INNER JOIN empresa e ON p.id = e.id
              WHERE e.email != :logado";
    
    $stmt = $conexao->prepare($query);
    $stmt->bindParam(':logado', $logado, PDO::PARAM_STR);
    $stmt->execute();
    $perfis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Perfis</title>
    <style>
        .perfil {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .perfil img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
        }
        .perfil-info {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>
<body>
    <h1>Lista de Perfis</h1>
    <?php foreach ($perfis as $perfil): ?>
        <div class="perfil">
            <img src="<?php echo htmlspecialchars($perfil['imagem']); ?>" alt="<?php echo htmlspecialchars($perfil['nome']); ?>">
            <div class="perfil-info">
                <h2><?php echo htmlspecialchars($perfil['nome']); ?></h2>
                <p><?php echo htmlspecialchars($perfil['bio']); ?></p>
                <p><?php echo htmlspecialchars($perfil['local']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>
