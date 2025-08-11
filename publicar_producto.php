<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: panel.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Publicar Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3 class="card-title mb-4"> Publicar Nuevo Producto</h3>

            <form method="post" action="guardar_producto.php">
                <div class="mb-3">
                    <label class="form-label">Nombre del producto:</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Categoría:</label>
                    <select name="categoria" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="Frutas">Frutas</option>
                        <option value="Vegetales">Vegetales</option>
                        <option value="Granos">Granos</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Precio por unidad (₡):</label>
                    <input type="number" name="precio" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Cantidad disponible:</label>
                    <input type="number" name="cantidad" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Fecha estimada de cosecha:</label>
                    <input type="date" name="fecha_cosecha" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">Publicar</button>
                <a href="panel.php" class="btn btn-secondary">Volver</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
