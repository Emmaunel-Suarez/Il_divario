<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $rol = $_POST['rol'] ?? 'user'; // Por defecto es user

    if (empty($email) || empty($password) || empty($confirm)) {
        $_SESSION['error'] = 'Completa todos los campos.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Correo no válido.';
    } elseif ($password !== $confirm) {
        $_SESSION['error'] = 'Las contraseñas no coinciden.';
    } elseif (strlen($password) < 6) {
        $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $_SESSION['error'] = 'El correo ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $mysqli->prepare("INSERT INTO users (email, password, rol) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $email, $hash, $rol);

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Registro exitoso. Ya puedes iniciar sesión.';
                header('Location: 6.php');
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar usuario.';
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
  <title>Crear Cuenta</title>
  <link rel="stylesheet" href="6.css">
</head>
<body>
  <div class="login-container">
    <h2>Crear Cuenta</h2>

    <?php if (!empty($_SESSION['error'])): ?>
      <p style="color:red"><?= htmlspecialchars($_SESSION['error']) ?></p>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
      <p style="color:green"><?= htmlspecialchars($_SESSION['success']) ?></p>
      <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="POST" action="7.php">
      <div class="input-group">
        <label for="email">Correo:</label>
        <input type="email" id="email" name="email" required>
      </div>
      <div class="input-group">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="input-group">
        <label for="confirm_password">Confirmar contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <div class="input-group">
        <label for="rol">Tipo de cuenta:</label>
        <select id="rol" name="rol">
          <option value="user">Usuario</option>
          <option value="admin">Administrador</option>
        </select>
      </div>
      <button type="submit" class="login-btn">Registrar</button>
    </form>

    <a href="6.php">Ya tengo cuenta</a>
  </div>
</body>
</html>
