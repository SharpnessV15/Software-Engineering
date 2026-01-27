<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'reader') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_number = $_POST['registration_number'];
    $units_used = $_POST['units_used'];
    $bill_date = $_POST['month']; 
    $month = date("F Y", strtotime($bill_date)); 

    $type_query = "SELECT connection_type FROM users WHERE registration_number = ?";
    $stmt_type = $conn->prepare($type_query);
    $stmt_type->bind_param("s", $registration_number);
    $stmt_type->execute();
    $type_result = $stmt_type->get_result();
    $user_data = $type_result->fetch_assoc();
    $connection_type = $user_data['connection_type'] ?? 'home';

    require_once 'calculate_bill.php';
    $bill_data = calculateBill($units_used, $connection_type);
    
    $bill_amount = $bill_data['total_amount'];
    $rate_details = array_map(function($seg) {
        return "{$seg['desc']} = Rs. {$seg['cost']}";
    }, $bill_data['segments']);

    $selected_rates = $bill_data['rates'];

    $due_date = date('Y-m-d', strtotime($bill_date . ' + 15 days'));
    
    $sql = "INSERT INTO bills (registration_number, units_used, bill_amount, bill_date, month, due_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidsss", $registration_number, $units_used, $bill_amount, $bill_date, $month, $due_date);

    if ($stmt->execute()) {
        $success = "Bill added successfully.";
        $mock_bill = [
            'registration_number' => $registration_number,
            'units_used' => $units_used,
            'month' => $month,
            'bill_date' => $bill_date,
            'total_amount' => $bill_amount,
            'rate_details' => $rate_details
        ];
    } else {
        $error = "Error: " . $stmt->error;
    }
}

$result = $conn->query("SELECT registration_number FROM users WHERE role = 'user'");
?>


<?php include 'header.php'; ?>
    <div class="container py-5">
        <a href="login.php" class="btn btn-secondary mb-3">Back</a>
        <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
        <h1 class="text-center mb-4">Reader Dashboard</h1>
        
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>

        <?php if (isset($mock_bill)): ?>
            <div class="card mb-4 border-success">
                <div class="card-header bg-success text-white">
                    <h3>Mock Bill Generated</h3>
                </div>
                <div class="card-body">
                    <p><strong>Registration Number:</strong> <?= $mock_bill['registration_number'] ?></p>
                    <p><strong>Connection Type:</strong> <?= ucfirst($connection_type) ?></p>
                    <p><strong>Month:</strong> <?= $mock_bill['month'] ?></p>
                    <p><strong>Units Used:</strong> <?= $mock_bill['units_used'] ?></p>
                    <hr>
                    <h5>Rate Calculation:</h5>
                    <ul>
                        <?php foreach ($mock_bill['rate_details'] as $detail): ?>
                            <li><?= $detail ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <hr>
                    <h4 class="text-end">Total Amount: Rs. <?= number_format($mock_bill['total_amount'], 2) ?></h4>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="mx-auto" style="max-width: 600px;">
            <div class="card">
                <div class="card-header">
                    <h4>Add New Reading</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="registration_number" class="form-label">Select Registration Number</label>
                        <select id="registration_number" name="registration_number" class="form-select" required>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <option value="<?= $row['registration_number'] ?>"> <?= $row['registration_number'] ?> </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="units_used" class="form-label">Units Used</label>
                        <input type="number" id="units_used" name="units_used" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="month" class="form-label">Select Date</label>
                        <input type="date" id="month" name="month" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Add Bill</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
