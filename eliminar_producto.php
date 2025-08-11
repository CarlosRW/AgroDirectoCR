<?php
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Productor') {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

$productor_id = $_SESSION['user_id'];
$producto_id = $_GET['id'] ?? 0;

try {
    // Verificar que el producto pertenece al productor actual
    $stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? AND productor_id = ?");
    $stmt->execute([$producto_id, $productor_id]);
    
    if (!$stmt->fetch()) {
        header("Location: mis_productos.php?error=producto_no_encontrado");
        exit;
    }
    
    // Eliminar el producto
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ? AND productor_id = ?");
    $stmt->execute([$producto_id, $productor_id]);
    
    header("Location: mis_productos.php?success=producto_eliminado");
    exit;
    
} catch (PDOException $e) {
    header("Location: mis_productos.php?error=error_eliminar");
    exit;
}
?>