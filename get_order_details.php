<?php

include('connection.php'); 

session_start();
$user_id = $_SESSION['user_id'];  

// Fetch cart items from the Cart table, including rental and purchase
$sql = "SELECT Cart.cart_id, Cart.bookId, Cart.quantity, Cart.isRental, Books.bookTitle, Books.bookPrice, Cart.price AS rentalPrice
        FROM Cart
        JOIN Books ON Cart.bookId = Books.book_id
        WHERE Cart.userId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC); // Fetch all cart items

// Prepare the order details
$order_details = [];
$total_price = 0;
foreach ($cart_items as $item) {
    // If the item is rented, use the rental price, otherwise use the book price
    $price = $item['isRental'] == 1 ? $item['rentalPrice'] : $item['bookPrice'];
    $item_total = $price * $item['quantity'];
    $total_price += $item_total;
    $order_details[] = [
        'bookTitle' => $item['bookTitle'],
        'quantity' => $item['quantity'],
        'bookPrice' => number_format($price, 2),
        'total' => number_format($item_total, 2),
        'isRental' => $item['isRental'] // Adding isRental to distinguish rented items
    ];
}

// Return the order details as JSON
echo json_encode(['order_details' => $order_details, 'total_price' => number_format($total_price, 2)]); 

?>
