<?php
session_start();
require_once 'db.php';

if(!isset($_SESSION['id_usuario'])){
    die("Usuario no logueado");
}

if(isset($_POST['comentario'])){ // Ajustado a "comentario" según el formulario
    $comentario = trim($_POST['comentario']);
    $id_usuario = $_SESSION['id_usuario'];

    if($comentario === ''){
        die("El comentario no puede estar vacío");
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO comentarios (id_usuario, comentario) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $comentario]);

        // Redirigir antes de cualquier salida
        header("Location: 5.php");
        exit;
    } catch(PDOException $e){
        die("Error al guardar comentario: " . $e->getMessage());
    }
} else {
    die("No se envió ningún comentario");
}
