<?php
session_start();

// Eliminar producto del carrito
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_item'])) {
    $product_id = $_POST['remove_item'];
    if (isset($_SESSION['carrito'][$product_id])) {
        unset($_SESSION['carrito'][$product_id]);
    }
}
?>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>🛒 Carrito de Compras</h2>

    <?php if (empty($_SESSION['carrito'])): ?>
        <p class="alert alert-info mt-4">Tu carrito está vacío.</p>
    <?php else: ?>
        <table class="table table-bordered mt-4">
            <thead class="table-success">
                <tr>
                    <th>Imagen</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_carrito = 0;
                foreach ($_SESSION['carrito'] as $producto):
                    $subtotal = $producto['precio'] * $producto['cantidad'];
                    $total_carrito += $subtotal;
                ?>
                <tr>
                    <td><img src="<?php echo $producto['imagen']; ?>" width="60" height="60" style="object-fit:cover;"></td>
                    <td><?php echo $producto['nombre']; ?></td>
                    <td>₡<?php echo number_format($producto['precio'], 2); ?></td>
                    <td><?php echo $producto['cantidad']; ?></td>
                    <td>₡<?php echo number_format($subtotal, 2); ?></td>
                    <td>
                        <form method="post" action="carrito.php">
                            <button type="submit" name="remove_item" value="<?php echo $producto['id']; ?>" class="btn btn-danger btn-sm">Eliminar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-end">
            <h4>Total: ₡<?php echo number_format($total_carrito, 2); ?></h4>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
