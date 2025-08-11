<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

$productor_id = $_SESSION['user_id'];
$producto_id = $_GET['id'] ?? 0;
$mensaje = "";

// Obtener datos del producto
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM products p 
        LEFT JOIN categories c ON p.categoria_id = c.id 
        WHERE p.id = ? AND p.productor_id = ?
    ");
    $stmt->execute([$producto_id, $productor_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$producto) {
        header("Location: mis_productos.php");
        exit;
    }
} catch (PDOException $e) {
    header("Location: mis_productos.php");
    exit;
}

// Procesar formulario de actualización
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria = $_POST['categoria'];
    
    if (empty($nombre) || $precio <= 0 || $stock < 0 || empty($categoria)) {
        $mensaje = "<div class='alert alert-danger'>Todos los campos son obligatorios y deben ser válidos</div>";
    } else {
        try {
            // Buscar o crear categoría
            $stmt = $pdo->prepare("SELECT id FROM categories WHERE nombre = ?");
            $stmt->execute([$categoria]);
            $cat = $stmt->fetch();
            
            if (!$cat) {
                $stmt = $pdo->prepare("INSERT INTO categories (nombre) VALUES (?)");
                $stmt->execute([$categoria]);
                $categoria_id = $pdo->lastInsertId();
            } else {
                $categoria_id = $cat['id'];
            }
            
            // Actualizar producto
            $stmt = $pdo->prepare("
                UPDATE products 
                SET nombre = ?, precio = ?, stock = ?, categoria_id = ? 
                WHERE id = ? AND productor_id = ?
            ");
            
            $stmt->execute([$nombre, $precio, $stock, $categoria_id, $producto_id, $productor_id]);
            
            $mensaje = "<div class='alert alert-success'>Producto actualizado correctamente</div>";
            
            // Actualizar datos para mostrar
            $producto['nombre'] = $nombre;
            $producto['precio'] = $precio;
            $producto['stock'] = $stock;
            $producto['categoria_nombre'] = $categoria;
            
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar el producto</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - AgroDirectoCR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>Editar Producto</h3>
                    
                    <?php echo $mensaje; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Nombre del producto:</label>
                            <input type="text" name="nombre" class="form-control" 
                                   value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Categoría:</label>
                            <select name="categoria" class="form-select" required>
                                <option value="">Seleccione...</option>
                                <option value="Frutas" <?php echo ($producto['categoria_nombre'] === 'Frutas') ? 'selected' : ''; ?>>Frutas</option>
                                <option value="Vegetales" <?php echo ($producto['categoria_nombre'] === 'Vegetales') ? 'selected' : ''; ?>>Vegetales</option>
                                <option value="Granos" <?php echo ($producto['categoria_nombre'] === 'Granos') ? 'selected' : ''; ?>>Granos</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Precio por unidad (colones):</label>
                            <input type="number" name="precio" class="form-control" min="1" 
                                   value="<?php echo $producto['precio']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Cantidad en stock:</label>
                            <input type="number" name="stock" class="form-control" min="0" 
                                   value="<?php echo $producto['stock']; ?>" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Actualizar Producto</button>
                            <a href="mis_productos.php" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>