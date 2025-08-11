<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

$productor_id = $_SESSION['user_id'];
$productos = [];

try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM products p 
        LEFT JOIN categories c ON p.categoria_id = c.id 
        WHERE p.productor_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$productor_id]);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error al cargar productos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Productos - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Mis Productos</h2>
        <a href="publicar_producto.php" class="btn btn-success">Agregar Producto</a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">
            <h4>No tienes productos publicados</h4>
            <p>Comienza publicando tu primer producto para empezar a vender.</p>
            <a href="publicar_producto.php" class="btn btn-success">Publicar Producto</a>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                            <p class="card-text">
                                <strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría'); ?><br>
                                <strong>Precio:</strong> ₡<?php echo number_format($producto['precio'], 2); ?><br>
                                <strong>Stock:</strong> <?php echo $producto['stock']; ?> unidades
                            </p>
                            
                            <?php if ($producto['stock'] > 0): ?>
                                <span class="badge bg-success">Disponible</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Sin stock</span>
                            <?php endif; ?>
                            
                            <div class="mt-3">
                                <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('¿Estás seguro de eliminar este producto?')">Eliminar</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <div class="mt-4">
        <a href="panel_productor.php" class="btn btn-secondary">Volver al Panel</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>