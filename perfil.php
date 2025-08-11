<?php
session_start();

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

// Obtener información completa del usuario desde la base de datos
$user_id = $_SESSION['user_id'];
$usuario = null;
$mensaje = "";

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        session_destroy();
        header("Location: login.php");
        exit;
    }
} catch (PDOException $e) {
    $mensaje = "<div class='alert alert-danger'>Error al cargar perfil</div>";
}

// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar'])) {
    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    
    if (!empty($nombre)) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET nombre = ?, direccion = ? WHERE id = ?");
            $stmt->execute([$nombre, $direccion, $user_id]);
            
            // Actualizar sesión
            $_SESSION['nombre'] = $nombre;
            $_SESSION['direccion'] = $direccion;
            
            // Actualizar datos locales
            $usuario['nombre'] = $nombre;
            $usuario['direccion'] = $direccion;
            
            $mensaje = "<div class='alert alert-success'>Perfil actualizado correctamente</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar perfil</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>El nombre es obligatorio</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Perfil - AgroDirectoCR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header">
          <h3>Mi Perfil</h3>
        </div>
        <div class="card-body">
          
          <?php echo $mensaje; ?>
          
          <?php if ($usuario): ?>
            <div class="row">
              <!-- Información del perfil -->
              <div class="col-md-6">
                <h5>Información Personal</h5>
                <div class="mb-3">
                  <strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?>
                </div>
                <div class="mb-3">
                  <strong>Correo:</strong> <?php echo htmlspecialchars($usuario['correo']); ?>
                </div>
                <div class="mb-3">
                  <strong>Rol:</strong> 
                  <span class="badge <?php echo ($usuario['rol'] === 'Productor') ? 'bg-primary' : 'bg-success'; ?>">
                    <?php echo htmlspecialchars($usuario['rol']); ?>
                  </span>
                </div>
                <div class="mb-3">
                  <strong>Dirección:</strong> 
                  <?php echo !empty($usuario['direccion']) ? htmlspecialchars($usuario['direccion']) : '<em class="text-muted">No especificada</em>'; ?>
                </div>
                <div class="mb-3">
                  <strong>Miembro desde:</strong> 
                  <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?>
                </div>
              </div>
              
              <!-- Formulario de edición -->
              <div class="col-md-6">
                <h5>Editar Información</h5>
                <form method="post">
                  <div class="mb-3">
                    <label class="form-label">Nombre completo:</label>
                    <input type="text" class="form-control" name="nombre" 
                           value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Dirección:</label>
                    <textarea class="form-control" name="direccion" rows="3" 
                              placeholder="Provincia, cantón, distrito..."><?php echo htmlspecialchars($usuario['direccion'] ?? ''); ?></textarea>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label">Correo electrónico:</label>
                    <input type="email" class="form-control" 
                           value="<?php echo htmlspecialchars($usuario['correo']); ?>" disabled>
                    <div class="form-text">El correo no se puede cambiar</div>
                  </div>
                  
                  <button type="submit" name="actualizar" class="btn btn-primary">
                    Actualizar Perfil
                  </button>
                </form>
              </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Acciones adicionales -->
            <div class="row">
              <div class="col-12">
                <h5>Acciones</h5>
                <div class="d-flex gap-2 flex-wrap">
                  <a href="panel.php" class="btn btn-success">Ir al Panel</a>
                  
                  <?php if ($usuario['rol'] === 'Productor'): ?>
                    <a href="mis_productos.php" class="btn btn-info">Mis Productos</a>
                  <?php else: ?>
                    <a href="catalogo.php" class="btn btn-info">Ver Catálogo</a>
                    <a href="carrito.php" class="btn btn-warning">Mi Carrito</a>
                  <?php endif; ?>
                  
                  <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>