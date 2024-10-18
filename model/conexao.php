
<?php
$host = 'localhost';
$dbname = 'syssec';
$username = 'root';
$password = ''; 

try {
    $conexao = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Testar a conexão
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
