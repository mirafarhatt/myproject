<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'connection.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Log incoming data for debugging
file_put_contents('log.txt', print_r($input, true), FILE_APPEND);

// Validate and sanitize input
if (!isset($input['userId'], $input['bookId'], $input['rentalPrice'], $input['quantity'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit;
}

$userId = (int)$input['userId'];
$bookId = (int)$input['bookId'];
$rentalPrice = (float)$input['rentalPrice'];  // Ensure this is treated as a float
$quantity = (int)$input['quantity'];
$isRental = 1; // Since this is for renting

// Log rental price for debugging
error_log('Rental Price: ' . $rentalPrice);

// Check if the book already exists in the cart for the user
$sqlCheck = "SELECT * FROM Cart WHERE userId = ? AND bookId = ? AND isRental = 1";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->bind_param("ii", $userId, $bookId);
$stmtCheck->execute();
$result = $stmtCheck->get_result();

if ($result->num_rows > 0) {
    // Update quantity and ensure the price is rentalPrice
    $sqlUpdate = "UPDATE Cart SET quantity = quantity + ?, price = ? WHERE userId = ? AND bookId = ? AND isRental = 1";
    $stmtUpdate = $conn->prepare($sqlUpdate);
    $stmtUpdate->bind_param("idii", $quantity, $rentalPrice, $userId, $bookId);
    if ($stmtUpdate->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Book rental updated in the cart.']);
    } else {
        $error = $stmtUpdate->error;
        error_log('SQL Error: ' . $error);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to update the cart.']);
    }
    $stmtUpdate->close();
} else {
    // Insert a new entry with the rentalPrice
    $sqlInsert = "INSERT INTO Cart (userId, bookId, quantity, created_at, isRental, price) VALUES (?, ?, ?, NOW(), ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("iiiid", $userId, $bookId, $quantity, $isRental, $rentalPrice);
    
    if ($stmtInsert->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Book rental added to the cart.']);
    } else {
        $error = $stmtInsert->error;
        error_log('SQL Error: ' . $error);  // Log the error message
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Failed to add the book to the cart.']);
    }
    $stmtInsert->close();
}

$stmtCheck->close();
$conn->close();
