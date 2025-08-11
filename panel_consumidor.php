<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'Consumidor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Obtener estadísticas del consumidor
$productos_disponibles = 0;
$items_en_carrito = 0;

try {
    // Contar productos disponibles
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE stock > 0");
    $productos_disponibles = $stmt->fetchColumn();
    
    // Contar items en carrito
    if (isset($_SESSION['carrito'])) {
        $items_en_carrito = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
    }
} catch (PDOException $e) {
    error_log("Error al obtener estadísticas: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Consumidor - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <!-- Saludo personalizado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h2><i class="bi bi-person-circle"></i> ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
                        <p class="mb-0">Disfruta de productos frescos directamente del campo a tu mesa</p>
                    </div>
                </div>
            </div>
        </div>
            
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Productos Disponibles</h5>
                        <h3><?php echo $productos_disponibles; ?></h3>
                        <p class="text-muted">productos en stock</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Mi Carrito</h5>
                        <h3><?php echo $items_en_carrito; ?></h3>
                        <p class="text-muted">items agregados</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Productos Locales</h5>
                        <h3>100%</h3>
                        <p class="text-muted">frescos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Ver Productos</h5>
                        <p>Explora todos los productos disponibles</p>
                        <a href="catalogo.php" class="btn btn-primary">Ver Catálogo</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Mi Carrito</h5>
                        <p>Revisar productos en el carrito</p>
                        <a href="carrito.php" class="btn btn-success">
                            Ver Carrito
                            <?php if ($items_en_carrito > 0): ?>
                                <span class="badge bg-danger ms-1"><?php echo $items_en_carrito; ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Mis Pedidos</h5>
                        <p>Historial de compras realizadas</p>
                        <a href="mis_pedidos.php" class="btn btn-info">Ver Pedidos</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Mi Perfil</h5>
                        <p>Actualizar información personal</p>
                        <a href="perfil.php" class="btn btn-secondary">Ver Perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>