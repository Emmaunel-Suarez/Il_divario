<?php
session_start();
require_once 'db.php';

// Simular usuario logueado si no hay sesión
if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['id_usuario'] = 1;
}

try {
    $stmt = $pdo->query("SELECT * FROM manillas");
    $manillas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error en la consulta SQL: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="3.css">
<title>Joyería - Manillas</title>
</head>
<body>

<header>
   <nav class="navbar">
        <a href="1.php">Inicio</a>
        <a href="2.html">Información</a>
        <a href="3.php">Sabores</a>
        <a href="4.php" class="active">Manillas</a>
        <a href="5.php">Reseñas</a>
    </nav>
    <br><br>
    <h1>Joyería Contemporánea</h1>
</header>

<main>
    <h2>Manillas Artesanales</h2>
    <div id="container">
        <?php if (!empty($manillas)): ?>
            <?php foreach ($manillas as $m): ?>
                <div class="card">
                    <div class="card-head">
                        <h4><?= htmlspecialchars($m['nombre']); ?></h4>
                        <img src="<?= htmlspecialchars($m['imagen']); ?>" alt="<?= htmlspecialchars($m['nombre']); ?>">
                    </div>
                    <div class="card-body">
                        <p>Precio: $<?= number_format($m['precio'], 0, ',', '.'); ?></p>
                        <label for="cantidad_<?= $m['id']; ?>">Cantidad:</label>
                        <input type="number" id="cantidad_<?= $m['id']; ?>" value="1" min="1">
                        <br>
                        <button class="add-to-cart" data-id="<?= $m['id']; ?>" data-tipo="manilla">Añadir al carrito</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay manillas disponibles.</p>
        <?php endif; ?>
    </div>
</main>

<footer>
    <button onclick="window.location.href='1.php';">Volver</button>
</footer>

<!-- 🛒 Botón flotante del carrito -->
<button id="carrito-flotante">
    🛒 <span id="contador-carrito">0</span>
    <span id="limpiar-carrito" title="Vaciar carrito">🗑️</span>
</button>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const contadorSpan = document.getElementById("contador-carrito");
    const container = document.getElementById("container");
    const carritoBtn = document.getElementById("carrito-flotante");
    const limpiarBtn = document.getElementById("limpiar-carrito");

    // 🔹 Obtener el contador real desde el servidor
    actualizarContador();

    // 🔹 Ir al carrito
    carritoBtn.addEventListener("click", (e) => {
        if (e.target !== limpiarBtn) {
            window.location.href = "carrito.php";
        }
    });

    // 🔹 Limpiar carrito
    limpiarBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        fetch("limpiar_carrito.php", { method: "POST" })
        .then(() => actualizarContador());
    });

    // 🔹 Añadir producto
    container.addEventListener("click", (e) => {
        if (e.target.classList.contains("add-to-cart")) {
            const id = e.target.dataset.id;
            const tipo = e.target.dataset.tipo;
            const cantidad = document.getElementById('cantidad_' + id).value;

            fetch("agregar_carrito.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: `id=${id}&cantidad=${cantidad}&tipo=${tipo}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    actualizarContador();
                } else {
                    alert("⚠️ Error: " + data.message);
                }
            });
        }
    });

    // 🔹 Función para actualizar contador
    function actualizarContador() {
        fetch("obtener_contador.php")
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                contadorSpan.textContent = data.contador;
            }
        });
    }
});
</script>

</body>
</html>
