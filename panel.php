<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'Consumidor') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body text-center">
                <h3>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h3>
                <p>Rol: <strong><?php echo $_SESSION['rol']; ?></strong></p>
                <a href="catalogo.php" class="btn btn-info me-2">🔍 Ver Catálogo</a>
                <a href="mis_pedidos.php" class="btn btn-warning me-2">📦 Mis Pedidos</a>
                <a href="logout.php" class="btn btn-danger">🚪 Cerrar Sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
