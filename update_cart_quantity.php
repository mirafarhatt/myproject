<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Get JSON input from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!$data || !isset($data['book_id'], $data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$bookId = $data['book_id'];
$quantity = intval($data['quantity']);
$userId = $_SESSION['user_id'] ?? null;

// Ensure the user is logged in
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Check if the book exists and has sufficient stock
$sql = "SELECT stockQuantity FROM Books WHERE book_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $bookId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Book not found.']);
    exit;
}

$book = $result->fetch_assoc();
if ($quantity > $book['stockQuantity']) {
    echo json_encode(['success' => false, 'message' => 'Insufficient stock available.']);
    exit;
}

// Update the quantity in the cart
$sql = "UPDATE Cart SET quantity = ? WHERE bookId = ? AND userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $quantity, $bookId, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update quantity.']);
}

$stmt->close();
$conn->close();
?>
