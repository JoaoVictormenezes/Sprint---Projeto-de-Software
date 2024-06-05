<?php
$dbHost = 'Localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'bd';
try {
    $conexao = new mysqli($dbHost,$dbUsername,$dbPassword,$dbName);
} catch (mysqliException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>