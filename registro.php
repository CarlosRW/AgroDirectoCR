<?php
session_start();
require 'conexion.php';

$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['password_confirm'] ?? '';
    $rol      = $_POST['rol'] ?? '';
    $direccion= trim($_POST['direccion'] ?? '');

    // Validaciones
    if (empty($nombre)) {
        $mensaje = "<div class='alert alert-danger'>El nombre es obligatorio</div>";
    } elseif (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<div class='alert alert-danger'>Correo electrónico inválido</div>";
    } elseif (strlen($pass) < 6) {
        $mensaje = "<div class='alert alert-danger'>La contraseña debe tener al menos 6 caracteres</div>";
    } elseif ($pass !== $pass2) {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas no coinciden</div>";
    } elseif (!in_array($rol, ['Productor', 'Consumidor'])) {
        $mensaje = "<div class='alert alert-danger'>Debe elegir un rol válido</div>";
    } else {
        // Verificar si el correo ya existe
        try {
            $stmt_check = $pdo->prepare("SELECT id FROM users WHERE correo = ?");
            $stmt_check->execute([$correo]);
            
            if ($stmt_check->fetch()) {
                $mensaje = "<div class='alert alert-danger'>Este correo ya está registrado</div>";
            } else {
                // Insertar en la base de datos
                $hash = password_hash($pass, PASSWORD_DEFAULT);

                // Nota: Cambiado 'contrasena' por 'password'
                $stmt = $pdo->prepare(
                    "INSERT INTO users (nombre, correo, password, rol, direccion) VALUES (?, ?, ?, ?, ?)"
                );
                $stmt->execute([$nombre, $correo, $hash, $rol, $direccion]);

                // Iniciar sesión automáticamente
                $_SESSION['user_id']   = $pdo->lastInsertId();
                $_SESSION['nombre']    = $nombre;
                $_SESSION['rol']       = $rol;
                $_SESSION['correo']    = $correo;
                $_SESSION['direccion'] = $direccion;

                // Redirigir al panel general
                header("Location: panel.php");
                exit;
            }
        } catch (PDOException $e) {
            // Error más específico
            if ($e->getCode() == 23000) { // Duplicate entry
                $mensaje = "<div class='alert alert-danger'>Este correo ya está registrado</div>";
            } else {
                $mensaje = "<div class='alert alert-danger'>Error del servidor. Intente nuevamente.</div>";
                // Log del error para debugging (no mostrar al usuario)
                error_log("Error de registro: " . $e->getMessage());
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
<title>Registro - AgroDirectoCR</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow w-100" style="max-width: 500px">
        <h3 class="card-title text-center mb-4">Registro en AgroDirectoCR</h3>

        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <form method="post" id="registroForm">
            <div class="mb-3">
                <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nombre" 
                       value="<?php echo htmlspecialchars($nombre ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                <input type="email" class="form-control" name="correo" 
                       value="<?php echo htmlspecialchars($correo ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Contraseña <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password" 
                       minlength="6" required>
                <div class="form-text">Mínimo 6 caracteres</div>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirmar contraseña <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="password_confirm" 
                       minlength="6" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Tipo de usuario <span class="text-danger">*</span></label>
                <select class="form-select" name="rol" required>
                    <option value="">Seleccione su rol</option>
                    <option value="Productor" <?php echo (($rol ?? '') === 'Productor') ? 'selected' : ''; ?>>
                        Productor - Vendo mis productos
                    </option>
                    <option value="Consumidor" <?php echo (($rol ?? '') === 'Consumidor') ? 'selected' : ''; ?>>
                        Consumidor - Compro productos
                    </option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Dirección</label>
                <textarea class="form-control" name="direccion" rows="2" 
                          placeholder="Provincia, cantón, distrito..."><?php echo htmlspecialchars($direccion ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100 mb-3">
                Crear cuenta
            </button>
            
            <div class="text-center">
                <p class="mb-0">¿Ya tienes cuenta? 
                    <a href="login.php" class="text-success">Inicia sesión aquí</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    
// Validación adicional en el frontend
document.getElementById('registroForm').addEventListener('submit', function(e) {
    const pass = document.querySelector('[name="password"]').value;
    const passConfirm = document.querySelector('[name="password_confirm"]').value;
    
    if (pass !== passConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }
});
</script>
</body>
</html>