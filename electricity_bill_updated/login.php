<?php
include 'db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_number = $_POST['registration_number'];

    $sql = "SELECT * FROM users WHERE registration_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $registration_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['registration_number'] = $user['registration_number'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] == 'administrator') {
            if (isset($_POST['password']) && $_POST['password'] == $user['password']) {
                header('Location: admin_dashboard.php');
            } else {
                $error = "Invalid password for administrator.";
            }
        } elseif ($user['role'] == 'reader') {
             if (isset($_POST['password']) && $_POST['password'] == $user['password']) {
                 header('Location: reader_dashboard.php');
             } else {
                 $error = "Invalid password for reader.";
             }
        } else {
            header('Location: user_dashboard.php');
        }
        if (!isset($error)) exit;
    } else {
        $error = "Invalid registration number.";
    }
}
?>


<?php include 'header.php'; ?>
    <div class="container py-5">
        <h1 class="text-center mb-4">Login</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
        <?php endif; ?>
        <form method="POST" action="" class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" id="registration_number" name="registration_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (Required for Admin & Reader)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password if Admin/Reader">
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</body>
</html>