<?php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to view your cart.']);
    exit();
}

// Fetch the cart items for the logged-in user
$userId = $_SESSION['user_id'];
$sql = "SELECT c.*, b.bookTitle, b.bookPrice, b.rentalPrice, b.image
        FROM Cart c
        JOIN Books b ON c.bookId = b.book_id
        WHERE c.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
while ($row = $result->fetch_assoc()) {
    // Choose the price based on whether it's a rental or purchase
    $price = $row['isRental'] == 1 ? $row['rentalPrice'] : $row['bookPrice'];

    $cartItems[] = [
        'id' => $row['bookId'],
        'title' => $row['bookTitle'],
        'price' => $price,
        'quantity' => $row['quantity'],
        'image' => $row['image'],
    ];
}

echo json_encode(['success' => true, 'cart' => $cartItems]);

$stmt->close();
$conn->close();
?>
