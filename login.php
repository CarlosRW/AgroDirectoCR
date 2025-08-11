<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $pass = $_POST['contrasena'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE correo = ?");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($pass, $usuario['contrasena'])) {
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['correo'] = $usuario['correo'];
        $_SESSION['direccion'] = $usuario['direccion'];

        header("Location: panel.php");
        exit;
    } else {
        $mensaje = "<div class='alert alert-danger'>Correo o contraseña incorrectos</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Iniciar sesión</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow w-100" style="max-width: 400px">
        <h3 class="card-title text-center mb-4">Iniciar sesión</h3>
        <?php if (!empty($mensaje)) echo $mensaje; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <input type="email" class="form-control" name="correo" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" class="form-control" name="contrasena" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Entrar</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
