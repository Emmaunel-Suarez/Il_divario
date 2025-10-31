<?php
session_start();
require_once 'db.php';

$logged_in = $_SESSION['logged_in'] ?? false;
$id_usuario = $_SESSION['id_usuario'] ?? null;

if($logged_in && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_comentario'])){
    $id_comentario = (int) $_POST['id_comentario'];

    try {
        // Solo eliminar si el comentario pertenece al usuario logueado
        $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$id_comentario, $id_usuario]);
    } catch(PDOException $e){
        die("Error al eliminar comentario: " . $e->getMessage());
    }
}

header("Location: 5.php");
exit;
?>
