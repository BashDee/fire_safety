<?
// Verify Certificate (verify.php)
$qr_code = $_POST['qr_code'];

$stmt = $pdo->prepare("SELECT * FROM certificates WHERE qr_code = :qr_code AND status = 'Approved'");
$stmt->execute(['qr_code' => $qr_code]);

if ($stmt->rowCount() > 0) {
    echo "Certificate is valid.";
} else {
    echo "Invalid or unapproved certificate.";
}
