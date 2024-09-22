<?php
// Start the session
session_start();

// Include database connection
require '../includes/config.php'; // Assumes config.php contains database connection code

// Initialize variables
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));
    
    // Basic validation
   
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    // Check if the email or username is already taken
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $existingUser = $stmt->fetch();

    if ($existingUser) {
        if ($existingUser['username'] == $username) {
            $errors[] = "Username is already taken.";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user into the database
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
        $stmt->execute([
            'username' => $username,
            'password' => $hashed_password
        ]);

        // Success message and redirect to login page
        $success = "Registration successful! You can now <a href='admin_login.php'>login</a>.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #ff6b6b, #f6d365);
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
            background-color: #f6d365;
            color: white;
        }
        .btn-custom:hover {
            background-color: #ff6b6b;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Admin Register for Fire Safety</h2>

    <!-- Display errors if there are any -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Display success message -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?= $success ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="admin_register.php">
       
     
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required value="<?= isset($username) ? $username : '' ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-custom">Register</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
