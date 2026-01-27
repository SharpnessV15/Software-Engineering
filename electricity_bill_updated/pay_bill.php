<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['bill_id'])) {
    $bill_id = $_POST['bill_id'];
    $sql = "UPDATE bills SET status = 'paid' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bill_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Bill paid successfully!'); window.location.href='user_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error processing payment.'); window.location.href='user_dashboard.php';</script>";
    }
} else {
    header('Location: user_dashboard.php');
}
?>
