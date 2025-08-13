<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>AgroDirectoCR</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
section {
  padding: 80px 0;
}
.product-card {
  transition: transform .2s;
}
.product-card:hover {
  transform: scale(1.03);
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">AgroDirectoCR</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="#inicio">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#sobre">Sobre Nosotros</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#productos">Destacados</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="catalogo.php">Productos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="registro.php">Login / Registro</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="perfil.php">Perfil</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Inicio -->
<section id="inicio" class="bg-light text-center">
  <div class="container">
    <h1 class="mb-4">🌾 Bienvenido a AgroDirectoCR</h1>
    <p class="lead">Conectamos directamente a productores agrícolas con consumidores finales en Costa Rica.  
    Apoya lo local y recibe productos frescos sin intermediarios.</p>

    <?php if (isset($_SESSION['nombre'])): ?>
      <p class="fs-5 text-success">
        Sesión iniciada como <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong> (<?php echo htmlspecialchars($_SESSION['rol']); ?>)
      </p>
      <a href="panel.php" class="btn btn-primary btn-lg">Ir al Panel</a>
      <a href="cerrar_sesion.php" class="btn btn-danger btn-lg">Cerrar Sesión</a>
    <?php else: ?>
      <a href="registro.php" class="btn btn-success btn-lg">Regístrate ahora</a>
    <?php endif; ?>
  </div>
</section>

<!-- Sobre Nosotros -->
<section id="sobre" class="nosotros-section">
  <div class="container">
    <h2>Sobre Nosotros</h2>
    <p class="lead">AgroDirectoCR nació con la misión de apoyar a los pequeños y medianos productores agrícolas costarricenses, brindándoles un canal digital para comercializar sus productos sin intermediarios.</p>
    <div class="nosotros-flex">
      <div class="nosotros-card">
        <h3>🌱 Comercio Justo</h3>
        <p>Garantizamos precios justos para los productores y accesibles para los consumidores.</p>
      </div>
      <div class="nosotros-card">
        <h3>🍎 Frescura Garantizada</h3>
        <p>Los productos se entregan frescos, directamente de las fincas a tu mesa.</p>
      </div>
      <div class="nosotros-card">
        <h3>📍 Apoyo Local</h3>
        <p>Fortalecemos las economías rurales y apoyamos a las comunidades agrícolas del país.</p>
      </div>
      <div class="nosotros-card">
        <h3>💻 Tecnología Accesible</h3>
        <p>Una plataforma intuitiva y fácil de usar para productores y consumidores.</p>
      </div>
    </div>
  </div>
</section>

<!-- Productos Destacados -->
<section id="productos" class="bg-light text-center">
  <div class="container">
    <h2 class="mb-4">Productos Destacados</h2>
    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card product-card shadow-sm">
          <img src="img/frutasTemp.png" class="card-img-top" alt="Frutas">
          <div class="card-body">
            <h5 class="card-title">Frutas de Temporada</h5>
            <p class="card-text">Las mejores frutas frescas directamente desde la finca a tu mesa.</p>
            <button class="btn btn-success btn-sm">Comprar</button>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card product-card shadow-sm">
          <img src="img/vegetalesOrg.png" class="card-img-top" alt="Vegetales">
          <div class="card-body">
            <h5 class="card-title">Vegetales Orgánicos</h5>
            <p class="card-text">Cultivados con técnicas sostenibles, saludables y sabrosos.</p>
            <button class="btn btn-success btn-sm">Comprar</button>
          </div>
        </div>
      </div>
      <div class="col-md-4 mb-4">
        <div class="card product-card shadow-sm">
          <img src="img/granosBasicos.png" class="card-img-top" alt="Granos">
          <div class="card-body">
            <h5 class="card-title">Granos Básicos</h5>
            <p class="card-text">Alimentos esenciales de alta calidad producidos localmente.</p>
            <button class="btn btn-success btn-sm">Comprar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
  <div class="container">
    <p>&copy; <?php echo date('Y'); ?> AgroDirectoCR. Todos los derechos reservados.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
