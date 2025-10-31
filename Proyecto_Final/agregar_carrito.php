<?php
session_start();
require_once 'db.php'; // conexión PDO

header('Content-Type: application/json; charset=utf-8');

// ==============================
// 🧍 Verificar usuario logueado
// ==============================
if (isset($_SESSION['user_id'])) {
    $usuario = $_SESSION['user_id'];
} elseif (isset($_SESSION['id_usuario'])) {
    $usuario = $_SESSION['id_usuario'];
} elseif (isset($_SESSION['id'])) {
    $usuario = $_SESSION['id'];
} else {
    echo json_encode([
        'success' => false,
        'message' => '⚠️ No has iniciado sesión',
        'contador' => 0
    ]);
    exit;
}

// ==============================
// 📦 Validar datos del POST
// ==============================
$idProducto = isset($_POST['id']) ? intval($_POST['id']) : 0;
$cantidad = isset($_POST['cantidad']) ? max(1, intval($_POST['cantidad'])) : 1;
$tipo = isset($_POST['tipo']) ? trim(strtolower($_POST['tipo'])) : 'crema';

if ($idProducto <= 0 || !in_array($tipo, ['crema', 'manilla'])) {
    echo json_encode([
        'success' => false,
        'message' => '🛑 Datos inválidos del producto',
        'contador' => 0
    ]);
    exit;
}

try {
    // =================================
    // 🔎 Verificar si ya existe producto
    // =================================
    $stmt = $pdo->prepare("
        SELECT id, cantidad 
        FROM carrito 
        WHERE usuario_id = ? AND producto_id = ? AND tipo_producto = ?
    ");
    $stmt->execute([$usuario, $idProducto, $tipo]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // 🔁 Si ya existe, aumentar cantidad
        $nuevaCantidad = $row['cantidad'] + $cantidad;
        $upd = $pdo->prepare("UPDATE carrito SET cantidad = ? WHERE id = ?");
        $upd->execute([$nuevaCantidad, $row['id']]);
    } else {
        // 🆕 Insertar nuevo producto
        $ins = $pdo->prepare("
            INSERT INTO carrito (usuario_id, producto_id, cantidad, tipo_producto)
            VALUES (?, ?, ?, ?)
        ");
        $ins->execute([$usuario, $idProducto, $cantidad, $tipo]);
    }

    // =================================
    // 🔢 Calcular total actual del carrito
    // =================================
    $cnt = $pdo->prepare("
        SELECT COALESCE(SUM(cantidad), 0) AS total 
        FROM carrito 
        WHERE usuario_id = ?
    ");
    $cnt->execute([$usuario]);
    $total = (int) $cnt->fetchColumn();

    echo json_encode([
        'success' => true,
        'message' => '✅ Producto agregado correctamente',
        'contador' => $total
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => '💥 Error al guardar en la base de datos: ' . $e->getMessage(),
        'contador' => 0
    ]);
}
?>
