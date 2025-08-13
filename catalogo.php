<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Consumidor') {
    header("Location: index.php");
    exit;
}

require 'conexion.php';
include 'navbar.php';

// ==================== OBTENER CATEGORÍAS ====================
try {
    $categoriasStmt = $pdo->query("SELECT id, nombre FROM categories ORDER BY nombre ASC");
    $categorias = $categoriasStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categorias = [];
    $cat_error = $e->getMessage();
}

// ==================== VARIABLES DE FILTRO ====================
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
$categoria_id = (isset($_GET['categoria_id']) && $_GET['categoria_id'] !== '') ? intval($_GET['categoria_id']) : null;

// ==================== CONSULTA DE PRODUCTOS ====================
try {
    $sql = "
        SELECT p.id, p.nombre, p.descripcion, p.precio, p.imagen, p.stock, c.nombre as categoria_nombre
        FROM products p
        LEFT JOIN categories c ON p.categoria_id = c.id
        WHERE p.stock > 0
    ";
    $params = [];

    if ($categoria_id) {
        $sql .= " AND p.categoria_id = ?";
        $params[] = $categoria_id;
    }

    if ($busqueda !== '') {
        $sql .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ? OR c.nombre LIKE ?)";
        $like = "%{$busqueda}%";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $sql .= " ORDER BY p.created_at DESC LIMIT 40";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $productos = [];
    $prod_error = $e->getMessage();
}

// ==================== FUNCIÓN PARA CORTAR DESCRIPCIÓN ====================
function corta_texto($texto, $lim = 120) {
    if (mb_strlen($texto) <= $lim) return $texto;
    return mb_strimwidth($texto, 0, $lim, '...');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo - AgroDirectoCR</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-product { transition: transform .12s ease, box-shadow .12s ease; }
        .card-product:hover { transform: translateY(-6px); box-shadow: 0 10px 18px rgba(0,0,0,.06); }
        .img-card { height: 200px; object-fit: cover; width:100%; border-top-left-radius: .375rem; border-top-right-radius: .375rem; }
        .card-desc { height: 60px; overflow: hidden; text-overflow: ellipsis; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="mb-4">Catálogo de Productos</h2>

    <?php if (!empty($cat_error)): ?>
        <div class="alert alert-warning">No se pudieron cargar las categorías: <?php echo htmlspecialchars($cat_error); ?></div>
    <?php endif; ?>

    <!-- Formulario de búsqueda y filtro -->
    <form method="get" class="row g-3 align-items-end mb-4">
        <div class="col-md-5">
            <label class="form-label">Buscar:</label>
            <input type="text" name="busqueda" class="form-control" placeholder="Nombre, descripción o categoría" value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Categoría:</label>
            <select name="categoria_id" class="form-select">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo intval($cat['id']); ?>" <?php echo ($categoria_id == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3 d-flex">
            <button type="submit" class="btn btn-primary me-2">Filtrar</button>
            <a href="catalogo.php" class="btn btn-secondary">Limpiar</a>
        </div>
    </form>

    <?php if (!empty($prod_error)): ?>
        <div class="alert alert-danger">Error al cargar productos: <?php echo htmlspecialchars($prod_error); ?></div>
    <?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="alert alert-info">
            <h4>No hay productos disponibles</h4>
            <p>Por el momento no hay productos en stock que coincidan con los filtros.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($productos as $producto): ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card card-product h-100">
                        <?php if (!empty($producto['imagen'])): ?>
                            <img src="<?php echo htmlspecialchars($producto['imagen'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>" class="img-card" loading="lazy" onerror="this.onerror=null;this.src='https://via.placeholder.com/400x300?text=Sin+imagen';">
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                <span class="text-muted">Sin imagen</span>
                            </div>
                        <?php endif; ?>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-1"><?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <div class="text-muted small mb-2"><?php echo htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría', ENT_QUOTES, 'UTF-8'); ?></div>
                            <p class="card-desc mb-3"><?php echo htmlspecialchars(corta_texto($producto['descripcion'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold text-success">₡<?php echo number_format($producto['precio'], 2); ?></span>
                                    <small class="text-muted"><?php echo intval($producto['stock']); ?> u.</small>
                                </div>

                                <?php
                                // Construir URL de ver detalle preservando filtros
                                $params = ['id' => intval($producto['id'])];
                                if ($busqueda !== '') $params['busqueda'] = $busqueda;
                                if ($categoria_id) $params['categoria_id'] = $categoria_id;
                                $url_detalle = 'producto_detalle.php?' . http_build_query($params);
                                ?>

                                <a href="<?php echo htmlspecialchars($url_detalle, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-outline-primary w-100 mb-2">Ver más</a>

                                <form method="post" action="carrito.php">
                                    <input type="hidden" name="id" value="<?php echo intval($producto['id']); ?>">
                                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="precio" value="<?php echo htmlspecialchars($producto['precio'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" name="agregar" class="btn btn-success w-100">Añadir al Carrito</button>
                                </form>
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
