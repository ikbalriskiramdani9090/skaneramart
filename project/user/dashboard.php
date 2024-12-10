<?php
session_start();
include '../db.php';

// Pastikan hanya user yang dapat mengakses halaman ini
if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

// Tambahkan produk ke keranjang
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = 1; // Default quantity

    // Cek apakah keranjang sudah ada di sesi
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Validasi produk
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity,
            ];
        }
        $_SESSION['message'] = "Product added to cart!";
    } else {
        $_SESSION['message'] = "Product not found.";
    }

    $stmt->close();
    header("Location: dashboard.php?category_id=" . $_GET['category_id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            flex-direction: column;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-top: 20px;
        }

        a {
            text-decoration: none;
            color: #007BFF;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Message Styles */
        .message {
            text-align: center;
            color: green;
            font-size: 16px;
            margin-top: 10px;
        }

        /* Navigation Links */
        nav {
            text-align: center;
            margin: 20px 0;
        }

        nav a {
            margin: 0 15px;
            font-size: 18px;
            color: #333;
        }

        /* Categories and Products */
        h2 {
            margin-top: 30px;
            font-size: 24px;
            color: #333;
        }

        /* Kategori */
        .category-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 0;
        }

        .category-list .category {
            text-align: center;
            width: 150px;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .category-list .category img {
            width: 100%;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

        .category-list .category a {
            display: block;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
            font-size: 16px;
            text-transform: capitalize;
        }

        /* Product Layout */
        .product-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .product-list .product {
            background-color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 200px;
        }

        .product-list .product img {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .product-list .product button {
            padding: 8px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .product-list .product button:hover {
            background-color: #0056b3;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .category-list .category {
                width: 120px;
            }

            .product-list .product {
                width: 150px;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 20px;
            }

            .category-list {
                flex-direction: column;
                align-items: center;
            }

            .category-list .category {
                width: 100%;
            }

            .product-list .product {
                width: 100%;
            }

            .product-list .product button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

    <h1>User Dashboard</h1>
    <nav>
        <a href="cart.php">Cart</a>
        <a href="checkout.php">Checkout</a>
        <a href="history.php">Order History</a>
    </nav>

    <?php
    // Tampilkan pesan sukses atau error
    if (isset($_SESSION['message'])) {
        echo "<p class='message'>" . htmlspecialchars($_SESSION['message']) . "</p>";
        unset($_SESSION['message']);
    }
    ?>

    <h2>Product Categories</h2>
    <div class="category-list">
        <?php
        $sql = "SELECT * FROM categories";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($category = $result->fetch_assoc()) {
                echo "<div class='category'>";
                if (!empty($category['image'])) {
                    echo "<img src='../uploads/categories/" . htmlspecialchars($category['image']) . "' alt='" . htmlspecialchars($category['name']) . "'>";
                }
                echo "<a href='?category_id=" . htmlspecialchars($category['id']) . "'>" . htmlspecialchars($category['name']) . "</a>";
                echo "</div>";
            }
        } else {
            echo "<p>No categories found.</p>";
        }
        ?>
    </div>

    <h2>Products</h2>
    <div class="product-list">
        <?php
        if (isset($_GET['category_id'])) {
            $category_id = intval($_GET['category_id']);
            $product_sql = "SELECT * FROM products WHERE category_id = ?";
            $stmt = $conn->prepare($product_sql);
            $stmt->bind_param("i", $category_id);
            $stmt->execute();
            $product_result = $stmt->get_result();

            if ($product_result->num_rows > 0) {
                while ($product = $product_result->fetch_assoc()) {
                    echo "<div class='product'>";
                    echo "<img src='../uploads/products/" . htmlspecialchars($product['image']) . "' alt='" . htmlspecialchars($product['name']) . "'><br>";
                    echo "<strong>" . htmlspecialchars($product['name']) . "</strong><br>";
                    echo "Price: Rp" . number_format($product['price'], 0, ',', '.') . "<br>";
                    echo "<form method='POST'>";
                    echo "<input type='hidden' name='product_id' value='" . $product['id'] . "'>";
                    echo "<button type='submit' name='add_to_cart'>Add to Cart</button>";
                    echo "</form>";
                    echo "</div>";
                }
            } else {
                echo "<p>No products available in this category.</p>";
            }
            $stmt->close();
        } else {
            echo "<p>Select a category to view products.</p>";
        }
        ?>
    </div>

</body>
</html>
