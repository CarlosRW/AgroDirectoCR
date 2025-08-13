<?php
session_start();
require 'conexion.php';

$mensaje = "";
$correo_anterior = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $pass = $_POST['password'] ?? '';
    $correo_anterior = $correo; // Para mantener el valor en el formulario

    if (empty($correo) || empty($pass)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios</div>";
    } else {
        try {
            // Nota: Cambiado 'contrasena' por 'password'
            $stmt = $pdo->prepare("SELECT id, nombre, correo, password, rol, direccion FROM users WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($pass, $usuario['password'])) {
                // Iniciar sesión
                $_SESSION['user_id']   = $usuario['id'];
                $_SESSION['nombre']    = $usuario['nombre'];
                $_SESSION['rol']       = $usuario['rol'];
                $_SESSION['correo']    = $usuario['correo'];
                $_SESSION['direccion'] = $usuario['direccion'];

                // Redirigir al panel general que redirigirá según el rol
                header("Location: panel.php");
                exit;
            } else {
                $mensaje = "<div class='alert alert-danger'>Correo o contraseña incorrectos</div>";
            }
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error del servidor. Intente nuevamente.</div>";
            error_log("Error de login: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Iniciar Sesión - AgroDirectoCR</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow w-100" style="max-width: 400px">
        <h3 class="card-title text-center mb-4">Iniciar Sesión</h3>
        
        <!-- Muestra cuando alguien intenta acceder al catálogo sin estar logueado. -->
        <?php if (!empty($mensaje)) echo $mensaje; ?>
        
        <?php if (isset($_SESSION['mensaje_catalogo'])): ?>
            <div class="alert alert-info">
                <?php echo htmlspecialchars($_SESSION['mensaje_catalogo']); ?>
                <?php unset($_SESSION['mensaje_catalogo']); ?>
            </div>
        <?php endif; ?>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" name="correo" 
                       value="<?php echo htmlspecialchars($correo_anterior); ?>" 
                       required autofocus>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            
            <button type="submit" class="btn btn-success w-100 mb-3">
                Entrar
            </button>
            
            <div class="text-center">
                <p class="mb-0">¿No tienes cuenta? 
                    <a href="registro.php" class="text-success">Regístrate aquí</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>