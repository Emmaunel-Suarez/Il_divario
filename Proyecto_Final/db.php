<?php
// db.php
$host = "localhost";
$db   = "mi_app"; // Cambia esto por el nombre real de tu base de datos
$user = "root";
$pass = ""; // tu contraseña si aplica

// Conexión MySQLi
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Error de conexión (MySQLi): " . $mysqli->connect_error);
}

// Conexión PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión (PDO): " . $e->getMessage());
}
?>
