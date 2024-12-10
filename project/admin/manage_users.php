<?php
session_start();
include '../db.php'; // Pastikan path ini benar

// Cek apakah pengguna adalah admin
if ($_SESSION['role'] !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo "Access denied: You do not have permission to access this page.";
    exit();
}

// Hapus pengguna
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        $message = "User deleted successfully!";
    } else {
        $message = "Failed to delete user.";
    }
    $stmt->close();
}

// Ambil daftar pengguna
$users = $conn->query("SELECT id, username, role FROM users");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        /* Navbar */
        a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 1.1em;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #4CAF50;
            color: white;
        }

        table tr:hover {
            background-color: #f5f5f5;
        }

        /* Button Styling */
        .delete-btn {
            color: white;
            background-color: #ff4d4d;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .delete-btn:hover {
            background-color: #e60000;
        }

        /* Notification Styles */
        .message {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 5px;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            table {
                font-size: 0.9em;
            }

            table th, table td {
                padding: 8px;
            }

            .container {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                font-size: 0.8em;
            }

            .delete-btn {
                font-size: 0.9em;
                padding: 4px 8px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Users</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <!-- Notifikasi -->
    <?php if (isset($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <!-- Tabel Daftar Pengguna -->
    <h2>Users List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?= $user['id'] ?></td>
            <td><?= $user['username'] ?></td>
            <td><?= ucfirst($user['role']) ?></td>
            <td>
                <a href="manage_users.php?delete=<?= $user['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
