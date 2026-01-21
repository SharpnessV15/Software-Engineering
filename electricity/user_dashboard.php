<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}

$registration_number = $_SESSION['registration_number'];

$user_query = "SELECT name, connection_type FROM users WHERE registration_number = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("s", $registration_number);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$connection_type = $user['connection_type'] ?? 'home';
$name = $user['name'];

require_once 'calculate_bill.php';
$dummy_calc = calculateBill(0, $connection_type);
$my_rates = $dummy_calc['rates'];

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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Welcome, <?= htmlspecialchars($name) ?></h1>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Connection Details</h5>
                <p class="card-text mb-1"><strong>Registration Number:</strong> <?= htmlspecialchars($registration_number) ?></p>
                <p class="card-text mb-1"><strong>Connection Type:</strong> <?= ucfirst($connection_type) ?></p>
                <p class="card-text mb-0"><strong>Current Rates:</strong> 
                    Tier 1: Rs. <?= $my_rates[0] ?>, 
                    Tier 2: Rs. <?= $my_rates[1] ?>, 
                    Tier 3: Rs. <?= $my_rates[2] ?>
                </p>
            </div>
        </div>

        <h2 class="h4 mb-3">Bill History</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover shadow-sm bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Month</th>
                        <th>Bill Date</th>
                        <th>Units Used</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        $units = $row['units_used'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['month']) ?></td>
                            <td><?= date('d M Y', strtotime($row['bill_date'])) ?></td>
                            <td><?= $units ?></td>
                            <td class="fw-bold">Rs. <?= number_format($row['bill_amount'], 2) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>