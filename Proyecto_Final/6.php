<?php
session_start();
require_once 'db.php';

// Mostrar errores (solo en desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = 'Completa todos los campos.';
    } else {
        $stmt = $mysqli->prepare("SELECT id, nombre, password, rol FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $_SESSION['error'] = 'Correo o contraseña incorrectos.';
        } else {
            $stmt->bind_result($id, $nombre, $hash, $rol);
            $stmt->fetch();

            if (password_verify($password, $hash)) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $id;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_name'] = $nombre;
                $_SESSION['rol'] = $rol;
                $_SESSION['logged_in'] = true;

                if ($rol === 'admin') {
                    header('Location: admin_panel.php');
                } else {
                    header('Location: 1.php');
                }
                exit;
            } else {
                $_SESSION['error'] = 'Correo o contraseña incorrectos.';
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="6.css">
</head>
<body>
  <div class="login-container">
    <h2>Iniciar sesión</h2>

    <?php if (!empty($_SESSION['error'])): ?>
      <p style="color:red"><?= htmlspecialchars($_SESSION['error']) ?></p>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST" action="6.php">
      <div class="input-group">
        <label for="email">Correo:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="input-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="login-btn">Entrar</button>
    </form>

    <a href="7.php">Crear Cuenta</a>
  </div>
</body>
</html>
