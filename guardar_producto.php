<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productor_id = $_SESSION['user_id'];
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $cantidad = intval($_POST['cantidad']);
    $categoria = $_POST['categoria'];
    
    // Validaciones básicas
    if (empty($nombre) || $precio <= 0 || $cantidad < 0 || empty($categoria)) {
        header("Location: publicar_producto.php?error=1");
        exit;
    }
    
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
        
        // Insertar producto
        $stmt = $pdo->prepare("
            INSERT INTO products (productor_id, nombre, precio, stock, categoria_id) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([$productor_id, $nombre, $precio, $cantidad, $categoria_id]);
        
        header("Location: panel_productor.php?success=1");
        exit;
        
    } catch (PDOException $e) {
        header("Location: publicar_producto.php?error=2");
        exit;
    }
} else {
    header("Location: publicar_producto.php");
    exit;
}
?>