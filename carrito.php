<?php
session_start();

// Lógica para actualizar o eliminar productos del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['cantidad'] as $product_id => $cantidad) {
            if (isset($_SESSION['carrito'][$product_id])) {
                $cantidad = max(0, (int)$cantidad); 
                
                if ($cantidad == 0) {
                    unset($_SESSION['carrito'][$product_id]);
                } else {
                    $_SESSION['carrito'][$product_id]['cantidad'] = $cantidad;
                }
            }
        }
        $mensaje_carrito = "<div class='alert alert-success mt-3'>Carrito actualizado.</div>";
    } elseif (isset($_POST['remove_item'])) {
        $product_id = $_POST['product_id'];
        if (isset($_SESSION['carrito'][$product_id])) {
            unset($_SESSION['carrito'][$product_id]);
            $mensaje_carrito = "<div class='alert alert-success mt-3'>Producto eliminado del carrito.</div>";
        }
    }
}

$carrito_vacio = !isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0;
$total_carrito = 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-light">

<?php include 'navbar.php'; ?>

<section id="carrito" class="py-5 mt-5">
    <div class="container">
        <h2 class="text-center mb-4">🛒 Tu Carrito de Compras</h2>
        <?php if (!empty($mensaje_carrito)) echo $mensaje_carrito; ?>

        <?php if ($carrito_vacio): ?>
            <div class="alert alert-info text-center" role="alert">
                Tu carrito está vacío. <a href="catalogo.php" class="alert-link">Explora nuestros productos</a> para empezar a comprar.
            </div>
        <?php else: ?>
            <form method="post" action="">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">Producto</th>
                                <th scope="col">Precio Unitario</th>
                                <th scope="col">Cantidad</th>
                                <th scope="col">Subtotal</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($_SESSION['carrito'] as $product_id => $item): ?>
                                <?php
                                $subtotal = $item['precio'] * $item['cantidad'];
                                $total_carrito += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php echo htmlspecialchars($item['nombre']); ?>
                                    </td>
                                    <td>₡<?php echo number_format($item['precio'], 0, ',', '.'); ?></td>
                                    <td>
                                        <input type="number" name="cantidad[<?php echo $product_id; ?>]" value="<?php echo htmlspecialchars($item['cantidad']); ?>" min="0" class="form-control form-control-sm w-auto d-inline-block">
                                    </td>
                                    <td>₡<?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                                    <td>
                                        <button type="submit" name="remove_item" value="<?php echo $product_id; ?>" class="btn btn-danger btn-sm">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total del Carrito:</th>
                                <th colspan="2">₡<?php echo number_format($total_carrito, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="catalogo.php" class="btn btn-secondary">← Seguir Comprando</a>
                    <button type="submit" name="update_cart" class="btn btn-info">Actualizar Carrito</button>
                    <a href="realizar_pedido.php" class="btn btn-success">Finalizar Pedido →</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>