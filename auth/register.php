<?php
require_once __DIR__ . '/../config/bootstrap.php';

if (isset($_SESSION['user'])) {
    redirect('../dashboard.php');
}

$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (mb_strlen($name) < 2) {
        $errors[] = 'Escribe tu nombre completo.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Escribe un correo válido.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if ($password !== $confirmation) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (!$errors) {
        try {
            $statement = $database->prepare(
                'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)'
            );
            $statement->execute([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            $_SESSION['user'] = [
                'id' => (int) $database->lastInsertId(),
                'name' => $name,
                'email' => $email,
                'role' => 'user',
            ];
            redirect('../dashboard.php');
        } catch (PDOException $exception) {
            if ((int) $exception->errorInfo[1] === 19) {
                $errors[] = 'Ese correo ya está registrado.';
            } else {
                $errors[] = 'No se pudo crear la cuenta. Inténtalo nuevamente.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear cuenta | I.E. Ricardo Palma</title>
  <link rel="stylesheet" href="auth.css">
</head>
<body>
  <main class="auth-shell">
    <a class="auth-brand" href="../index.html">I.E. Ricardo Palma <span>Marcona</span></a>
    <section class="auth-card">
      <p class="eyebrow">Comunidad ricardina</p>
      <h1>Crear una cuenta</h1>
      <p class="intro">Regístrate para acceder al espacio de usuarios.</p>
      <?php if ($errors): ?>
        <div class="alert" role="alert"><?php echo escape(implode(' ', $errors)); ?></div>
      <?php endif; ?>
      <form method="post">
        <label>Nombre completo<input name="name" type="text" value="<?php echo escape($name); ?>" required autocomplete="name"></label>
        <label>Correo electrónico<input name="email" type="email" value="<?php echo escape($email); ?>" required autocomplete="email"></label>
        <label>Contraseña<input name="password" type="password" required minlength="8" autocomplete="new-password"></label>
        <label>Repite la contraseña<input name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></label>
        <button type="submit">Crear cuenta</button>
      </form>
      <p class="switch">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
    </section>
  </main>
</body>
</html>
