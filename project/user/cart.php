<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

echo "<h1>Your Cart</h1>";

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
} else {
    // Display cart items in a styled table
    echo "<div class='cart-container'>";
    echo "<table class='cart-table'>";
    echo "<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Action</th></tr>";

    $total = 0;

    foreach ($_SESSION['cart'] as $id => $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;

        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td>Rp" . number_format($item['price'], 0, ',', '.') . "</td>";
        echo "<td>" . $item['quantity'] . "</td>";
        echo "<td>Rp" . number_format($subtotal, 0, ',', '.') . "</td>";
        echo "<td>";
        echo "<form method='POST' action='update_cart.php'>";
        echo "<input type='hidden' name='product_id' value='$id'>";
        echo "<button name='remove' class='remove-btn'>Remove</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }

    echo "<tr><td colspan='3' class='total-label'>Total</td><td colspan='2' class='total-price'>Rp" . number_format($total, 0, ',', '.') . "</td></tr>";
    echo "</table>";
    echo "<a class='checkout-btn' href='checkout.php'>Proceed to Checkout</a>";
    echo "</div>";
}
?>

<style>
    /* Overall Page Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-top: 20px;
    }

    /* Cart Container */
    .cart-container {
        max-width: 1000px;
        width: 90%;
        margin-top: 20px;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
    }

    /* Cart Table */
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .cart-table th, .cart-table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .cart-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .cart-table td {
        vertical-align: middle;
    }

    .total-label {
        text-align: right;
        font-weight: bold;
    }

    .total-price {
        font-size: 18px;
        font-weight: bold;
        color: #007BFF;
    }

    /* Action Buttons */
    .checkout-btn {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 25px;
        background-color: #28a745;
        color: white;
        font-size: 18px;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
        width: 100%;
        max-width: 300px;
        margin: 20px auto;
        transition: background-color 0.3s;
        float: right; /* Align button to the right */
    }

    .checkout-btn:hover {
        background-color: #218838;
    }

    .remove-btn {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .remove-btn:hover {
        background-color: #c82333;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .cart-table th, .cart-table td {
            font-size: 14px;
            padding: 10px;
        }

        .checkout-btn {
            font-size: 16px;
            padding: 10px 20px;
        }

        .cart-container {
            width: 95%;
            padding: 15px;
        }

        h1 {
            font-size: 22px;
        }
    }

    @media (max-width: 480px) {
        .cart-table th, .cart-table td {
            font-size: 12px;
            padding: 8px;
        }

        .checkout-btn {
            font-size: 14px;
            padding: 8px 18px;
        }

        h1 {
            font-size: 20px;
        }
    }
</style>
