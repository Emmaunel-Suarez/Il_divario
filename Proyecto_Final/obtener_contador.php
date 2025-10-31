<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['user_id'])) {
    $usuario = $_SESSION['user_id'];
} elseif (isset($_SESSION['id_usuario'])) {
    $usuario = $_SESSION['id_usuario'];
} elseif (isset($_SESSION['id'])) {
    $usuario = $_SESSION['id'];
} else {
    echo json_encode(['success' => true, 'contador' => 0]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(cantidad),0) AS total FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$usuario]);
    $total = (int) $stmt->fetchColumn();
    echo json_encode(['success' => true, 'contador' => $total]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage(), 'contador' => 0]);
}
