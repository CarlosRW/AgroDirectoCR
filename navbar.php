<?php

?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">AgroDirectoCR</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php#inicio">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php#sobre">Sobre Nosotros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="catalogo.php">Catálogo de Productos</a>
                </li>
                <?php if (isset($_SESSION['rol'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="panel.php">Panel de Usuario</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cerrar_sesion.php">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="registro.php">Login / Registro</a>
                    </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Consumidor'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">🛒 Carrito 
                            <?php if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                                <span class="badge bg-danger ms-1"><?php echo array_sum(array_column($_SESSION['carrito'], 'cantidad')); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>