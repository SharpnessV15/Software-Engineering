<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'administrator') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_number = $_POST['registration_number'];
    $units_used = $_POST['units_used'];
    $month = $_POST['month'];

    $bill_amount = 0;
    if ($units_used <= 50) {
        $bill_amount = $units_used * 2;
    } elseif ($units_used <= 100) {
        $bill_amount = (50 * 2) + (($units_used - 50) * 4);
    } else {
        $bill_amount = (50 * 2) + (50 * 4) + (($units_used - 100) * 6);
    }

    $bill_date = date('Y-m-d');
    $sql = "INSERT INTO bills (registration_number, units_used, bill_amount, bill_date, month) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidss", $registration_number, $units_used, $bill_amount, $bill_date, $month);

    if ($stmt->execute()) {
        $success = "Bill added successfully.";
    } else {
        $error = "Error: " . $stmt->error;
    }
}

$result = $conn->query("SELECT registration_number FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="login.php" class="btn btn-secondary mb-3">Back</a>
        <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
        <h1 class="text-center mb-4">Admin Dashboard</h1>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>
        <form method="POST" action="" class="mx-auto" style="max-width: 600px;">
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
                <label for="month" class="form-label">Month</label>
                <input type="text" id="month" name="month" class="form-control" placeholder="e.g., January 2026" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Add Bill</button>
        </form>
    </div>
</body>
</html>