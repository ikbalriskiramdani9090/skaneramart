<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: products.php');
}

// Fetch products
$products = $conn->query("SELECT products.*, categories.name AS category_name FROM products 
                          LEFT JOIN categories ON products.category_id = categories.id");
$categories = $conn->query("SELECT * FROM categories");
?>

<h1>Manage Products</h1>
<a href="dashboard.php">Back to Dashboard</a>

<h2>Add Product</h2>
<form method="POST" action="add_product.php" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="number" name="price" placeholder="Price" required>
    <input type="number" name="stock" placeholder="Stock" required>
    <select name="category_id" required>
        <option value="">Select Category</option>
        <?php while ($category = $categories->fetch_assoc()): ?>
            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <input type="file" name="image" required>
    <button type="submit">Add Product</button>
</form>

<h2>Product List</h2>
<table border="1">
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Category</th>
        <th>Image</th>
        <th>Actions</th>
    </tr>
    <?php while ($product = $products->fetch_assoc()): ?>
    <tr>
        <td><?= $product['name'] ?></td>
        <td><?= $product['description'] ?></td>
        <td><?= $product['price'] ?></td>
        <td><?= $product['stock'] ?></td>
        <td><?= $product['category_name'] ?></td>
        <td><img src="../uploads/products/<?= $product['image'] ?>" width="50"></td>
        <td>
            <a href="edit_product.php?id=<?= $product['id'] ?>">Edit</a>
            <a href="?delete=<?= $product['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
