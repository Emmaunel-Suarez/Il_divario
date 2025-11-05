<?php
session_start();
require_once 'db.php'; // aquí debe estar tu conexión PDO en la variable $pdo

// Comprobar si hay usuario logueado
$logged_in = $_SESSION['logged_in'] ?? false;
$id_usuario = $_SESSION['id_usuario'] ?? null;

// Mensaje general
$mensaje = '';

// Manejar envío de comentarios (solo si está logueado)
if ($logged_in && $id_usuario !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $comentario = trim($_POST['comentario'] ?? '');
    if ($comentario !== '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO comentarios (id_usuario, comentario) VALUES (?, ?)");
            $stmt->execute([$id_usuario, $comentario]);
            $mensaje = "Comentario guardado correctamente.";
        } catch (PDOException $e) {
            $mensaje = "Error al guardar comentario: " . $e->getMessage();
        }
    } else {
        $mensaje = "El comentario no puede estar vacío.";
    }
}

// Manejar eliminación de comentario (solo propio)
if ($logged_in && isset($_GET['eliminar'])) {
    $id_comentario = intval($_GET['eliminar']);
    try {
        $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$id_comentario, $id_usuario]);
        $mensaje = "Comentario eliminado correctamente.";
    } catch (PDOException $e) {
        $mensaje = "Error al eliminar comentario: " . $e->getMessage();
    }
}

// Obtener comentarios con nombre de usuario
try {
    $stmt = $pdo->query("
        SELECT c.id AS id_comentario, c.comentario, c.fecha, u.nombre, c.id_usuario
        FROM comentarios c
        JOIN users u ON c.id_usuario = u.id
        ORDER BY c.fecha DESC
    ");
    $comentarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $mensaje = "Error al obtener comentarios: " . $e->getMessage();
    $comentarios = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Comentarios</title>
<link rel="stylesheet" href="5.css">
</head>
<body>

<nav class="navbar">
    <a href="1.php">Inicio</a>
    <a href="2.html">Información</a>
    <a href="3.php">Sabores</a>
    <a href="4.php">Manillas</a>
    <a href="5.php">Reseñas</a>
</nav>

<header>
    <h1>Comentarios</h1>
    <img src="./img/comentarios_png.png" alt="">
</header>

<main>
    <?php if ($logged_in): ?>
        <h2>¡Nos interesa tu opinión!</h2>
        <form method="POST">
            <textarea name="comentario" placeholder="Escribe aquí tus comentarios..."></textarea><br>
            <button type="submit" class="boton-envio">Enviar</button>
        </form>
    <?php else: ?>
        <p>Debes iniciar sesión para enviar comentarios.</p>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <p class="mensaje"><?= htmlspecialchars($mensaje); ?></p>
    <?php endif; ?>

    <h2>Comentarios recientes</h2>
    <?php if ($comentarios): ?>
        <ul class="comentarios-lista">
            <?php foreach ($comentarios as $c): ?>
                <li>
                    <strong><?= htmlspecialchars($c['nombre']); ?>:</strong> <?= htmlspecialchars($c['comentario']); ?>
                    <small>(<?= htmlspecialchars($c['fecha']); ?>)</small>
                    <?php if ($logged_in && $c['id_usuario'] == $id_usuario): ?>
                        <a href="?eliminar=<?= $c['id_comentario']; ?>" class="boton-eliminar">Eliminar</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay comentarios aún.</p>
    <?php endif; ?>
</main>

<footer>
    <button onclick="window.location.href='1.php';">Volver</button>
</footer>

</body>
</html>
