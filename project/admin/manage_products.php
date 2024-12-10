<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id = $id");
    header('Location: manage_products.php');
    exit();
}

// Fetch products and categories
$products = $conn->query("SELECT products.*, categories.name AS category_name FROM products 
                          LEFT JOIN categories ON products.category_id = categories.id");
$categories = $conn->query("SELECT * FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        a {
            text-decoration: none;
            color: #007BFF;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Button Styles */
        button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Form Styles */
        form {
            max-width: 600px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        form input, form select {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        table th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }

        table td img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Actions Links */
        .actions a {
            margin: 0 5px;
            font-size: 14px;
            color: #333;
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .actions a:hover {
            background-color: #007BFF;
            color: white;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
                padding: 10px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }

            form input, form select {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                display: block;
                width: 100%;
                text-align: left;
                padding: 10px;
            }

            table td {
                border: none;
                border-bottom: 1px solid #ddd;
            }

            table th {
                background-color: transparent;
                color: #333;
            }

            table tr {
                display: block;
                margin-bottom: 15px;
            }

            .actions {
                display: flex;
                justify-content: center;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <h1>Manage Products</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <h2>Add Product</h2>
    <form method="POST" action="add_product.php" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Product Name" required>
        <!-- Removed Description textarea -->
        <input type="number" name="price" placeholder="Price" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <select name="category_id" required>
            <option value="">Select Category</option>
            <?php while ($category = $categories->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($category['id']) ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="file" name="image" required>
        <button type="submit">Add Product</button>
    </form>

    <h2>Product List</h2>
    <table>
        <tr>
            <th>Name</th>
            <!-- Removed Description column -->
            <th>Price</th>
            <th>Stock</th>
            <th>Category</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($product = $products->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <!-- Removed Description column -->
            <td><?= htmlspecialchars($product['price']) ?></td>
            <td><?= htmlspecialchars($product['stock']) ?></td>
            <td><?= htmlspecialchars($product['category_name']) ?></td>
            <td>
                <?php
                $imagePath = '../uploads/products/' . htmlspecialchars($product['image']);
                if (!empty($product['image']) && file_exists($imagePath)) {
                    echo "<img src='" . $imagePath . "' alt='" . htmlspecialchars($product['name']) . "'>";
                } else {
                    echo "No image available";
                }
                ?>
            </td>
            <td class="actions">
                <a href="?delete=<?= htmlspecialchars($product['id']) ?>" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
