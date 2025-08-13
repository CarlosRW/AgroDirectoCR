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
$mis_pedidos = 0;

try {
    $user_id = $_SESSION['user_id'];
    
    // Contar mis productos
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE productor_id = ?");
    $stmt->execute([$user_id]);
    $mis_productos = (int) $stmt->fetchColumn();
    
    // Contar mis pedidos que contienen productos de este productor
    $stmt2 = $pdo->prepare("
        SELECT COUNT(DISTINCT oi.order_id) AS pedidos_count
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE p.productor_id = ?
    ");
    $stmt2->execute([$user_id]);
    $mis_pedidos = (int) $stmt2->fetchColumn();
    
    // Valores simulados para ventas e ingresos
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
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .card-quick { min-height: 150px; }
    </style>
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
                        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?>!</h2>
                        <p class="mb-0">Administra tu negocio y conecta con consumidores locales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas resumidas -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-quick">
                    <div class="card-body text-center">
                        <h5>Mis Productos</h5>
                        <h3><?php echo $mis_productos; ?></h3>
                        <p class="text-muted">productos publicados</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card card-quick">
                    <div class="card-body text-center">
                        <h5>Vendidos</h5>
                        <h3><?php echo $productos_vendidos; ?></h3>
                        <p class="text-muted">este mes</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-3">
                <div class="card card-quick">
                    <div class="card-body text-center">
                        <h5>Ingresos</h5>
                        <h3>₡<?php echo number_format($ingresos_totales); ?></h3>
                        <p class="text-muted">total</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flashcards principales alineadas y botones visibles -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5>Publicar Producto</h5>
                        <p class="flex-grow-1">Añade nuevos productos para vender</p>
                        <a href="publicar_producto.php" class="btn btn-success mt-auto">Nuevo Producto</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5>Mis Productos</h5>
                        <p class="flex-grow-1">Ver y editar mis productos publicados</p>
                        <a href="mis_productos.php" class="btn btn-primary mt-auto">Ver Productos</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5>Mis Pedidos</h5>
                        <p class="flex-grow-1">Pedidos que incluyen tus productos</p>
                        <a href="mis_pedidos.php" class="btn btn-info mt-auto">Ver Pedidos (<?php echo $mis_pedidos; ?>)</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5>Mi Perfil</h5>
                        <p class="flex-grow-1">Actualizar información personal</p>
                        <a href="perfil.php" class="btn btn-secondary mt-auto">Ver Perfil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
