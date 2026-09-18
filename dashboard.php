<?php
require_once __DIR__ . '/config/bootstrap.php';
requireUser();
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi espacio | I.E. Ricardo Palma</title>
  <link rel="stylesheet" href="auth/auth.css">
</head>
<body>
  <main class="auth-shell">
    <a class="auth-brand" href="index.html">I.E. Ricardo Palma <span>Marcona</span></a>
    <section class="auth-card">
      <p class="eyebrow">Espacio de usuario</p>
      <h1>Hola, <?php echo escape($user['name']); ?></h1>
      <p class="intro">Tu cuenta está activa. Desde aquí podremos agregar próximamente matrículas, comunicados y contenidos personalizados.</p>
      <p><strong>Correo:</strong> <?php echo escape($user['email']); ?></p>
      <p><strong>Perfil:</strong> <?php echo escape($user['role']); ?></p>
      <p><a class="switch" href="auth/logout.php">Cerrar sesión</a></p>
    </section>
  </main>
</body>
</html>
