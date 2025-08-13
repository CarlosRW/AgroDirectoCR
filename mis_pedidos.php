<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: login.php");
    exit;
}

require 'conexion.php';
include 'navbar.php';

$productor_id = intval($_SESSION['user_id']);
$orders = [];
$error = null;

try {
    // Obtener órdenes que contienen al menos un item de productor
    $stmt = $pdo->prepare("
        SELECT DISTINCT o.id, o.user_id, o.total, o.status, o.created_at
        FROM orders o
        JOIN order_items oi ON oi.order_id = o.id
        JOIN products p ON oi.product_id = p.id
        WHERE p.productor_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$productor_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Preparar consulta para obtener los items del productor en cada orden
    $stmtItems = $pdo->prepare("
        SELECT oi.*, p.nombre AS producto_nombre
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ? AND p.productor_id = ?
    ");
} catch (PDOException $e) {
    $error = "Error al cargar pedidos: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Pedidos - AgroDirectoCR</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.card-order { transition: box-shadow .12s ease; }
.card-order:hover { box-shadow: 0 10px 20px rgba(0,0,0,.06); }
.items-scroll { max-height: 180px; overflow-y: auto; padding-right: 8px; }
.item-row { display:flex; justify-content:space-between; padding:6px 0; border-bottom:1px solid #f1f1f1; }
.item-row:last-child { border-bottom: none; }
</style>
</head>
<body>
<div class="container py-5">
    <h2 class="mb-4">Mis Pedidos</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info">No hay pedidos que contengan tus productos todavía.</div>
        <a href="panel_productor.php" class="btn btn-secondary">Volver al panel</a>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($orders as $order): ?>
                <?php
                    $stmtItems->execute([intval($order['id']), $productor_id]);
                    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
                    $subtotal_productor = 0;
                    foreach ($items as $it) $subtotal_productor += floatval($it['subtotal']);
                ?>
                <div class="col-sm-6 col-md-4">
                    <div class="card card-order h-100">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">Pedido #<?php echo intval($order['id']); ?></h5>
                            <small class="text-muted mb-2">Fecha: <?php echo htmlspecialchars($order['created_at']); ?></small>

                            <div class="items-scroll mb-3">
                                <?php if (empty($items)): ?>
                                    <div class="text-muted">No hay items tuyos en esta orden.</div>
                                <?php else: ?>
                                    <?php foreach ($items as $it): ?>
                                        <div class="item-row">
                                            <div>
                                                <div class="fw-medium"><?php echo htmlspecialchars($it['producto_nombre']); ?></div>
                                                <div class="text-muted small">₡<?php echo number_format($it['precio_unitario'],2); ?> × <?php echo intval($it['cantidad']); ?></div>
                                            </div>
                                            <div class="text-end">
                                                <div>₡<?php echo number_format($it['subtotal'],2); ?></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Tu subtotal:</small>
                                    <strong>₡<?php echo number_format($subtotal_productor,2); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Total orden:</small>
                                    <strong>₡<?php echo number_format($order['total'],2); ?></strong>
                                </div>

                                <a href="panel_productor.php" class="btn btn-outline-secondary btn-sm w-100 mt-3">Volver al Panel</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
