<?php
include('connection.php');

// Start session to get user ID
session_start();
$user_id = $_SESSION['user_id'];  // Assuming user ID is stored in session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the order data from POST request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $total_price = $_POST['total_price'];

    // Get cart items for the current user
    $sql_cart = "SELECT * FROM Cart WHERE userId = ?";
    $stmt_cart = $conn->prepare($sql_cart);
    $stmt_cart->bind_param('i', $user_id);
    $stmt_cart->execute();
    $cart_result = $stmt_cart->get_result();

    // Insert order data into the Orders table
    $sql_order = "INSERT INTO Orders (userId, totalPrice, phoneNumber, street, city, address) 
                  VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param('idssss', $user_id, $total_price, $phone, $street, $city, $address);
    $stmt_order->execute();

    // Get the last inserted order ID
    $order_id = $stmt_order->insert_id;

    // Initialize total price
    $total_price_calculated = 0;

    // Process each cart item
    while ($cart_item = $cart_result->fetch_assoc()) {
        $book_id = $cart_item['bookId'];
        $quantity = $cart_item['quantity'];
        $is_rental = $cart_item['isRental'];
        $price = $cart_item['price']; // Rental price if it’s a rental

        if ($is_rental == 1) {
            // Insert into the Rented_books table for rentals
            $sql_rented = "INSERT INTO Rented_books (userId, bookId, rent_until, rented_at, orderId) 
                           VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 7 DAY), NOW(), ?)";
            $stmt_rented = $conn->prepare($sql_rented);
            $stmt_rented->bind_param('iii', $user_id, $book_id, $order_id);
            $stmt_rented->execute();

            // Decrement stock for rentals
            $sql_update_stock_rentals = "UPDATE Books SET stockQuantity = stockQuantity - ? WHERE book_id = ?";
            $stmt_update_stock_rentals = $conn->prepare($sql_update_stock_rentals);
            $stmt_update_stock_rentals->bind_param('ii', $quantity, $book_id);
            $stmt_update_stock_rentals->execute();

            // Add rental cost to total
            $total_price_calculated += $price * $quantity;
        } else {
            // Insert into the Order_Books table for purchases
            $sql_order_books = "INSERT INTO Order_Books (order_id, book_id, quantity) 
                                VALUES (?, ?, ?)";
            $stmt_order_books = $conn->prepare($sql_order_books);
            $stmt_order_books->bind_param('iii', $order_id, $book_id, $quantity);
            $stmt_order_books->execute();

            // Decrement stock for purchases
            $sql_update_stock = "UPDATE Books SET stockQuantity = stockQuantity - ? WHERE book_id = ?";
            $stmt_update_stock = $conn->prepare($sql_update_stock);
            $stmt_update_stock->bind_param('ii', $quantity, $book_id);
            $stmt_update_stock->execute();

            // Calculate total price for purchases
            $sql_book_price = "SELECT bookPrice FROM Books WHERE book_id = ?";
            $stmt_book_price = $conn->prepare($sql_book_price);
            $stmt_book_price->bind_param('i', $book_id);
            $stmt_book_price->execute();
            $result_price = $stmt_book_price->get_result();
            $book_data = $result_price->fetch_assoc();
            $book_price = $book_data['bookPrice'];
            $total_price_calculated += $book_price * $quantity;
        }
    }

    // Update the total price for the order
    $sql_update_order = "UPDATE Orders SET totalPrice = ? WHERE order_id = ?";
    $stmt_update_order = $conn->prepare($sql_update_order);
    $stmt_update_order->bind_param('di', $total_price_calculated, $order_id);
    $stmt_update_order->execute();

    // Clear the user's cart after the order is placed
    $sql_clear_cart = "DELETE FROM Cart WHERE userId = ?";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param('i', $user_id);
    $stmt_clear_cart->execute();

    // Redirect to the order confirmation page
    header("Location: order_confirmation.php?order_id=" . $order_id);
    exit();
}
?>
