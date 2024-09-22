<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fire Safety Web App</title>
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
            padding: 15px 20px;
            font-size: 18px;
        }
        .btn-custom:hover {
            background-color: #ff6b6b;
        }
    </style>
</head>
<body>

<div class="container text-center">
    <h1 class="mb-4">Welcome to Fire Safety Web App</h1>
    <p>Select your role to log in:</p>
    <div class="d-grid gap-3 col-6 mx-auto">
        <a href="user/user_login.php" class="btn btn-custom">User Login</a>
        <a href="admin/admin_login.php" class="btn btn-custom">Admin Login</a>
        <a href="verifier_login.php" class="btn btn-custom">Verifier Login</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
