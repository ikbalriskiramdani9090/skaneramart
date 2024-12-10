<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $result = $conn->query("SELECT * FROM users WHERE username='$username'");
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        if ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } else {
            header('Location: user/dashboard.php');
        }
    } else {
        echo "Kredensial tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Styles */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }

        form input[type="text"],
        form input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        form input[type="text"]:focus,
        form input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
        }

        form button {
            width: 100%;
            padding: 12px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        form button:hover {
            background-color: #0056b3;
        }

        form button:active {
            background-color: #004085;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            form {
                padding: 15px;
                width: 90%;
            }

            form input[type="text"],
            form input[type="password"],
            form button {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 24px;
            }

            form {
                padding: 10px;
                width: 100%;
            }

            form input[type="text"],
            form input[type="password"],
            form button {
                padding: 10px;
                font-size: 14px;
            }
        }

        /* Link Styles */
        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .register-link a {
            text-decoration: none;
            color: #007BFF;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Masuk</h1>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Masuk</button>
    </form>

    <div class="register-link">
        <p>Belum punya akun? <a href="register.php">Daftar disini</a></p>
    </div>

</body>
</html>
