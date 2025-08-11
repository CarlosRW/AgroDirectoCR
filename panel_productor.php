<?php
session_start();
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: login.php");
    exit();
}

require 'conexion.php';

// Obtener estadísticas del productor
$mis_productos = 0;
$productos_vendidos = 0;
$ingresos_totales = 0;

try {
    $user_id = $_SESSION['user_id'];
    
    // Contar mis productos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE productor_id = ?");
    $stmt->execute([$user_id]);
    $mis_productos = $stmt->fetchColumn();
    
    // Por ahora simular algunas estadísticas
    $productos_vendidos = rand(5, 25);
    $ingresos_totales = rand(50000, 250000);
    
} catch (PDOException $e) {
    error_log("Error al obtener estadísticas del productor: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Productor - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container mt-5">
        <h2>Panel del Productor</h2>

        <!-- Saludo personalizado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h2><i class="bi bi-person-badge"></i> ¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
                        <p class="mb-0">Administra tu negocio y conecta con consumidores locales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas resumidas -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Mis Productos</h5>
                        <h3><?php echo $mis_productos; ?></h3>
                        <p class="text-muted">productos publicados</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Vendidos</h5>
                        <h3><?php echo $productos_vendidos; ?></h3>
                        <p class="text-muted">este mes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5>Ingresos</h5>
                        <h3>₡<?php echo number_format($ingresos_totales); ?></h3>
                        <p class="text-muted">total</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Publicar Producto</h5>
                        <p>Añade nuevos productos para vender</p>
                        <a href="publicar_producto.php" class="btn btn-success">Nuevo Producto</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Mis Productos</h5>
                        <p>Ver y editar mis productos</p>
                        <a href="mis_productos.php" class="btn btn-primary">Ver Productos</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Pedidos</h5>
                        <p>Revisar pedidos de clientes</p>
                        <a href="pedidos_recibidos.php" class="btn btn-info">Ver Pedidos</a>
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