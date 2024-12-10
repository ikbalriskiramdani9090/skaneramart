<?php
session_start();
include '../db.php';

// Pastikan hanya user yang dapat mengakses halaman ini
if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

// Pastikan data yang dikirim lengkap
if (isset($_POST['order_id']) && isset($_POST['confirm_order'])) {
    $order_id = intval($_POST['order_id']);

    // Update status konfirmasi pesanan di tabel orders
    $stmt = $conn->prepare("UPDATE orders SET confirmation_status = 'confirmed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        // Redirect kembali ke halaman riwayat pesanan setelah konfirmasi
        header("Location: history.php?status=confirmed");
        exit();
    } else {
        echo "<p>Error confirming order.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid request.</p>";
}
?>
