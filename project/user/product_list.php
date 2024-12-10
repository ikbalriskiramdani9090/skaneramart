<?php
session_start();
include('../db.php');

// Cek apakah user sudah login
if ($_SESSION['role'] != 'user') {
    header('Location: ../index.php');
    exit();
}

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll();
?>

<h1>Product List</h1>

<?php foreach ($products as $product): ?>
<div>
    <img src="../uploads/products/<?= $product['image'] ?>" alt="Product Image" width="100">
    <h3><?= $product['name'] ?></h3>
    <p><?= $product['description'] ?></p>
    <p>Price: <?= $product['price'] ?></p>
    <p>Stock: <?= $product['stock'] ?></p>
    <a href="cart.php?id=<?= $product['id'] ?>">Add to Cart</a>
</div>
<?php endforeach; ?>
