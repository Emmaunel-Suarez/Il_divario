<?php
session_start();

$estado = $_GET['estado'] ?? 'pendiente';
$total = $_GET['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de pago</title>
</head>
<body>
    <h1>Confirmación de pago</h1>

    <?php if ($estado === 'aprobado'): ?>
        <p>✅ Tu pago de <strong>$<?= number_format($total, 0, ',', '.'); ?></strong> fue aprobado.</p>
        <a href="carrito.php">Volver al carrito</a>
    <?php else: ?>
        <p>⚠️ El pago está en estado: <strong><?= htmlspecialchars($estado); ?></strong></p>
        <a href="carrito.php">Volver al carrito</a>
    <?php endif; ?>
</body>
</html>
