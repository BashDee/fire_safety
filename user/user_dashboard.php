<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: user_login.php');
    exit();
}

// Include database connection
require '../includes/config.php';

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Handle certificate cancellation
if (isset($_POST['cancel_application']) && isset($_POST['cert_id'])) {
    $cert_id = $_POST['cert_id'];

    // Delete the pending certificate request
    $stmt = $pdo->prepare("DELETE FROM certificates WHERE id = :cert_id AND user_id = :user_id AND status = 'Pending'");
    $stmt->execute(['cert_id' => $cert_id, 'user_id' => $user_id]);

    $success = "Your pending certificate request has been cancelled.";
    header("Location: user_dashboard.php"); // Refresh the page after cancellation
    exit();
}

// Fetch the user's certificate requests
$stmt = $pdo->prepare("SELECT * FROM certificates WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute(['user_id' => $user_id]);
$certificates = $stmt->fetchAll();

// Handle certificate request submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['property_type'])) {
    $property_type = htmlspecialchars(trim($_POST['property_type']));
    $property_address = htmlspecialchars(trim($_POST['property_address']));

    // Validate input
    if (empty($property_type) || empty($property_address)) {
        $error = "Both property type and address are required!";
    } else {
        // Insert the certificate request into the database
        $stmt = $pdo->prepare("INSERT INTO certificates (user_id, property_type, property_address, status) VALUES (:user_id, :property_type, :property_address, 'Pending')");
        $stmt->execute([
            'user_id' => $user_id,
            'property_type' => $property_type,
            'property_address' => $property_address
        ]);

        $success = "Your certificate request has been submitted successfully!";
        header("Location: user_dashboard.php"); // Refresh to see the updated list
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Apply for Certificate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #007bff, #6610f2);
            height: 100vh;
            /* display: flex; */
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
<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="user_logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav> 
<div class="container">
    <h2 class="text-center mb-4">Apply for Fire Safety Certificate</h2>

    <!-- Display success or error messages -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <!-- Certificate Request Form -->
    <form method="POST" action="user_dashboard.php">
        <div class="mb-3">
            <label for="property_type" class="form-label">Property Type</label>
            <input type="text" class="form-control" id="property_type" name="property_type" required>
        </div>
        <div class="mb-3">
            <label for="property_address" class="form-label">Property Address</label>
            <input type="text" class="form-control" id="property_address" name="property_address" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-custom">Submit Application</button>
        </div>
    </form>

    <!-- List of Certificate Requests -->
    <h3 class="mt-5">Your Certificate Requests</h3>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>Property Type</th>
                <th>Property Address</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($certificates)): ?>
                <?php foreach ($certificates as $cert): ?>
                    <tr>
                        <td><?= $cert['id'] ?></td>
                        <td><?= htmlspecialchars($cert['property_type']) ?></td>
                        <td><?= htmlspecialchars($cert['property_address']) ?></td>
                        <td><?= htmlspecialchars($cert['status']) ?></td>
                        <td>
                            <?php if ($cert['status'] == 'Pending'): ?>
                                <!-- Cancel pending application -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
                                    <button type="submit" name="cancel_application" class="btn btn-danger">Cancel</button>
                                </form>
                            <?php elseif ($cert['status'] == 'Approved'): ?>
                                <!-- View approved certificate -->
                                <?php
                                $pdf_file = '../certificates/cert_' . $cert['id'] . '.pdf';
                                if (file_exists($pdf_file)): ?>
                                    <a href="<?= $pdf_file ?>" target="_blank" class="btn btn-success">View Certificate</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">Not available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No certificate requests found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
