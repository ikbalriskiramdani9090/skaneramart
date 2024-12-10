<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Query untuk mengambil semua pesanan
$sql = "SELECT * FROM orders ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
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

        /* Form Styles for updating payment status */
        form {
            display: inline;
        }

        form select {
            padding: 5px 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        form button {
            padding: 5px 10px;
            margin-top: 5px;
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

            form select {
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

            .actions a {
                padding: 5px 10px;
            }
        }
    </style>
</head>
<body>
    <h1>Admin - Manage Orders</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<thead><tr><th>ID Order</th><th>User Name</th><th>Phone</th><th>Address</th><th>Payment Method</th><th>Total Amount</th><th>Payment Status</th><th>Payment Receipt</th><th>Order Date</th><th>Action</th></tr></thead><tbody>";

        while ($order = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($order['id']) . "</td>";
            echo "<td>" . htmlspecialchars($order['name']) . "</td>";
            echo "<td>" . htmlspecialchars($order['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($order['address']) . "</td>";
            echo "<td>" . htmlspecialchars($order['payment_method']) . "</td>";
            echo "<td>Rp" . number_format($order['total_amount'], 0, ',', '.') . "</td>";
            echo "<td>" . htmlspecialchars($order['payment_status']) . "</td>";

            // Tampilkan bukti pembayaran jika ada
            if (!empty($order['payment_receipt'])) {
                echo "<td><a href='../uploads/payment_receipts/" . htmlspecialchars($order['payment_receipt']) . "' target='_blank'>View Receipt</a></td>";
            } else {
                echo "<td>-</td>";
            }

            echo "<td>" . htmlspecialchars($order['created_at']) . "</td>";

            // Form untuk memperbarui status pembayaran
            echo "<td>
                    <form method='POST' action='update_payment_status.php'>
                        <input type='hidden' name='order_id' value='" . $order['id'] . "'>
                        <select name='payment_status'>
                            <option value='pending'" . ($order['payment_status'] == 'pending' ? ' selected' : '') . ">Pending</option>
                            <option value='completed'" . ($order['payment_status'] == 'completed' ? ' selected' : '') . ">Completed</option>
                            <option value='failed'" . ($order['payment_status'] == 'failed' ? ' selected' : '') . ">Failed</option>
                        </select>
                        <button type='submit'>Update Status</button>
                    </form>
                </td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p>No orders found.</p>";
    }

    ?>
</body>
</html>
