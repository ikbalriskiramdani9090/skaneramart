<?php
session_start();

if (isset($_POST['remove'])) {
    $product_id = intval($_POST['product_id']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        $_SESSION['message'] = "Product removed from cart.";
    }
}

header("Location: cart.php");
exit();
?>
