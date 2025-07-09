<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = ($_POST['nombre']);
    $correo = ($_POST['correo']);
    $contrasena = ($_POST['contrasena']);
    $contrasena_confirm = ($_POST['contrasena_confirm']);
    $rol = ($_POST['rol']);
    $direccion = ($_POST['direccion']);

    //Validar datos
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<div class='alert alert-danger'>Correo inválido</div>";
    } elseif ($contrasena !== $contrasena_confirm) {
        $mensaje = "<div class='alert alert-danger'>Las contraseñas no coinciden</div>";
    } elseif (!in_array($rol, ['Productor', 'Consumidor'])) {
        $mensaje = "<div class='alert alert-danger'>Debe elegir un rol válido</div>";
    } else {
        $_SESSION['nombre'] = $nombre;
        $_SESSION['rol'] = $rol;
        $_SESSION['correo'] = $correo;
        $_SESSION['direccion'] = $direccion;

        $mensaje = "<div class='alert alert-success'>¡Registro exitoso para <strong>$nombre</strong> como <strong>$rol</strong>!</div>";
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

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow w-100" style="max-width: 500px">
        <h3 class="card-title text-center mb-4">Registro en AgroDirectoCR</h3>

        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label" for="nombre">Nombre completo:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="correo">Correo electrónico:</label>
                <input type="correo" class="form-control" id="correo" name="correo" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="contraseña">Contraseña:</label>
                <input type="password" class="form-control" id="contraseña" name="contraseña" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="contrasena_confirm">Confirmar contraseña:</label>
                <input type="password" class="form-control" id="contrasena_confirm" name="contrasena_confirm" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="rol">Rol:</label>
                <select class="form-select" id="rol" name="rol" required>
                    <option value="">Seleccione su rol</option>
                    <option value="Productor">Productor</option>
                    <option value="Consumidor">Consumidor</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label" for="direccion">Dirección:</label>
                <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">Registrarse</button>
        </form> 
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>