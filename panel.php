<?php
session_start();

if (!isset($_SESSION['nombre'])) {
    header("Location: login.php");
    exit();
}

// Redirigir según el rol
if ($_SESSION['rol'] === 'Productor') {
    header("Location: panel_productor.php");
    exit();
} elseif ($_SESSION['rol'] === 'Consumidor') {
    header("Location: panel_consumidor.php");
    exit();
} else {
    // Si por alguna razón no tiene un rol válido
    session_destroy();
    header("Location: login.php");
    exit();
}
?>