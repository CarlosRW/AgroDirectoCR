<?php
session_start();
require 'conexion.php';

// Iniciar carrito
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}


// AGREGAR PRODUCTO 
if (isset($_POST['agregar']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $cantidad = intval($_POST['cantidad']);

    // Verificar el producto en la base de datos
    $stmt = $pdo->prepare("SELECT id, nombre, precio, imagen, stock FROM products WHERE id = ?");
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


// ACTUALIZAR LA CANTIDAD
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


// ELIMINAR PRODUCTO
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $_SESSION['carrito'] = array_values(array_filter($_SESSION['carrito'], function ($item) use ($id) {
        return $item['id'] != $id;
    }));
    header("Location: carrito.php");
    exit;
}


// VACIAR CARRITO
if (isset($_GET['vaciar'])) {
    $_SESSION['carrito'] = [];
    header("Location: carrito.php");
    exit;
}


// FINALIZAR LA COMPRA y crear pedido en DB
if (isset($_POST['finalizar'])) {
    // Debe existir carrito con items
    if (empty($_SESSION['carrito'])) {
        $_SESSION['checkout_error'] = "El carrito está vacío.";
        header("Location: carrito.php");
        exit;
    }

    // usuario logueado
    if (!isset($_SESSION['user_id'])) {
        // Redirigir a login/registro; opcional: guardar origen
        $_SESSION['after_login_redirect'] = 'carrito.php';
        header("Location: registro.php");
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $items = $_SESSION['carrito'];

    // Calcular el total y validar el stock en DB
    $total = 0;
    $insufficient = [];
    try {
        // Iniciar la transacción
        $pdo->beginTransaction();

        // Verificar nuevamente el stock y los precios en la base de datos para evitar manipulaciones por parte del cliente
        foreach ($items as $it) {
            $stmt = $pdo->prepare("SELECT id, precio, stock FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([intval($it['id'])]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $insufficient[] = "Producto ID {$it['id']} no existe.";
                continue;
            }
            if (intval($row['stock']) < intval($it['cantidad'])) {
                $insufficient[] = "Producto ID {$it['id']} - stock insuficiente (disponible: {$row['stock']}, solicitado: {$it['cantidad']}).";
            }
            // calcular el subtotal con el precio real de BD
            $total += floatval($row['precio']) * intval($it['cantidad']);
        }

        if (!empty($insufficient)) {
            //devolver mensaje
            $pdo->rollBack();
            $_SESSION['checkout_error'] = "No se pudo finalizar la compra: " . implode(' | ', $insufficient);
            header("Location: carrito.php");
            exit;
        }

        // Insertar orden
        $stmtOrder = $pdo->prepare("INSERT INTO orders (user_id, total, status) VALUES (?, ?, ?)");
        $stmtOrder->execute([$user_id, $total, 'pendiente']);
        $order_id = $pdo->lastInsertId();

        // Insertar items y actualizar stock
        $stmtInsertItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($items as $it) {
            // Obtener precio actual
            $stmt = $pdo->prepare("SELECT precio FROM products WHERE id = ?");
            $stmt->execute([intval($it['id'])]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $precio_unit = floatval($row['precio']);
            $cantidad = intval($it['cantidad']);
            $subtotal = $precio_unit * $cantidad;

            // Insertar item
            $stmtInsertItem->execute([$order_id, intval($it['id']), $cantidad, $precio_unit, $subtotal]);

            // Actualizar stock
            $stmtUpdateStock->execute([$cantidad, intval($it['id'])]);
        }

        // Commit
        $pdo->commit();

        // Vaciar carrito
        $_SESSION['carrito'] = [];

        // Construir URL robusta hacia compra_exitosa.php en la misma carpeta del script
        $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        $successPath = ($base === '' || $base === '/') ? '/compra_exitosa.php' : $base . '/compra_exitosa.php';
        header("Location: " . $successPath . "?order_id=" . urlencode($order_id));
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['checkout_error'] = "Error al procesar la compra: " . $e->getMessage();
        header("Location: carrito.php");
        exit;
    }
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

    <?php if (!empty($_SESSION['checkout_error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['checkout_error']); unset($_SESSION['checkout_error']); ?></div>
    <?php endif; ?>

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

                <!-- FORMULARIO para finalizar compra usa POST) -->
                <form method="post" style="display:inline-block;">
                    <input type="hidden" name="finalizar" value="1">
                    <button type="submit" class="btn btn-success">Finalizar Compra</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
