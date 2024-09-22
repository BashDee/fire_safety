<?php
// Start the session
session_start();

// Include the database connection
require '../includes/config.php'; // Ensure this file contains the database connection

// Initialize variables
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Basic validation
    if (empty($username) || empty($password)) {
        $errors[] = "Both username and password are required.";
    } else {
        // Prepare a query to find the user by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // If the user exists, verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Store the user's ID in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // Redirect to the user dashboard
            header('Location: user_dashboard.php');
            exit();
        } else {
            $errors[] = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login - Fire Safety</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #007bff, #6610f2);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: #6610f2;
            color: white;
        }
        .btn-custom:hover {
            background-color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">User Login</h2>

    <!-- Display error messages -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="user_login.php">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-custom">Login</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
