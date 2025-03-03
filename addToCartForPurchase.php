<?php
session_start();
require 'connection.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Decode the incoming JSON request
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['bookId'], $input['price'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$userId = $_SESSION['user_id'];
$bookId = (int)$input['bookId'];
$price = (float)$input['price'];

// Insert into Cart
$query = $conn->prepare("INSERT INTO Cart (userId, bookId, quantity, isRental, created_at, price) VALUES (?, ?, ?, ?, NOW(), ?)");
if ($query->execute([$userId, $bookId, 1, 0,$price])) { // isRental = 0 for purchases
    echo json_encode(['success' => true, 'message' => 'Book added to cart for purchase.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $query->errorInfo()[2]]);
}
?>
