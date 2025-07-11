<?php
session_start();

// Productos simulados 
$productos = [
    ['id' => 1, 'nombre' => 'Tomate', 'precio' => 500, 'imagen' => 'imagenes/tomate.jpg'],
    ['id' => 2, 'nombre' => 'Lechuga', 'precio' => 300, 'imagen' => 'imagenes/lechuga.jpg'],
    ['id' => 3, 'nombre' => 'Zanahoria', 'precio' => 250, 'imagen' => 'imagenes/zanahoria.jpg'],
];

// Agregar al carrito
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Buscar producto por ID
    $producto_encontrado = null;
    foreach ($productos as $producto) {
        if ($producto['id'] == $product_id) {
            $producto_encontrado = $producto;
            break;
        }
    }

    if ($producto_encontrado) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        if (isset($_SESSION['carrito'][$product_id])) {
            $_SESSION['carrito'][$product_id]['cantidad']++;
        } else {
            $_SESSION['carrito'][$product_id] = [
                'id' => $producto_encontrado['id'],
                'nombre' => $producto_encontrado['nombre'],
                'precio' => $producto_encontrado['precio'],
                'cantidad' => 1,
                'imagen' => $producto_encontrado['imagen']
            ];
        }

        $_SESSION['mensaje_carrito'] = "{$producto_encontrado['nombre']} añadido al carrito.";
        header("Location: catalogo.php");
        exit;
    }
}

// Mostrar mensaje si existe
$mensaje_carrito = '';
if (isset($_SESSION['mensaje_carrito'])) {
    $mensaje_carrito = "<div class='alert alert-success mt-3'>" . $_SESSION['mensaje_carrito'] . "</div>";
    unset($_SESSION['mensaje_carrito']);
}
?>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h2>Catálogo de Productos</h2>
    <?php echo $mensaje_carrito; ?>

    <div class="row">
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo $producto['nombre']; ?>" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $producto['nombre']; ?></h5>
                        <p class="card-text">₡<?php echo number_format($producto['precio'], 2); ?></p>
                        <form method="post" action="catalogo.php">
                            <input type="hidden" name="product_id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" name="add_to_cart" class="btn btn-success w-100">Añadir al Carrito</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
