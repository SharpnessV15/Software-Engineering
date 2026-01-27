<?php
include 'db.php';

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'administrator') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_number = $_POST['registration_number'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $connection_date = $_POST['connection_date'];
    
    $house_no = $_POST['house_no'];
    $locality = $_POST['locality'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    
    $address = "$house_no, $locality, $city, $state - $pincode";

    $role = $_POST['role'];
    $connection_type = $_POST['connection_type'] ?? 'home'; 
    $password = !empty($_POST['password']) ? $_POST['password'] : null;

    if (!preg_match("/^[a-zA-Z0-9]+$/", $registration_number)) {
        $error = "Registration Number must be alphanumeric.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Name must contain only letters and spaces.";
    } elseif (!preg_match("/^\d{10}$/", $_POST['phone_number'])) {
        $error = "Phone number must be exactly 10 digits.";
    } elseif (!preg_match("/^[a-zA-Z0-9\/\-\s]+$/", $house_no)) {
        $error = "House No must be alphanumeric.";
    } elseif (!preg_match("/^[a-zA-Z0-9\s,\-]+$/", $locality)) {
        $error = "Locality contains invalid characters.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $city)) {
        $error = "City must contain only letters.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $state)) {
        $error = "State must contain only letters.";
    } elseif (!preg_match("/^[0-9]{6}$/", $pincode)) {
        $error = "Pin Code must be 6 digits.";
    } elseif (($role == 'administrator' || $role == 'reader') && empty($password)) {
        $error = "Password is required for Administrator and Reader.";
    } else {
        $phone_number = $_POST['phone_number'];
        $sql = "INSERT INTO users (registration_number, name, dob, connection_date, address, role, password, connection_type, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $registration_number, $name, $dob, $connection_date, $address, $role, $password, $connection_type, $phone_number);

        if ($stmt->execute()) {
            $success = "User registered successfully!";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>


<?php include 'header.php'; ?>
    <div class="container py-5">
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back</a>
        <a href="logout.php" class="btn btn-danger mb-3 float-end">Logout</a>
        <h1 class="text-center mb-4">Register User</h1>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"> <?= $success ?> </div>
            <script>alert("<?= addslashes($success) ?>");</script>
        <?php endif; ?>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"> <?= $error ?> </div>
            <script>alert("<?= addslashes($error) ?>");</script>
        <?php endif; ?>
        <form method="POST" action="" class="mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number (Alphanumeric)</label>
                <input type="text" id="registration_number" name="registration_number" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Name (Letters only)</label>
                <input type="text" id="name" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number (10 digits)</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" required pattern="\d{10}" title="Exactly 10 digits">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (Required for Admin & Reader)</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Required if role is Administrator/Reader">
            </div>
            <div class="mb-3">
                <label for="connection_type" class="form-label">Connection Type</label>
                <select id="connection_type" name="connection_type" class="form-select" required>
                    <option value="home">Home</option>
                    <option value="corporate">Corporate</option>
                    <option value="industrial">Industrial</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" id="dob" name="dob" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="connection_date" class="form-label">Connection Date</label>
                <input type="date" id="connection_date" name="connection_date" class="form-control" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="house_no" class="form-label">House No</label>
                    <input type="text" id="house_no" name="house_no" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="locality" class="form-label">Locality</label>
                    <input type="text" id="locality" name="locality" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="state" class="form-label">State</label>
                    <input type="text" id="state" name="state" class="form-control" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="pincode" class="form-label">Pin Code (6 digits)</label>
                <input type="text" id="pincode" name="pincode" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="user">User</option>
                    <option value="administrator">Administrator</option>
                    <option value="reader">Reader</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</body>
</html>