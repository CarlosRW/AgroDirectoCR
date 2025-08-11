<?php
session_start();

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto
if (isset($_POST['agregar'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];

    $encontrado = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == $id) {
            $item['cantidad'] += $cantidad;
            $encontrado = true;
            break;
        }
    }
    if (!$encontrado) {
        $_SESSION['carrito'][] = [
            'id' => $id,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];
    }
    header("Location: carrito.php");
    exit;
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $_SESSION['carrito'] = array_filter($_SESSION['carrito'], function ($item) use ($id) {
        return $item['id'] != $id;
    });
}

// Vaciar carrito
if (isset($_GET['vaciar'])) {
    $_SESSION['carrito'] = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carrito de Compras</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container py-5">
    <h2 class="mb-4">Carrito de Compras</h2>
    <?php if (empty($_SESSION['carrito'])): ?>
        <div class="alert alert-info">Tu carrito está vacío.</div>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; ?>
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <?php $subtotal = $item['precio'] * $item['cantidad']; ?>
                    <?php $total += $subtotal; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                        <td>₡<?php echo number_format($item['precio'], 2); ?></td>
                        <td><?php echo $item['cantidad']; ?></td>
                        <td>₡<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="carrito.php?eliminar=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" class="text-end">Total:</th>
                    <th>₡<?php echo number_format($total, 2); ?></th>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <a href="carrito.php?vaciar=1" class="btn btn-outline-danger">Vaciar Carrito</a>
            <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
