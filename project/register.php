<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql) === TRUE) {
        header('Location: index.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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
        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            text-decoration: none;
            color: #007BFF;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Daftar</h1>

    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Daftar</button>
    </form>

    <div class="login-link">
        <p>Sudah punya akun? <a href="index.php">Masuk disini</a></p>
    </div>

</body>
</html>
