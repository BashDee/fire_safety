<?php
// Start the session
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

// Include the database connection and the required libraries
require '../includes/config.php';
require '../phpqrcode/qrlib.php'; // For QR code generation
require '../fpdf/fpdf.php'; // For PDF certificate generation

// Handle QR Code and Certificate Generation
if (isset($_POST['generate_certificate']) && isset($_POST['cert_id'])) {
    $cert_id = $_POST['cert_id'];

    // Fetch certificate details
    $stmt = $pdo->prepare("SELECT certificates.*, users.name, users.username FROM certificates 
                            JOIN users ON certificates.user_id = users.id 
                            WHERE certificates.id = :cert_id");
    $stmt->execute(['cert_id' => $cert_id]);
    $certificate = $stmt->fetch();

    if ($certificate) {
        // Generate QR Code based on certificate details
        $qr_content = "Certificate ID: " . $certificate['id'] . "\n" .
                      "User: " . $certificate['name'] . "\n" .
                      "Property: " . $certificate['property_type'] . "\n" .
                      "Address: " . $certificate['property_address'];
        $qr_file = '../qrcodes/cert_' . $certificate['id'] . '.png';
        QRcode::png($qr_content, $qr_file);

        // Create a beautiful PDF certificate
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Add certificate border
        $pdf->SetLineWidth(1);
        $pdf->Rect(10, 10, 190, 277, 'D');

        // Add logo image
        $pdf->Image('../img/logo.png', 85, 15, 30); // Adjust 'logo.png' path and size
        $pdf->Ln(35); // Adjust space after logo

        // Certificate Header
        $pdf->SetFont('Arial', 'B', 28);
        $pdf->SetTextColor(50, 50, 150); // Blue color
        $pdf->Cell(0, 20, 'FIRE SAFETY CERTIFICATE', 0, 1, 'C');
        $pdf->Ln(5);

        // Certificate Subtitle
        $pdf->SetFont('Arial', 'I', 16);
        $pdf->SetTextColor(100, 100, 100); // Grey
        $pdf->Cell(0, 10, 'Certificate of Fire Safety Compliance', 0, 1, 'C');
        $pdf->Ln(20);

        // Property Details
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(0, 0, 0); // Black
        $pdf->MultiCell(0, 10, 'This is to certify that the property located at ' . $certificate['property_address'] .
            ' has been inspected and meets the required fire safety standards for ' . $certificate['property_type'] . '.', 0, 'C');
        $pdf->Ln(20);

        // Issued to
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Issued to: ' . $certificate['name'], 0, 1, 'C');
        $pdf->Ln(10);

        // Certificate number
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, 'Certificate No: ' . $certificate['id'], 0, 1, 'C'); // Certificate number based on cert_id
        $pdf->Ln(10);

        // Add QR code (authentication barcode)
        $pdf->Image($qr_file, 80, 200, 50, 50); // Position it appropriately on the certificate

        // Add footer text
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->SetTextColor(100, 100, 100); // Grey
        $pdf->SetY(-40); // Footer position
        $pdf->Cell(0, 5, 'Scan the QR code to verify the authenticity of this certificate.', 0, 0, 'C');
        $pdf->Ln(10);
        $pdf->Cell(0, 5, 'Issued on: ' . date('Y-m-d'), 0, 0, 'C');

        // Save the certificate as PDF
        $pdf_file = '../certificates/cert_' . $certificate['id'] . '.pdf';
        $pdf->Output('F', $pdf_file); // Save PDF file

        // Display success message
        $success = "Certificate generated successfully for " . $certificate['name'];
    }
}

// Fetch all approved certificates
$stmt = $pdo->query("SELECT certificates.*, users.name, users.username FROM certificates 
                     JOIN users ON certificates.user_id = users.id 
                     WHERE certificates.status = 'Approved'");
$certificates_approved = $stmt->fetchAll();


// Handle Approve or Deny actions
if (isset($_POST['action']) && isset($_POST['cert_id'])) {
    $cert_id = $_POST['cert_id'];
    $action = $_POST['action'];

    // Update the certificate status
    if ($action == 'approve') {
        $stmt = $pdo->prepare("UPDATE certificates SET status = 'Approved' WHERE id = :cert_id");
    } elseif ($action == 'deny') {
        $stmt = $pdo->prepare("UPDATE certificates SET status = 'Denied' WHERE id = :cert_id");
    }

    $stmt->execute(['cert_id' => $cert_id]);

    // Redirect to refresh the page after action
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all pending certificate requests
$stmt = $pdo->query("SELECT certificates.*, users.name, users.username FROM certificates 
                     JOIN users ON certificates.user_id = users.id 
                     WHERE certificates.status = 'Pending'");
$pending_certificates = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Generate Certificate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(45deg, #28a745, #17a2b8);
        }
        .dashboard-container {
            margin-top: 50px;
        }
        .btn-custom {
            background-color: #17a2b8;
            color: white;
        }
        .btn-custom:hover {
            background-color: #28a745;
        }
        .nav-link {
            color: white;
            font-weight: bold;
        }
        .nav-link:hover {
            color: #ffc107;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_logs.php">View Logs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="admin_logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Admin Dashboard Content -->
<div class="container dashboard-container">
<div class="row">
        <div class="col-md-12">
            <h2 class="text-center text-white">Manage Pending Certificate Requests</h2>

            <!-- Pending Certificate Requests -->
            <table class="table table-bordered table-light mt-5">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Property Type</th>
                        <th>Property Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pending_certificates) > 0): ?>
                        <?php foreach ($pending_certificates as $cert): ?>
                            <tr>
                                <td><?= $cert['id'] ?></td>
                                <td><?= htmlspecialchars($cert['name']) ?> (<?= htmlspecialchars($cert['username']) ?>)</td>
                                <td><?= htmlspecialchars($cert['property_type']) ?></td>
                                <td><?= htmlspecialchars($cert['property_address']) ?></td>
                                <td>
                                    <!-- Approve Form -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                    </form>

                                    <!-- Deny Form -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
                                        <button type="submit" name="action" value="deny" class="btn btn-danger">Deny</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No pending certificate requests.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h2 class="text-center text-white">Generate Certificates</h2>

            <!-- Success message -->
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <!-- Approved Certificates with Certificate Generation -->
            <h3>Approved Certificates</h3>
            <table class="table table-bordered table-light mt-2">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Property Type</th>
                        <th>Property Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($certificates_approved) > 0): ?>
                        <?php foreach ($certificates_approved as $cert): ?>
                            <tr>
                                <td><?= $cert['id'] ?></td>
                                <td><?= htmlspecialchars($cert['name']) ?> (<?= htmlspecialchars($cert['username']) ?>)</td>
                                <td><?= htmlspecialchars($cert['property_type']) ?></td>
                                <td><?= htmlspecialchars($cert['property_address']) ?></td>
                                <td>
                                    <!-- Generate Certificate Button -->
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="cert_id" value="<?= $cert['id'] ?>">
                                        <button type="submit" name="generate_certificate" class="btn btn-primary">Generate Certificate</button>
                                    </form>
                                    <?php
                                    $pdf_file = '../certificates/cert_' . $cert['id'] . '.pdf';
                                    if (file_exists($pdf_file)): ?>
                                        <a href="<?= $pdf_file ?>" target="_blank" class="btn btn-success">View Certificate</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No approved certificates yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
