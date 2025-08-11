<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $pass     = $_POST['contrasena'] ?? '';
    $pass2    = $_POST['contrasena_confirm'] ?? '';
    $rol      = $_POST['rol'] ?? '';
    $direccion= trim($_POST['direccion'] ?? '');

    // Validaciones
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<div class='alert alert-danger'>Correo inválido</div>";
    } elseif ($pass !== $pass2) {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas no coinciden</div>";
    } elseif (!in_array($rol, ['Productor', 'Consumidor'])) {
        $mensaje = "<div class='alert alert-danger'>Debe elegir un rol válido</div>";
    } else {
        // Insertar en la base de datos con try/catch
        try {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (nombre, correo, contrasena, rol, direccion) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nombre, $correo, $hash, $rol, $direccion]);

            // Iniciar sesión y redirigir
            $_SESSION['nombre']    = $nombre;
            $_SESSION['rol']       = $rol;
            $_SESSION['correo']    = $correo;
            $_SESSION['direccion'] = $direccion;

            header("Location: panel.php");
            exit;
        } catch (PDOException $e) {
            // Si hay un error (por ejemplo correo duplicado), mostrarlo de forma segura
            $mensaje = "<div class='alert alert-danger'>Error al registrar usuario: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Registro - AgroDirectoCR</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow w-100" style="max-width: 500px">
        <h3 class="card-title text-center mb-4">Registro en AgroDirectoCR</h3>

        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nombre completo:</label>
                <input type="text" class="form-control" name="nombre" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico:</label>
                <input type="email" class="form-control" name="correo" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" class="form-control" name="contrasena" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar contraseña:</label>
                <input type="password" class="form-control" name="contrasena_confirm" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <select class="form-select" name="rol" required>
                    <option value="">Seleccione su rol</option>
                    <option value="Productor">Productor</option>
                    <option value="Consumidor">Consumidor</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección:</label>
                <textarea class="form-control" name="direccion" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </form>
    </div>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
