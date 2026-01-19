<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_number = $_POST['registration_number'];
    $units_used = $_POST['units_used'];

    // Fetch user details
    $sql = "SELECT * FROM users WHERE registration_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $registration_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Calculate bill
        $bill_amount = 0;
        if ($units_used <= 50) {
            $bill_amount = $units_used * 2;
        } elseif ($units_used <= 100) {
            $bill_amount = (50 * 2) + (($units_used - 50) * 4);
        } else {
            $bill_amount = (50 * 2) + (50 * 4) + (($units_used - 100) * 6);
        }

        // Insert bill into database
        $bill_date = date('Y-m-d');
        $insert_sql = "INSERT INTO bills (registration_number, units_used, bill_amount, bill_date) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sids", $registration_number, $units_used, $bill_amount, $bill_date);
        $insert_stmt->execute();

        echo "<h1>Bill Details</h1>";
        echo "<p>Name: " . $user['name'] . "</p>";
        echo "<p>Registration Number: " . $user['registration_number'] . "</p>";
        echo "<p>Address: " . $user['address'] . "</p>";
        echo "<p>Units Used: " . $units_used . "</p>";
        echo "<p>Bill Amount: Rs. " . $bill_amount . "</p>";
        echo "<p>Bill Date: " . $bill_date . "</p>";

        $insert_stmt->close();
    } else {
        echo "User not found!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Calculate Bill</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <a href="login.php" class="btn btn-secondary mb-3">Back</a>
        <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
        <h1 class="text-center mb-4">Calculate Electricity Bill</h1>
        <form method="POST" action="" class="mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" id="registration_number" name="registration_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="units_used" class="form-label">Units Used</label>
                <input type="number" id="units_used" name="units_used" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Calculate Bill</button>
        </form>
    </div>
</body>
</html>