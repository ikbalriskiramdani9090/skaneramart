<?php
session_start();
include '../db.php';

if ($_SESSION['role'] !== 'user') {
    header('Location: ../index.php');
    exit();
}

// Hitung total belanja
$total_amount = 0;
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }
}

// Proses form checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);
    $payment_method = htmlspecialchars($_POST['payment_method']);

    // Cek dan upload bukti pembayaran
    $upload_dir = '../uploads/payment_receipts/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $receipt_name = '';
    if (isset($_FILES['payment_receipt']) && $_FILES['payment_receipt']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['payment_receipt']['tmp_name'];
        $file_name = basename($_FILES['payment_receipt']['name']);
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'pdf'];

        if (in_array(strtolower($file_ext), $allowed_ext)) {
            $receipt_name = time() . '_' . uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $receipt_name;

            if (!move_uploaded_file($file_tmp, $upload_path)) {
                $_SESSION['message'] = "Failed to upload payment receipt.";
                header('Location: checkout.php');
                exit();
            }
        } else {
            $_SESSION['message'] = "Invalid file type for payment receipt.";
            header('Location: checkout.php');
            exit();
        }
    } else {
        $_SESSION['message'] = "Payment receipt is required.";
        header('Location: checkout.php');
        exit();
    }

    // Simpan data pesanan
    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, phone, address, payment_method, total_amount, payment_receipt) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssds", $_SESSION['user_id'], $name, $phone, $address, $payment_method, $total_amount, $receipt_name);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "Order placed successfully!";
        unset($_SESSION['cart']); // Kosongkan keranjang setelah checkout
    } else {
        $_SESSION['message'] = "Failed to place order. Please try again.";
    }

    $stmt->close();
    header('Location: checkout.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        a {
            text-decoration: none;
            color: #1a73e8;
        }

        .message {
            text-align: center;
            color: green;
            font-weight: bold;
        }

        .checkout-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .checkout-form label {
            font-size: 1.1em;
            color: #333;
        }

        .checkout-form input,
        .checkout-form select,
        .checkout-form textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .checkout-form textarea {
            height: 100px;
            resize: none;
        }

        .checkout-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.2em;
            width: 100%;
        }

        .checkout-form button:hover {
            background-color: #45a049;
        }

        .total-amount {
            text-align: center;
            font-size: 1.5em;
            margin: 20px 0;
        }

        .payment-details {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 4px;
        }

        .payment-details p {
            font-size: 1em;
            color: #333;
        }

        @media (max-width: 600px) {
            .checkout-form {
                padding: 15px;
            }

            .checkout-form button {
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Checkout</h1>
    <a href="cart.php">Back to Cart</a>

    <?php
    // Tampilkan pesan sukses atau error
    if (isset($_SESSION['message'])) {
        echo "<p class='message'>" . htmlspecialchars($_SESSION['message']) . "</p>";
        unset($_SESSION['message']);
    }
    ?>

    <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <p>Your cart is empty. <a href="dashboard.php">Go back to shop</a>.</p>
    <?php else: ?>
        <form class="checkout-form" method="POST" enctype="multipart/form-data">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="address">Shipping Address:</label>
            <textarea id="address" name="address" required></textarea>

            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required onchange="showPaymentDetails()">
                <option value="">Select Payment Method</option>
                <option value="bank_bca">Bank Transfer (BCA)</option>
                <option value="bank_mandiri">Bank Transfer (Mandiri)</option>
                <option value="gopay">GoPay</option>
                <option value="ovo">OVO</option>
                <option value="dana">DANA</option>
            </select>

            <div id="payment_details" style="display: none;" class="payment-details">
                <p><strong>Payment Details:</strong></p>
                <p id="payment_info"></p>
            </div>

            <label for="payment_receipt">Upload Payment Receipt:</label>
            <input type="file" id="payment_receipt" name="payment_receipt" required>

            <div class="total-amount">
                <p><strong>Total Amount: Rp<?php echo number_format($total_amount, 0, ',', '.'); ?></strong></p>
            </div>

            <button type="submit">Place Order</button>
        </form>
    <?php endif; ?>
</div>

<script>
function showPaymentDetails() {
    const paymentMethod = document.getElementById('payment_method').value;
    const paymentDetails = document.getElementById('payment_details');
    const paymentInfo = document.getElementById('payment_info');

    switch (paymentMethod) {
        case 'bank_bca':
            paymentDetails.style.display = 'block';
            paymentInfo.innerHTML = 'Bank BCA<br>Nomor Rekening: <strong>1234567890</strong><br>Atas Nama: PT Toko Online';
            break;
        case 'bank_mandiri':
            paymentDetails.style.display = 'block';
            paymentInfo.innerHTML = 'Bank Mandiri<br>Nomor Rekening: <strong>9876543210</strong><br>Atas Nama: PT Toko Online';
            break;
        case 'gopay':
            paymentDetails.style.display = 'block';
            paymentInfo.innerHTML = 'GoPay<br>Nomor: <strong>081234567890</strong>';
            break;
        case 'ovo':
            paymentDetails.style.display = 'block';
            paymentInfo.innerHTML = 'OVO<br>Nomor: <strong>081234567891</strong>';
            break;
        case 'dana':
            paymentDetails.style.display = 'block';
            paymentInfo.innerHTML = 'DANA<br>Nomor: <strong>081234567892</strong>';
            break;
        default:
            paymentDetails.style.display = 'none';
            paymentInfo.innerHTML = '';
            break;
    }
}
</script>

</body>
</html>
