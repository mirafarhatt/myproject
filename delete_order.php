<?php
include 'connection.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $orderId = intval($_POST['order_id']);

    // Delete from `order_books` table
    $conn->query("DELETE FROM order_books WHERE order_id = $orderId");

    // Delete from `rented_books` table
    $conn->query("DELETE FROM rented_books WHERE orderId = $orderId");

    // Delete from `orders` table
    $conn->query("DELETE FROM orders WHERE order_id = $orderId");

    // Redirect back to orders page
    header('Location: admin_dashboard.php');
    exit;
}
?>
