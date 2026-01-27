<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'administrator') {
    header('Location: login.php');
    exit;
}

$result = $conn->query("SELECT registration_number FROM users");
?>


<?php include 'header.php'; ?>
    <div class="container py-5">
        <a href="login.php" class="btn btn-secondary mb-3">Back</a>
        <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
        <h1 class="text-center mb-4">Admin Dashboard</h1>
        <div class="list-group mx-auto" style="max-width: 600px;">
            <a href="register.php" class="list-group-item list-group-item-action text-center py-5">
                <h3>Register New User</h3>
                <p>Add new users, administrators, or readers.</p>
            </a>
        </div>
    </div>
</body>
</html>