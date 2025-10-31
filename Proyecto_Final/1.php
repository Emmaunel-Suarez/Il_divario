<?php
session_start();
$user_name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>   
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il Divario</title>
    <link rel="stylesheet" href="1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">
</head>
<body>

    <!-- Barra de navegación -->
    <nav class="navbar">
        <a href="1.php">Inicio</a>
        <a href="2.html">Información</a>
        <a href="3.php">Sabores</a>
        <a href="4.php">Manillas</a>
        <a href="5.php">Reseñas</a>

        <?php if (empty($user_name)): ?>
            <!-- Solo visible si NO hay sesión -->
            <a href="6.php">Iniciar sesión</a>
            <a href="7.php">Registrarse</a>
        <?php else: ?>
            <!-- Solo visible si hay sesión -->
            <span>👤 Bienvenid@, <?= htmlspecialchars($user_name) ?></span>
            <a href="logout.php" style="color:red; font-weight:bold;">Cerrar sesión</a>
        <?php endif; ?>
    </nav>

    <!-- Carrusel -->
    <div class="swiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide"><img src="./img/0a24e024-db7b-49a2-9ba9-48263efddc03.jpg" alt="Slide 1"></div>
            <div class="swiper-slide"><img src="./img/WhatsApp Image 2025-10-01 at 9.06.06 AM.jpeg" alt="Slide 2"></div>
            <div class="swiper-slide"><img src="./img/459bc9d1-0b71-4454-affa-a7c7786b23f2.jpg" alt="Slide 3"></div>
        </div>
    </div>

    <h1>Il Divario</h1>
    <h2>Cremas y pulseras</h2>

    <!-- Tarjetas -->
    <div class="card-container">
        <a class="card" href="2.html">
            <img src="./img/WhatsApp Image 2025-04-28 at 7.38.20 AM.jpeg" alt="Información">
            <span>Información de la página</span>
        </a>

        <a class="card" href="3.php">
            <img src="./img/WhatsApp Image 2025-05-05 at 3.25.40 AM.jpeg" alt="Sabores">
            <span>Sabores de cremas</span>
        </a>

        <a class="card" href="4.php">
            <img src="./img/WhatsApp Image 2025-05-07 at 10.44.39 AM.jpeg" alt="Manillas">
            <span>Diseño de manillas</span>
        </a>

        <a class="card" href="5.php">
            <img src="./img/WhatsApp Image 2025-05-07 at 10.48.56 AM.jpeg" alt="Reseñas">
            <span>Comentarios y/o reseñas</span>
        </a>

        <?php if (empty($user_name)): ?>
            <!-- Estas tarjetas solo se muestran si NO está logueado -->
            <a class="card" href="6.php">
                <img src="./img/regitro.webp" alt="Iniciar sesión">
                <span>Iniciar sesión</span>
            </a>

            <a class="card" href="7.php">
                <img src="./img/inicio de sesion.png" alt="Registrarse">
                <span>Registrarse</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        const swiper = new Swiper('.swiper', {
            loop: true,
            autoplay: { delay: 3000 },
        });
    </script>
</body>
</html>
