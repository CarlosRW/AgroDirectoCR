<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Consumidor') {
    header("Location: index.php");
    exit;
}

require 'conexion.php';
include 'navbar.php';

// Obtener productos desde la base de datos
try {
    $stmt = $pdo->query("SELECT * FROM products LIMIT 40");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error al cargar productos: " . $e->getMessage() . "</div>";
    $productos = [];
}

<div class="container mt-5">
    <h2>Catálogo de Productos</h2>

    <div class="row">
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="<?php echo htmlspecialchars($producto['imagen']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                             style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <img src="imagenes/no-image.jpg" 
                             class="card-img-top" 
                             alt="Sin imagen" 
                             style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                        <p class="card-text fw-bold">₡<?php echo number_format($producto['precio'], 2); ?></p>
                        
                        <form method="post" action="carrito.php" class="mt-auto">
                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                            <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" name="agregar" class="btn btn-success w-100">
                                Añadir al Carrito
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>
