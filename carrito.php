<?php
session_start();
require 'conexion.php';

// Inicializar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Agregar producto
if (isset($_POST['agregar']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $cantidad = intval($_POST['cantidad']);

    // Verificar producto en base de datos
    $stmt = $pdo->prepare("SELECT id, nombre, precio, imagen FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
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
                'id' => $producto['id'],
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'cantidad' => $cantidad
            ];
        }
    }
    header("Location: carrito.php");
    exit;
}

// Actualizar cantidad
if (isset($_POST['actualizar']) && isset($_POST['id']) && isset($_POST['cantidad'])) {
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == intval($_POST['id'])) {
            $item['cantidad'] = max(1, intval($_POST['cantidad']));
            break;
        }
    }
    header("Location: carrito.php");
    exit;
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
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
        <div class="alert alert-info">Tu carrito está vacío. <a href="catalogo.php" class="alert-link">Ver productos</a></div>
    <?php else: ?>
        <table class="table table-bordered align-middle text-center">
            <thead class="table-success">
                <tr>
                    <th>Imagen</th>
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
                        <td>
                            <?php if (!empty($item['imagen'])): ?>
                                <img src="<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>" style="width:60px;height:60px;object-fit:cover;">
                            <?php else: ?>
                                <span class="text-muted">Sin imagen</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                        <td>₡<?php echo number_format($item['precio'], 2); ?></td>
                        <td>
                            <form method="post" class="d-flex justify-content-center">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="cantidad" value="<?php echo $item['cantidad']; ?>" min="1" class="form-control form-control-sm w-50 text-center">
                                <button type="submit" name="actualizar" class="btn btn-primary btn-sm ms-2">✔</button>
                            </form>
                        </td>
                        <td>₡<?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="carrito.php?eliminar=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm">🗑</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="4" class="text-end">Total:</th>
                    <th>₡<?php echo number_format($total, 2); ?></th>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <a href="catalogo.php" class="btn btn-outline-primary">⬅ Seguir Comprando</a>
            <div>
                <a href="carrito.php?vaciar=1" class="btn btn-outline-danger">Vaciar Carrito</a>
                <a href="checkout.php" class="btn btn-success">Finalizar Compra</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>

