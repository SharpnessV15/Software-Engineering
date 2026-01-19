<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}

$registration_number = $_SESSION['registration_number'];
$sql = "SELECT * FROM bills WHERE registration_number = ? ORDER BY bill_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $registration_number);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="login.php" class="btn btn-secondary mb-3">Back</a>
        <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
        <h1 class="text-center mb-4">User Dashboard</h1>
        <h2 class="text-center">Previous Bills</h2>
        <table class="table table-bordered table-striped mt-4">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Units Used</th>
                    <th>Bill Amount</th>
                    <th>Bill Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['month'] ?></td>
                        <td><?= $row['units_used'] ?></td>
                        <td>Rs. <?= $row['bill_amount'] ?></td>
                        <td><?= $row['bill_date'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>