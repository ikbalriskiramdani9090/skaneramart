<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $description = htmlspecialchars($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $image_name = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_dir = "../uploads/products/";
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate image file
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // File uploaded successfully
            } else {
                $_SESSION['message'] = "Error uploading image.";
                header('Location: manage_products.php');
                exit();
            }
        } else {
            $_SESSION['message'] = "File is not a valid image.";
            header('Location: manage_products.php');
            exit();
        }
    }

    // Insert product data into database
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, category_id, image) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdiss", $name, $description, $price, $stock, $category_id, $image_name);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Product added successfully.";
    } else {
        $_SESSION['message'] = "Error adding product.";
    }
    $stmt->close();
    header('Location: manage_products.php');
    exit();
}
?>
