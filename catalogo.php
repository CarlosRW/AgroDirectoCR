<?php
session_start();

// Simulación de productos 
$productos = [
    ['id' => 1, 'nombre' => 'Fresas Orgánicas', 'categoria' => 'Frutas', 'precio' => 2500, 'cantidad_disponible' => 10, 'imagen' => 'img/frutasTemp.png', 'descripcion' => 'Fresas frescas de temporada, cultivadas localmente.'],
    ['id' => 2, 'nombre' => 'Tomates Cherry', 'categoria' => 'Vegetales', 'precio' => 1800, 'cantidad_disponible' => 15, 'imagen' => 'img/vegetalesOrg.png', 'descripcion' => 'Tomates cherry orgánicos, ideales para ensaladas.'],
    ['id' => 3, 'nombre' => 'Frijoles Negros', 'categoria' => 'Granos', 'precio' => 1200, 'cantidad_disponible' => 50, 'imagen' => 'img/granosBasicos.png', 'descripcion' => 'Frijoles negros de alta calidad, cosechados en la zona de Guanacaste.'],
    ['id' => 4, 'nombre' => 'Mangos Maduros', 'categoria' => 'Frutas', 'precio' => 3000, 'cantidad_disponible' => 8, 'imagen' => 'https://via.placeholder.com/200x200/FFD700/000000?text=Mango', 'descripcion' => 'Deliciosos mangos maduros listos para disfrutar.'],
    ['id' => 5, 'nombre' => 'Lechuga Romana', 'categoria' => 'Vegetales', 'precio' => 900, 'cantidad_disponible' => 20, 'imagen' => 'https://via.placeholder.com/200x200/8BC34A/FFFFFF?text=Lechuga', 'descripcion' => 'Lechuga fresca y crujiente, ideal para cualquier ensalada.'],
];

// Lógica para añadir al carrito 
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $cantidad = 1;  

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $producto_encontrado = null;
    foreach ($productos as $p) {
        if ($p['id'] == $product_id) {
            $producto_encontrado = $p;
            break;
        }
    }

    if ($producto_encontrado) {
        if (isset($_SESSION['carrito'][$product_id])) {
            $_SESSION['carrito'][$product_id]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$product_id] = [
                'id' => $producto_encontrado['id'],
                'nombre' => $producto_encontrado['nombre'],
                'precio' => $producto_encontrado['precio'],
                'cantidad' => $cantidad,
                'imagen' => $producto_encontrado['imagen']
            ];
        }
        $mensaje_carrito = "<div class='alert alert-success mt-3'>'{$producto_encontrado['nombre']}' añadido al carrito.</div>";
    } else {
        $mensaje_carrito = "<div class='alert alert-danger mt-3'>Producto no encontrado.</div>";
    }
}

// lógica para filtros 
$productos_filtrados = $productos; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .product-card {
            transition: transform .2s;
        }
        .product-card:hover {
            transform: scale(1.03);
        }
        .product-card img {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body class="bg-light">

<?php include 'navbar.php'; //  barra de navegación ?>

<section id="catalogo" class="py-5 mt-5">
    <div class="container">
        <h2 class="text-center mb-4">Explora Nuestros Productos Frescos</h2>
        <?php if (!empty($mensaje_carrito)) echo $mensaje_carrito; ?>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card p-3 shadow-sm">
                    <h5>Filtros (en desarrollo)</h5>
                    <form class="row g-3">
                        <div class="col-md-4">
                            <label for="categoria" class="form-label visually-hidden">Categoría</label>
                            <select id="categoria" class="form-select">
                                <option selected>Todas las categorías</option>
                                <option>Frutas</option>
                                <option>Vegetales</option>
                                <option>Granos</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ubicacion" class="form-label visually-hidden">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" placeholder="Buscar por ubicación (ej. Cartago)">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-success w-100">Aplicar Filtros</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <?php foreach ($productos_filtrados as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card product-card shadow-sm h-100">
                    <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                        <p class="card-text text-muted"><?php echo htmlspecialchars($producto['categoria']); ?></p>
                        <p class="card-text flex-grow-1"><?php echo htmlspecialchars($producto['descripcion']); ?></p>
                        <p class="card-text fs-5 fw-bold text-success">₡<?php echo number_format($producto['precio'], 0, ',', '.'); ?></p>
                        <p class="card-text"><small class="text-muted">Cantidad disponible: <?php echo htmlspecialchars($producto['cantidad_disponible']); ?></p></small>
                        
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'Consumidor'): ?>
                            <form method="post" action="">
                                <input type="hidden" name="product_id" value="<?php echo $producto['id']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-success w-100 mt-2">🛒 Añadir al Carrito</button>
                            </form>
                        <?php elseif (!isset($_SESSION['rol'])): ?>
                            <a href="registro.php" class="btn btn-outline-success w-100 mt-2">Inicia Sesión para Comprar</a>
                        <?php else: ?>
                            <button class="btn btn-secondary w-100 mt-2" disabled>Solo Consumidores pueden Comprar</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($productos_filtrados)): ?>
                <div class="col-12 text-center">
                    <p class="lead">No hay productos disponibles en este momento.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; // el footer  ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>