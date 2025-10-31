<?php
session_start();
require_once 'db.php';

// 🔒 Solo el administrador puede acceder
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    header("Location: 6.php");
    exit;
}

// 📦 Determinar tabla
$tipo = $_GET['tipo'] ?? 'productos';
$tabla = $tipo === 'manillas' ? 'manillas' : 'productos';

// 📁 Carpeta de imágenes
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// ➕ Agregar producto
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $imagen = '';

    if (!empty($_FILES['imagen']['name'])) {
        $fileName = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            $imagen = 'uploads/' . $fileName;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO $tabla (nombre, precio, descripcion, imagen) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $precio, $descripcion, $imagen]);
    header("Location: admin_panel.php?tipo=$tipo");
    exit;
}

// ✏️ Editar producto
if (isset($_POST['editar_guardar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $descripcion = $_POST['descripcion'];
    $imagen = $_POST['imagen_actual'];

    if (!empty($_FILES['imagen']['name'])) {
        $fileName = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $targetPath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            $imagen = 'uploads/' . $fileName;
        }
    }

    $stmt = $pdo->prepare("UPDATE $tabla SET nombre=?, precio=?, descripcion=?, imagen=? WHERE id=?");
    $stmt->execute([$nombre, $precio, $descripcion, $imagen, $id]);
    header("Location: admin_panel.php?tipo=$tipo");
    exit;
}

// ❌ Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $stmt = $pdo->prepare("DELETE FROM $tabla WHERE id=?");
    $stmt->execute([$id]);
    header("Location: admin_panel.php?tipo=$tipo");
    exit;
}

// 📋 Obtener productos
$productos = $pdo->query("SELECT * FROM $tabla")->fetchAll(PDO::FETCH_ASSOC);

// 🧩 Producto a editar
$editar = null;
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $stmt = $pdo->prepare("SELECT * FROM $tabla WHERE id=?");
    $stmt->execute([$id]);
    $editar = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel de Administración - Il Divario</title>
<style>
/* =====================
   ESTILOS PANEL ADMIN
   ===================== */
body {
    font-family: 'Poppins', Arial, sans-serif;
    background-color: #fffaf1;
    color: #4b2e00;
    margin: 0;
}

header {
    background-color: #ffbf00;
    color: #4b2e00;
    text-align: center;
    padding: 20px;
    font-size: 24px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
}

.logout {
    position: absolute;
    top: 15px;
    right: 20px;
}

.logout a {
    text-decoration: none;
    background-color: white;
    color: #4b2e00;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}
.logout a:hover {
    background-color: #e6aa00;
    color: white;
}

.container {
    width: 90%;
    margin: 30px auto;
    background: #fff8dc;
    border-radius: 15px;
    box-shadow: 0 0 12px rgba(255, 191, 0, 0.3);
    padding: 25px;
}

.tabs {
    margin-bottom: 20px;
    text-align: center;
}
.tabs a {
    text-decoration: none;
    color: #4b2e00;
    background-color: #fff4c1;
    padding: 10px 20px;
    border-radius: 10px;
    margin: 0 5px;
    font-weight: bold;
}
.tabs a.active {
    background-color: #ffbf00;
    color: white;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}
th, td {
    border: 1px solid #ffda6a;
    padding: 10px;
    text-align: center;
}
th {
    background-color: #ffe68f;
    color: #4b2e00;
}
tr:nth-child(even) {
    background-color: #fffbe3;
}
tr:hover {
    background-color: #fff4c1;
}
img {
    border-radius: 8px;
    max-width: 70px;
    height: 70px;
    object-fit: cover;
}

form {
    margin-top: 25px;
    display: flex;
    flex-direction: column;
    align-items: center;
}
input, textarea {
    width: 90%;
    padding: 10px;
    margin: 6px;
    border-radius: 8px;
    border: 1px solid #d1a000;
}
button {
    background-color: #ffbf00;
    color: #4b2e00;
    border: none;
    border-radius: 8px;
    padding: 10px 15px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    background-color: #e6aa00;
    transform: scale(1.05);
}
.actions a {
    text-decoration: none;
    color: #b35f00;
    font-weight: bold;
}
.actions a:hover {
    color: #7b2e00;
}
footer {
    padding: 20px;
    background-color: #fff4c1;
    text-align: center;
}
footer button {
    background-color: #ffbf00;
    color: #4b2e00;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: bold;
    cursor: pointer;
}
footer button:hover {
    background-color: #e6aa00;
}
</style>
</head>
<body>

<header>
    🛍️ Panel de Administración - Il Divario
    <div class="logout"><a href="logout.php">Cerrar sesión</a></div>
</header>

<div class="container">
    <div class="tabs">
        <a href="?tipo=productos" class="<?= $tipo === 'productos' ? 'active' : '' ?>"> Cremas</a>
        <a href="?tipo=manillas" class="<?= $tipo === 'manillas' ? 'active' : '' ?>"> Manillas</a>
    </div>

    <h2>Gestión de <?= $tipo === 'productos' ? 'Cremas Artesanales' : 'Manillas Contemporáneas' ?></h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($productos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td>$<?= number_format($p['precio'], 0, ',', '.') ?></td>
            <td><?= htmlspecialchars($p['descripcion']) ?></td>
            <td><img src="<?= htmlspecialchars($p['imagen']) ?>" alt=""></td>
            <td class="actions">
                <a href="?tipo=<?= $tipo ?>&editar=<?= $p['id'] ?>">✏️</a> |
                <a href="?tipo=<?= $tipo ?>&eliminar=<?= $p['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">🗑️</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3><?= $editar ? "✏️ Editar Producto" : "➕ Agregar Nuevo Producto" ?></h3>
    <form method="post" enctype="multipart/form-data">
        <?php if ($editar): ?>
            <input type="hidden" name="id" value="<?= $editar['id'] ?>">
            <input type="hidden" name="imagen_actual" value="<?= $editar['imagen'] ?>">
        <?php endif; ?>

        <input type="text" name="nombre" placeholder="Nombre" required value="<?= $editar['nombre'] ?? '' ?>">
        <input type="number" name="precio" placeholder="Precio" step="0.01" required value="<?= $editar['precio'] ?? '' ?>">
        <textarea name="descripcion" placeholder="Descripción"><?= $editar['descripcion'] ?? '' ?></textarea>
        <input type="file" name="imagen" accept="image/*">

        <button type="submit" name="<?= $editar ? 'editar_guardar' : 'agregar' ?>">
            <?= $editar ? '💾 Guardar Cambios' : '➕ Agregar Producto' ?>
        </button>
    </form>
</div>

<footer>
    <button onclick="window.location.href='1.php'">🏠 Ir al inicio</button>
</footer>

</body>
</html>
