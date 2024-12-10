<?php
session_start();
include '../db.php';

// Pastikan hanya user yang dapat mengakses halaman ini
if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

// Ambil riwayat pesanan pengguna
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, name, total_amount, payment_status, created_at, user_notification, confirmation_status FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Tampilkan riwayat pesanan
?>

<h1>Order History</h1>

<?php
if ($result->num_rows > 0) {
    while ($order = $result->fetch_assoc()) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; margin-bottom: 20px;'>";

        // Tampilkan detail pesanan
        echo "<strong>Order ID: </strong>" . htmlspecialchars($order['id']) . "<br>";
        echo "<strong>Product Name: </strong>" . htmlspecialchars($order['name']) . "<br>";
        echo "<strong>Total Amount: </strong>Rp" . number_format($order['total_amount'], 0, ',', '.') . "<br>";
        echo "<strong>Payment Status: </strong>" . htmlspecialchars($order['payment_status']) . "<br>";
        echo "<strong>Order Date: </strong>" . htmlspecialchars($order['created_at']) . "<br>";

        // Tampilkan notifikasi jika ada
        if (!empty($order['user_notification'])) {
            echo "<div style='color: green; font-weight: bold; margin-top: 10px;'>" . htmlspecialchars($order['user_notification']) . "</div>";
        }

        // Tampilkan status konfirmasi dan form konfirmasi jika pesanan belum dikonfirmasi
        if ($order['confirmation_status'] == 'not_confirmed') {
            echo "<form method='POST' action='confirm_order.php'>
                    <input type='hidden' name='order_id' value='" . $order['id'] . "'>
                    <button type='submit' name='confirm_order'>Confirm Receipt</button>
                  </form>";
        } else {
            echo "<div style='color: blue; font-weight: bold;'>Your order has been confirmed.</div>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No orders found.</p>";
}

$stmt->close();
?>
