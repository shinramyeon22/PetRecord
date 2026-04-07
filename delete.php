<?php
// Database configuration - PetRecord System (for Laragon)
$host = 'sql102.infinityfree.com';
$port = 3306;
$dbName = 'if0_41586855_petrec';      // ← Professional name aligned with Pet content
$username = 'if0_41586855';
$password = 'ot0AKtU9ZYITi';             // Default blank in Laragon

// Connection string
$dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>