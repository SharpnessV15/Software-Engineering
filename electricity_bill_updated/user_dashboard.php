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

// Old query removed. Queries are now inside the HTML structure.
?>


<?php include 'header.php'; ?>
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
                <p class="card-text mb-0"><strong>Current Rates:</strong> <br>
                    Tier 1 (0-50): Rs. <?= $my_rates[0] ?> <br>
                    Tier 2 (51-100): Rs. <?= $my_rates[1] ?> <br>
                    Tier 3 (101-150): Rs. <?= $my_rates[2] ?> <br>
                    Tier 4 (>150): Rs. <?= $my_rates[3] ?>
                </p>
            </div>
        </div>

        <h2 class="h4 mb-3">Bill History & Pending Dues</h2>
        
        <?php
        $pending_query = "SELECT SUM(bill_amount) as total_pending FROM bills WHERE registration_number = ? AND status = 'unpaid'";
        $stmt_pending = $conn->prepare($pending_query);
        $stmt_pending->bind_param("s", $registration_number);
        $stmt_pending->execute();
        $pending_res = $stmt_pending->get_result()->fetch_assoc();
        $total_pending = $pending_res['total_pending'] ?? 0;
        ?>
        
        <div class="alert alert-warning">
            <strong>Total Pending Amount:</strong> Rs. <?= number_format($total_pending, 2) ?>
        </div>
        
        <h2 class="h4 mb-3 text-danger">Unpaid Bills</h2>
        
        <?php
        $sql_unpaid = "SELECT * FROM bills WHERE registration_number = ? AND status = 'unpaid' ORDER BY bill_date ASC";
        $stmt_unpaid = $conn->prepare($sql_unpaid);
        $stmt_unpaid->bind_param("s", $registration_number);
        $stmt_unpaid->execute();
        $res_unpaid = $stmt_unpaid->get_result();

        if ($res_unpaid->num_rows > 0):
            while ($row = $res_unpaid->fetch_assoc()):
                $units = $row['units_used'];
                $bill_calc = calculateBill($units, $connection_type);
                $segments = $bill_calc['segments'];
                $due_date = $row['due_date'];
                
                $fine_data = calculateFine($row['bill_date'], $due_date);
                $fine = $fine_data['amount'];
                $months_late = $fine_data['months'];

                $total_payable = $row['bill_amount'] + $fine;
        ?>
            <div class="card mb-4 border-danger shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Bill for <?= htmlspecialchars($row['month']) ?></h5>
                    <span class="badge bg-light text-danger">Due: <?= date('d M Y', strtotime($due_date)) ?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer Details</h6>
                            <p class="mb-1"><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
                            <p class="mb-1"><strong>Reg No:</strong> <?= htmlspecialchars($registration_number) ?></p>
                            <p><strong>Connection:</strong> <?= ucfirst($connection_type) ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted">Bill Details</h6>
                            <p class="mb-1"><strong>Bill Date:</strong> <?= date('d M Y', strtotime($row['bill_date'])) ?></p>
                            <p class="mb-1"><strong>Units Consumed:</strong> <?= $units ?></p>
                            <p><strong>Status:</strong> <span class="badge bg-danger">Unpaid</span></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="text-secondary">Charge Breakdown</h6>
                    <ul class="list-group list-group-flush mb-3">
                        <?php foreach ($segments as $seg): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <?= $seg['desc'] ?>
                                <span>Rs. <?= number_format($seg['cost'], 2) ?></span>
                            </li>
                        <?php endforeach; ?>
                        <?php if ($fine > 0): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 text-danger">
                            Late Payment Fine (<?= $months_late ?> month<?= $months_late > 1 ? 's' : '' ?> x 150)
                            <span>Rs. <?= number_format($fine, 2) ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                    
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <h4 class="mb-0">Total Payable: Rs. <?= number_format($total_payable, 2) ?></h4>
                        <form method="POST" action="pay_bill.php">
                            <input type="hidden" name="bill_id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-lg">Pay Now</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php 
            endwhile; 
        else:
        ?>
            <div class="alert alert-success">No unpaid bills!</div>
        <?php endif; ?>

        <hr class="my-5">

        <h2 class="h4 mb-3 text-secondary">Past Bills History</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover shadow-sm bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Month</th>
                        <th>Bill Date</th>
                        <th>Units</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql_paid = "SELECT * FROM bills WHERE registration_number = ? AND status = 'paid' ORDER BY bill_date DESC";
                    $stmt_paid = $conn->prepare($sql_paid);
                    $stmt_paid->bind_param("s", $registration_number);
                    $stmt_paid->execute();
                    $res_paid = $stmt_paid->get_result();

                    while ($row = $res_paid->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($row['month']) ?></td>
                            <td><?= date('d M Y', strtotime($row['bill_date'])) ?></td>
                            <td><?= $row['units_used'] ?></td>
                            <td>Rs. <?= number_format($row['bill_amount'], 2) ?></td>
                            <td><span class="badge bg-success">Paid</span></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>