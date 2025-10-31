<?php
session_start();
require_once 'db.php';

// Verificar sesión
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: 6.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];

try {
    // Obtener productos del carrito desde la base de datos
    $stmt = $pdo->prepare("SELECT * FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];
    $total = 0;

    foreach ($carrito as $c) {
        $tipo = $c['tipo_producto'];
        $id_prod = $c['producto_id'];

        if ($tipo === 'crema') {
            $q = $pdo->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
        } elseif ($tipo === 'manilla') {
            $q = $pdo->prepare("SELECT nombre, precio FROM manillas WHERE id = ?");
        } else {
            continue;
        }

        $q->execute([$id_prod]);
        $producto = $q->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $producto['cantidad'] = $c['cantidad'];
            $producto['subtotal'] = $producto['precio'] * $c['cantidad'];
            $items[] = $producto;
            $total += $producto['subtotal'];
        }
    }

} catch (PDOException $e) {
    die("Error al obtener carrito: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pago con PSE - Factura</title>
<style>
/* ======================
   ESTILOS GENERALES
====================== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #fffaf1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    color: #2b2b2b;
}

header {
    background-color: #ffbf00;
    color: #4b2e00;
    padding: 20px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

main {
    flex: 1;
    padding: 30px 20px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
    color: #4b2e00;
}

table {
    width: 80%;
    margin: 0 auto 30px auto;
    border-collapse: collapse;
    background-color: #fff8dc;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

th, td {
    border: 1px solid #e0c080;
    padding: 12px;
    text-align: center;
}

th {
    background-color: #ffbf00;
    color: #4b2e00;
}

tr:nth-child(even) {
    background-color: #fff4c1;
}

.total {
    font-size: 22px;
    font-weight: bold;
    color: #4b2e00;
    margin: 25px;
}

button {
    padding: 12px 25px;
    background-color: #ffbf00;
    color: #4b2e00;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    margin: 10px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

button:hover {
    background-color: #e6aa00;
    transform: scale(1.05);
}

footer {
    background-color: #fff4c1;
    text-align: center;
    padding: 15px;
    color: #4b2e00;
    font-weight: bold;
}
</style>
</head>
<body>

<header>
    <h1>💳 Pago con PSE</h1>
</header>

<main>
    <h2>Factura de compra</h2>

    <?php if (empty($items)): ?>
        <p style="color:#6a1b9a; font-weight:bold;">Tu carrito está vacío.</p>
        <button onclick="window.location.href='carrito.php'">Volver al carrito</button>
    <?php else: ?>
        <table>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                    <td><?= $item['cantidad'] ?></td>
                    <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                    <td>$<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="total">
            💰 Total a pagar: $<?= number_format($total, 0, ',', '.') ?>
        </div>

        <form method="POST" action="pse_confirmacion.php">
            <input type="hidden" name="total" value="<?= htmlspecialchars($total) ?>">
            <button type="submit" name="confirmar_pago">✅ Confirmar pago simulado</button>
            <button type="button" onclick="window.location.href='carrito.php'">⬅️ Volver al carrito</button>
        </form>
    <?php endif; ?>
</main>

<footer>
    © 2025 Tienda Virtual - Pagos Seguros con PSE 💳
</footer>

</body>
</html>
