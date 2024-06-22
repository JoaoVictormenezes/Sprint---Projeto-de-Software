<?php
// Configurações do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "syssec";

// Conectar ao banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Capturar a consulta de pesquisa
$query = $_GET['query'];

// Prevenir SQL Injection
$query = $conn->real_escape_string($query);

// Criar e executar a consulta SQL
$sql = "SELECT * FROM empresa WHERE nome LIKE '%$query%'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Pesquisa</title>
</head>
<body>
    <h1>Resultados da Pesquisa</h1>

    <?php
    if ($result->num_rows > 0) {
        // Exibir os resultados
        while($row = $result->fetch_assoc()) {
            echo "<p>" . $row['nome'] . "</p>";
        }
    } else {
        echo "<p>Nenhum resultado encontrado</p>";
    }

    // Fechar a conexão
    $conn->close();
    ?>

</body>
</html>
