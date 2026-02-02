<?php
// Configurações do Banco de Dados OddNex
// Se estiver usando XAMPP/Localhost, os dados abaixo costumam ser o padrão:
$host = 'localhost';      
$dbname = 'oddnex_db';    
$user = 'root';           
$pass = '';               

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erro na conexão: " . $e->getMessage());
}
?>
