<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .admin-links {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            gap: 20px;
            margin-top: 40px;
        }

        .admin-links a {
            background-color: #4CAF50;
            color: white;
            padding: 15px 25px;
            text-decoration: none;
            font-size: 1.2em;
            border-radius: 8px;
            text-align: center;
            width: 200px;
            transition: background-color 0.3s ease;
        }

        .admin-links a:hover {
            background-color: #45a049;
        }

        .admin-links a:active {
            background-color: #388e3c;
        }

        /* For smaller screens, stack the links vertically */
        @media (max-width: 768px) {
            .admin-links {
                flex-direction: column;
                align-items: center;
            }

            .admin-links a {
                width: 80%;
                margin-bottom: 15px;
            }
        }

        /* For very small screens */
        @media (max-width: 480px) {
            .admin-links a {
                font-size: 1.1em;
                padding: 12px 20px;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="admin-links">
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_products.php">Manage Products</a>
        <a href="manage_categories.php">Manage Categories</a>
        <a href="manage_order.php">Manage Orders</a>
    </div>
</div>

</body>
</html>
