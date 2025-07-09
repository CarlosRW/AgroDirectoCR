<?php
session_start();

if (!isset($_SESSION['rol'])) {
    header("Location: registro.php");
    exit;
}

$nombre = $_SESSION['nombre'];
$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Panel Principal - AgroDirectoCR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title">¡Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h3>
            <p class="lead">Rol: <strong><?php echo $rol; ?></strong></p>

            <?php if ($rol === 'Productor'): ?>
                <a href="publicar_producto.php" class="btn btn-success mb-2">📦 Publicar Producto</a>
                <a href="#" class="btn btn-primary mb-2">📋 Ver Pedidos</a>
            <?php else: ?>
                <a href="#" class="btn btn-info mb-2">🛍️ Ver Catálogo</a>
                <a href="#" class="btn btn-warning mb-2">📦 Mis Pedidos</a>
            <?php endif; ?>

            <a href="cerrar_sesion.php" class="btn btn-danger">🚪 Cerrar Sesión</a>
        </div>
    </div>
</div>
</body>
</html>
