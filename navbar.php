<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroDirectoCR</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Inicio</a>
                </li>

                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Consumidor'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="catalogo.php">Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">Carrito
                            <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                                <span class="badge bg-danger ms-1">
                                    <?php echo array_sum(array_column($_SESSION['carrito'], 'cantidad')); ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Productor'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="publicar_producto.php">Publicar Producto</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mis_productos.php">Mis Productos</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['nombre'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="panel.php"><?php echo htmlspecialchars($_SESSION['nombre']); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registro.php">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>