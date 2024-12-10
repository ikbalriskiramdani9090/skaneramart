<?php
session_start();
include '../db.php'; // Pastikan file db.php benar

// Cek apakah pengguna adalah admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// Tambah kategori
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $image_name = null;

    // Proses unggah gambar
    if (!empty($_FILES['category_image']['name'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($_FILES['category_image']['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            die("Only JPG, PNG, and GIF files are allowed.");
        }

        if ($_FILES['category_image']['size'] > 2 * 1024 * 1024) { // Maksimal 2MB
            die("File size must be less than 2MB.");
        }

        $target_dir = "../uploads/categories/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true); // Buat folder jika belum ada
        }
        $image_name = time() . "_" . basename($_FILES['category_image']['name']);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES['category_image']['tmp_name'], $target_file);
    }

    if (!empty($category_name)) {
        $stmt = $conn->prepare("INSERT INTO categories (name, image) VALUES (?, ?)");
        $stmt->bind_param("ss", $category_name, $image_name);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: manage_categories.php');
    exit();
}

// Hapus kategori
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];

    // Hapus gambar kategori dari server
    $stmt = $conn->prepare("SELECT image FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    if ($image) {
        $image_path = "../uploads/categories/" . $image;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt->close();

    // Hapus kategori dari database
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->close();

    header('Location: manage_categories.php');
    exit();
}

// Ambil daftar kategori
$categories = $conn->query("SELECT * FROM categories");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
            color: #333;
            text-align: center;
        }

        a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 1.1em;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Form Styles */
        form {
            display: flex;
            flex-direction: column;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form input, form textarea, form select, form button {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 1.1em;
        }

        form button:hover {
            background-color: #45a049;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
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

        table td img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }

        table a {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        table a.edit {
            background-color: #4CAF50;
        }

        table a.delete {
            background-color: #ff4d4d;
        }

        table a:hover {
            opacity: 0.8;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            form {
                width: 100%;
                padding: 15px;
            }

            table th, table td {
                padding: 8px;
            }

            table td img {
                width: 80px;
            }
        }

        @media (max-width: 480px) {
            table th, table td {
                font-size: 0.9em;
                padding: 6px;
            }

            table td img {
                width: 70px;
            }

            form button {
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Manage Categories</h1>
    <a href="dashboard.php">Back to Dashboard</a>

    <h2>Add Category</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <br><br>
        <label for="category_image">Category Image:</label>
        <input type="file" name="category_image" id="category_image" accept="image/*">
        <br><br>
        <button type="submit" name="add_category">Add</button>
    </form>

    <h2>Categories List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($category = $categories->fetch_assoc()): ?>
        <tr>
            <td><?= $category['id'] ?></td>
            <td><?= htmlspecialchars($category['name']) ?></td>
            <td>
                <?php if (!empty($category['image'])): ?>
                    <img src="../uploads/categories/<?= htmlspecialchars($category['image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>
            <td>
                <a href="manage_categories.php?delete=<?= $category['id'] ?>" class="delete" onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</body>
</html>
