<?php
session_start();

// Simulación de pedidos
$pedidos = [
    ['producto' => 'Tomate', 'cantidad' => 2, 'total' => 1000],
    ['producto' => 'Lechuga', 'cantidad' => 1, 'total' => 300]
];

include 'navbar.php';
?>

<div class="container mt-5">
    <h2>Mis Pedidos</h2>
    <p>Estos son tus pedidos simulados:</p>

    <ul class="list-group">
        <?php foreach ($pedidos as $pedido): ?>
            <li class="list-group-item">
                <?php echo "{$pedido['cantidad']} x {$pedido['producto']} - ₡{$pedido['total']}"; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include 'footer.php'; ?>
