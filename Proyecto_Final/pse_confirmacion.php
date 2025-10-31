<?php
session_start();
require_once 'db.php';

// Verificar sesión activa
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'pse_confirmacion.php';
    header('Location: 6.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pago'])) {
    $usuario = $_SESSION['user_id'];
    $total = floatval($_POST['total'] ?? 0);
    $fecha = date('Y-m-d H:i:s');
    $numero_factura = 'FAC-' . strtoupper(substr(md5(uniqid()), 0, 8));

    // Vaciar carrito después del pago exitoso
    try {
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario]);
    } catch (PDOException $e) {
        die("Error al procesar el pago: " . $e->getMessage());
    }

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <title>Pago confirmado</title>
        <style>
            body {
                background-color: #fffaf1;
                font-family: Arial, sans-serif;
                color: #4b2e00;
                text-align: center;
                padding: 40px;
            }
            .factura {
                background-color: #fff8dc;
                border: 2px solid #ffbf00;
                border-radius: 15px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                padding: 25px;
                width: 80%;
                margin: auto;
                max-width: 600px;
            }
            h2 {
                color: #4b2e00;
                margin-bottom: 15px;
            }
            .detalle {
                text-align: left;
                margin: 20px 0;
                line-height: 1.8;
            }
            .total {
                font-size: 20px;
                font-weight: bold;
                color: #2b2b2b;
            }
            .boton {
                display: inline-block;
                margin-top: 25px;
                padding: 10px 20px;
                background-color: #ffbf00;
                color: #4b2e00;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                text-decoration: none;
                transition: 0.3s;
                font-weight: bold;
            }
            .boton:hover {
                background-color: #e6aa00;
                transform: scale(1.05);
            }
        </style>
    </head>
    <body>
        <div class="factura">
            <h2>✅ Pago Confirmado</h2>
            <p>¡Gracias por tu compra! 🧡</p>
            <div class="detalle">
                <p><strong>Número de factura:</strong> <?= htmlspecialchars($numero_factura) ?></p>
                <p><strong>Fecha de pago:</strong> <?= htmlspecialchars($fecha) ?></p>
                <p class="total">💰 Total pagado: $<?= number_format($total, 0, ',', '.') ?></p>
                <p>El pago ha sido registrado exitosamente.</p>
            </div>
            <a href="3.php" class="boton">⬅️ Volver a la tienda</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Si alguien entra directamente sin pagar
header('Location: 3.php');
exit;
?>
