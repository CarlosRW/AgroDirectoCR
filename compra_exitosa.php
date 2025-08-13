<?php
session_start();
require 'conexion.php';
include 'navbar.php';

$order = null;
$order_items = [];

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    try {
        // Obtener orden
        $stmt = $pdo->prepare("SELECT o.*, u.nombre AS cliente_nombre FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ? LIMIT 1");
        $stmt->execute([$order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener items
        if ($order) {
            $stmt2 = $pdo->prepare("SELECT oi.*, p.nombre AS producto_nombre, p.imagen AS producto_imagen FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
            $stmt2->execute([$order_id]);
            $order_items = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = "Error al cargar la orden: " . $e->getMessage();
    }
} else {
    $error = "No se encontró número de orden.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Compra realizada - AgroDirectoCR</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <a href="catalogo.php" class="btn btn-primary">Volver al catálogo</a>
    <?php else: ?>
        <div class="text-center mb-4">
            <h2 class="text-success">✅ ¡Pedido realizado con éxito!</h2>
            <p class="mb-1">Número de pedido: <strong>#<?php echo htmlspecialchars($order['id']); ?></strong></p>
            <p class="mb-0">Total: <strong>₡<?php echo number_format($order['total'], 2); ?></strong></p>
            <p class="text-muted">Estado: <?php echo htmlspecialchars($order['status']); ?></p>
        </div>

        <h4>Resumen del pedido</h4>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Producto</th>
                    <th>Precio unit.</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $it): ?>
                    <tr>
                        <td>
                            <?php if (!empty($it['producto_imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($it['producto_imagen']); ?>" alt="" style="width:50px;height:50px;object-fit:cover;margin-right:8px;">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($it['producto_nombre']); ?>
                        </td>
                        <td>₡<?php echo number_format($it['precio_unitario'], 2); ?></td>
                        <td><?php echo intval($it['cantidad']); ?></td>
                        <td>₡<?php echo number_format($it['subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="mt-4 text-center">
            <a href="catalogo.php" class="btn btn-primary">Seguir comprando</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'footer.php'; ?>
</body>
</html>
