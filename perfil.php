<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil - AgroDirectoCR</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      padding-top: 80px;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">AgroDirectoCR</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link active" href="perfil.php">Perfil</a></li>
        <li class="nav-item"><a class="nav-link" href="registro.php">Login / Registro</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Perfil del Usuario -->
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card mt-5 p-4">
        <h3 class="text-center mb-4">👤 Mi Perfil</h3>
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><strong>Nombre:</strong> Keilyn Zamora</li>
          <li class="list-group-item"><strong>Correo:</strong> keilyn@ejemplo.com</li>
          <li class="list-group-item"><strong>Rol:</strong> Productora</li>
          <li class="list-group-item"><strong>Fecha de Registro:</strong> 13 de julio, 2025</li>
        </ul>
        <div class="text-center mt-4">
          <a href="#" class="btn btn-primary">Editar Perfil</a>
          <a href="#" class="btn btn-secondary">Cerrar Sesion</a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-5">
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> AgroDirectoCR. Todos los derechos reservados.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
