<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: 6.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);

header("Location: carrito.php");
exit;
