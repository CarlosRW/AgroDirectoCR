<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Consumidor') {
    header("Location: index.php");
    exit;
}

require 'conexion.php';
include 'navbar.php';

// Obtener los parámetros de retorno para volver con los filtros aplicados
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : '';

// Obtener producto por ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM products p
        LEFT JOIN categories c ON p.categoria_id = c.id
        WHERE p.id = ? LIMIT 1
    ");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al cargar el producto: " . $e->getMessage() . "</div>";
    $producto = null;
}

// URL de retorno para el catálogo con filtros
$url_retorno = "catalogo.php";
$params = [];
if ($busqueda !== '') $params[] = "busqueda=" . urlencode($busqueda);
if ($categoria_id !== '') $params[] = "categoria_id=" . urlencode($categoria_id);
if (!empty($params)) {
    $url_retorno .= "?" . implode("&", $params);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Producto - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <?php if (!$producto): ?>
        <div class="alert alert-warning">Producto no encontrado.</div>
        <a href="<?php echo $url_retorno; ?>" class="btn btn-secondary">Volver al Catálogo</a>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <?php if (!empty($producto['imagen'])): ?>
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                         class="img-fluid rounded" 
                         alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                        <span class="text-muted">Sin imagen</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <h2><?php echo htmlspecialchars($producto['nombre']); ?></h2>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría'); ?></p>
                <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
                <p class="fs-4 text-success"><strong>₡<?php echo number_format($producto['precio'], 2); ?></strong></p>
                <p><strong>Stock disponible:</strong> <?php echo $producto['stock']; ?> unidades</p>

                <form method="post" action="carrito.php">
                    <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo $producto['stock']; ?>" class="form-control mb-3" required>
                    <button type="submit" name="agregar" class="btn btn-success w-100">Añadir al Carrito</button>
                </form>

                <a href="<?php echo $url_retorno; ?>" class="btn btn-secondary w-100 mt-2">Volver al Catálogo</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
