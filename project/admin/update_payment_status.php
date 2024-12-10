<?php
session_start();
include '../db.php';

// Pastikan hanya admin yang dapat mengakses halaman ini
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Pastikan data yang dikirim lengkap
if (isset($_POST['order_id']) && isset($_POST['payment_status'])) {
    $order_id = intval($_POST['order_id']);
    $payment_status = $_POST['payment_status'];

    // Update status pembayaran di tabel orders
    $stmt = $conn->prepare("UPDATE orders SET payment_status = ?, user_notification = ? WHERE id = ?");
    $notification = 'Pesanan Anda sedang diproses'; // Pesan notifikasi untuk pengguna
    $stmt->bind_param("ssi", $payment_status, $notification, $order_id);
    
    if ($stmt->execute()) {
        // Redirect kembali ke halaman manage_orders setelah status diperbarui
        header("Location: manage_orders.php?status=updated");
        exit();
    } else {
        echo "<p>Error updating payment status.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid request.</p>";
}
?>
