<?php
session_start();
require_once 'db.php';

// ==========================
// 🔐 Verificar sesión
// ==========================
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: 6.php");
    exit;
}

$usuario_id = $_SESSION['user_id'];

// ==========================
// 📦 Obtener elementos del carrito
// ==========================
try {
    $stmt = $pdo->prepare("SELECT * FROM carrito WHERE usuario_id = ?");
    $stmt->execute([$usuario_id]);
    $carrito = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $items = [];

    foreach ($carrito as $c) {
        $tipo = $c['tipo_producto'];
        $id_prod = $c['producto_id'];

        if ($tipo === 'crema') {
            $q = $pdo->prepare("SELECT nombre, precio, imagen FROM productos WHERE id = ?");
        } elseif ($tipo === 'manilla') {
            $q = $pdo->prepare("SELECT nombre, precio, imagen FROM manillas WHERE id = ?");
        } else {
            continue;
        }

        $q->execute([$id_prod]);
        $producto = $q->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            $producto['tipo_producto'] = ucfirst($tipo);
            $producto['cantidad'] = $c['cantidad'];
            $producto['subtotal'] = $producto['precio'] * $c['cantidad'];
            $producto['carrito_id'] = $c['id'];
            $items[] = $producto;
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
<title>🛍️ Mi Carrito</title>
<link rel="stylesheet" href="3.css">
<style>
/* ESTILOS BONITOS */
body {
    background-color: #fffaf1;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    color: #2b2b2b;
}

header {
    background-color:rgb(112, 99, 61);
    padding: 20px;
    text-align: center;
    color: #4b2e00;
}

table {
    width: 90%;
    margin: 30px auto;
    border-collapse: collapse;
    background: #fff8dc;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

th {
    background: #ffbf00;
    color: #4b2e00;
    padding: 15px;
}

td {
    text-align: center;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

img {
    width: 80px;
    height: 70px;
    border-radius: 8px;
}

.btn {
    padding: 10px 18px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

.btn-eliminar { background-color: #dc3545; color: white; }
.btn-eliminar:hover { background-color: #b02a37; }

.btn-pse { background-color: #28a745; color: white; }
.btn-pse:hover { background-color: #218838; }

.btn-vaciar { background-color: #ffc107; color: #4b2e00; }
.btn-vaciar:hover { background-color: #e0a800; }

.total { text-align: right; font-size: 18px; margin-right: 5%; }
</style>
</head>
<body>
<header>
   <nav class="navbar">
        <a href="1.php">Inicio</a>
        <a href="2.html">Información</a>
        <a href="3.php">Cremas</a>
        <a href="4.php">Manillas</a>
        <a href="5.php">Reseñas</a>
    </nav>
    <br>
    <br><br>
    <h1>🛍️ Mi Carrito</h1>
</header>

<main>
<?php if (empty($items)): ?>
    <p style="text-align:center; font-weight:bold;">Tu carrito está vacío.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Producto</th>
                <th>Imagen</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $total = 0;
        foreach ($items as $item):
            $total += $item['subtotal'];
        ?>
            <tr>
                <td><?= htmlspecialchars($item['tipo_producto']) ?></td>
                <td><?= htmlspecialchars($item['nombre']) ?></td>
                <td><img src="<?= htmlspecialchars($item['imagen']) ?>"></td>
                <td>$<?= number_format($item['precio'], 0, ',', '.') ?></td>
                <td><?= $item['cantidad'] ?></td>
                <td>$<?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                <td>
                    <form action="eliminar_carrito.php" method="POST">
                        <input type="hidden" name="id" value="<?= $item['carrito_id'] ?>">
                        <button class="btn btn-eliminar" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total">💰 Total a pagar: $<?= number_format($total, 0, ',', '.') ?></p>

    <div style="text-align:center; margin-top:20px;">
        <!-- ✅ Ahora el total se pasa correctamente -->
        <a href="pse_pago.php?total=<?= urlencode($total) ?>" class="btn btn-pse">💳 Pagar con PSE</a>

        <form action="limpiar_carrito.php" method="POST" style="display:inline;">
            <button class="btn btn-vaciar" type="submit">Vaciar carrito</button>
        </form>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <a href="1.php">← Seguir comprando</a>
    </div>
<?php endif; ?>
</main>
</body>
</html>
