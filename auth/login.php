<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (isset($_SESSION['user'])) {
    redirect('../dashboard.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    $statement = $database->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect('../dashboard.php');
    }

    $error = 'El correo o la contraseña no son correctos.';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesión | I.E. Ricardo Palma</title>
  <link rel="stylesheet" href="auth.css">
</head>
<body>
  <main class="auth-shell">
    <a class="auth-brand" href="../index.html">I.E. Ricardo Palma <span>Marcona</span></a>
    <section class="auth-card">
      <p class="eyebrow">Acceso de usuarios</p>
      <h1>Bienvenido</h1>
      <p class="intro">Ingresa a tu espacio de la comunidad educativa.</p>
      <?php if ($error): ?><div class="alert" role="alert"><?php echo escape($error); ?></div><?php endif; ?>
      <form method="post">
        <label>Correo electrónico<input name="email" type="email" value="<?php echo escape($email); ?>" required autocomplete="email"></label>
        <label>Contraseña<input name="password" type="password" required autocomplete="current-password"></label>
        <button type="submit">Iniciar sesión</button>
      </form>
      <p class="switch">¿Todavía no tienes cuenta? <a href="register.php">Regístrate</a></p>
    </section>
  </main>
</body>
</html>
