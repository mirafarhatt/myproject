<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Get JSON input from the request
$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!$data || !isset($data['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$bookId = $data['book_id'];
$userId = $_SESSION['user_id'] ?? null;

// Ensure the user is logged in
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

// Delete the item from the cart
$sql = "DELETE FROM Cart WHERE bookId = ? AND userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $bookId, $userId);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to remove item.']);
}

$stmt->close();
$conn->close();
?>
