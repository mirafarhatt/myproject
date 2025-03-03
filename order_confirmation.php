<?php
include('connection.php'); 

// Start session to get user ID
session_start();
$order_id = $_GET['order_id'];  // Get the order ID from the URL

// Query to get order data
$sql_order = "SELECT * FROM Orders WHERE order_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param('i', $order_id);
$stmt_order->execute();
$order_result = $stmt_order->get_result();

if ($order_result->num_rows > 0) {
    $order = $order_result->fetch_assoc();
    
    // Query to get the books for this order from the Order_Books and Rented_books tables
    $sql_order_books = "
    SELECT OB.book_id, OB.quantity, B.bookTitle, B.bookPrice, 'purchase' AS type 
    FROM Order_Books OB
    JOIN Books B ON OB.book_id = B.book_id
    WHERE OB.order_id = ?
    
    UNION
    
    SELECT RB.bookId, 1 AS quantity, B.bookTitle, 
           IFNULL(B.rentalPrice, B.bookPrice) AS bookPrice, 'rental' AS type 
    FROM Rented_books RB
    JOIN Books B ON RB.bookId = B.book_id
    WHERE RB.userId = ?";
    
    
    $stmt_order_books = $conn->prepare($sql_order_books);
    $stmt_order_books->bind_param('ii', $order_id, $order['userId']);
    $stmt_order_books->execute();
    $order_books_result = $stmt_order_books->get_result();
    
    // Fetch book details for each book in the order
    $book_details = [];
    while ($book = $order_books_result->fetch_assoc()) {
        $book_details[] = [
            'bookTitle' => $book['bookTitle'],
            'bookPrice' => $book['bookPrice'],
            'quantity' => $book['quantity'],
            'type' => $book['type']
        ];
    }
    
    // Display order details
    echo "<h1 class='text-center'>Order Confirmation</h1>";
    echo "<div class='order-summary'>";
    echo "<p><strong>Order ID:</strong> " . $order['order_id'] . "</p>";
    echo "<p><strong>Total Price:</strong> $" . number_format($order['totalPrice'], 2) . "</p>";
    echo "<table class='table'>
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>";
    
    foreach ($book_details as $book) {
        $total_price = $book['bookPrice'] * $book['quantity'];
        echo "<tr>
                <td>" . htmlspecialchars($book['bookTitle']) . "</td>
                <td>" . $book['quantity'] . "</td>
                <td>$" . number_format($book['bookPrice'], 2) . "</td>
                <td>$" . number_format($total_price, 2) . "</td>
                <td>" . ucfirst($book['type']) . "</td>
              </tr>";
    }
    
    echo "</tbody></table></div>";
} else {
    echo "<p class='text-center'>Order not found.</p>";
}
?>


<!-- Add the following style tags inside the <head> section of your HTML -->
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }
    
    h1 {
        margin-top:50px;
        font-size: 2.5rem;
        color: rgb(75, 7, 101);;
        margin-bottom: 20px;
    }

    .order-summary {
        background-color: #fff;
        padding: 30px;
        margin: 20px auto;
        width: 80%;
        max-width: 900px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(58, 11, 71, 0.37);
    }

    .order-summary p {
        font-size: 1.1rem;
        color: #555;
        margin: 10px 0;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table th, .table td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .table th {
        background-color: #f4f4f4;
        color: #333;
    }

    .table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .table tr:hover {
        background-color: #f1f1f1;
    }

    .text-center {
        text-align: center;
    }

    .back-button {
        display: inline-block;
        padding: 10px 20px;
        margin-top: 20px;
        margin-left:50px;
        background-color:rgb(183, 44, 225);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        text-align: center;
    }

    .back-button:hover {
        background-color:rgb(75, 7, 101);
    }

    
</style>

<!-- Back Button -->
<a href="home.html" class="back-button" >Go back to the library</a>
