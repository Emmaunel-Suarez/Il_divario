<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: 6.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $usuario_id]);
}

header("Location: carrito.php");
exit;
