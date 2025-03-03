<?php
session_start();
require_once 'connection.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$userQuery = $conn->prepare("SELECT name FROM Users WHERE user_id = ?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$userResult = $userQuery->get_result();
$userData = $userResult->fetch_assoc();

// Fetch orders
$orderQuery = $conn->prepare("SELECT * FROM Orders WHERE userId = ?");
$orderQuery->bind_param("i", $user_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

// Fetch rented books
$rentedQuery = $conn->prepare("
    SELECT rb.rental_id, b.bookTitle, rb.rent_until, rb.rented_at, b.rentalPrice 
    FROM Rented_books rb
    JOIN Books b ON rb.bookId = b.book_id
    WHERE rb.userId = ?
");
;
$rentedQuery->bind_param("i", $user_id);
$rentedQuery->execute();
$rentedResult = $rentedQuery->get_result();

// Fetch graphic design requests
$requestQuery = $conn->prepare("SELECT request_id, request_type, created_at, status 
                                FROM design_requests 
                                WHERE user_id = ?");
$requestQuery->bind_param("i", $user_id);
$requestQuery->execute();
$requestResult = $requestQuery->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
   
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    
    <style>
        /* General Body Styling */
        body {
            margin-top: 180px;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #f7f8fa, #e2e6ec);
            color: #333333;
            line-height: 1.6;
        }

        /* Profile Section */
        .profile-section, .orders-section, .rented-section, .requests-section {
            margin: 20px 0;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Section Titles */
        .profile-section h2, 
        .orders-section h2, 
        .rented-section h2, 
        .requests-section h2 {
            font-size: 1.5rem;
            color: #444;
            margin-bottom: 15px;
        }

        /* Profile Info */
        .profile-section p, 
        .orders-section ul li, 
        .rented-section ul li, 
        .requests-section ul li {
            font-size: 1rem;
            color: #555;
            margin: 5px 0;
            padding-left: 10px;
            border-left: 3px solid #6c757d;
        }

        /* Lists in Sections */
        .orders-section ul, 
        .rented-section ul, 
        .requests-section ul {
            list-style: none;
            padding: 0;
        }

        .orders-section ul li, 
        .rented-section ul li, 
        .requests-section ul li {
            margin-bottom: 10px;
            padding: 8px;
            background: #f9f9f9;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        .orders-section ul li:hover, 
        .rented-section ul li:hover, 
        .requests-section ul li:hover {
            background: #f0f0f0;
        }

        /* Highlighting Strong Text */
        .profile-section p strong {
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col logo_section">
                <div class="full">
                    <div class="center-desk">
                        <div class="logo" style="margin-top:-40px;margin-bottom: -30px;">
                            <a href="Home.html"><img src="images/logo1.png" alt="#" width="150px"></a> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 col-lg-9 col-md-9 col-sm-9">
                <div class="menu-area">
                    <div class="limit-box">
                        <nav class="main-menu">
                            <ul class="menu-area-main">
                                <li><a href="Home.html">Home</a></li>
                                <li><a href="library.php">Our Books</a></li>
                                <li><a href="services.html">Our Services</a></li>
                                <li><a href="about.html">About us</a></li>
                                <li><a href="contact.html">Contact us</a></li>
                                <li class="mean-last"><a href="cart.html"><img src="images/cart.png" alt="#"></a></li>
                                <li><a href="logout.php" title="Logout"><img src="images/logout.png" alt="Logout"></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($userData['name']); ?></h1>

    <div class="orders-section">
    <h2>Your Orders</h2>
    <ul>
        <?php while ($order = $orderResult->fetch_assoc()): ?>
            <li>
                <strong>Order ID:</strong> <?php echo $order['order_id']; ?><br>
                <strong>Total Price:</strong> $<?php echo number_format($order['totalPrice'], 2); ?><br>
                <strong>Address:</strong> <?php echo $order['street'] . ', ' . $order['city']; ?><br>
                <strong>Phone Number:</strong> <?php echo $order['phoneNumber']; ?><br>

                <h5>Ordered Books:</h5>
                <ul>
                    <?php
                    // Fetch purchased books
                    $booksQuery = $conn->prepare("
                        SELECT ob.quantity, b.bookTitle, b.bookPrice
                        FROM Order_Books ob
                        JOIN Books b ON ob.book_id = b.book_id
                        WHERE ob.order_id = ?
                    ");
                    $booksQuery->bind_param("i", $order['order_id']);
                    $booksQuery->execute();
                    $booksResult = $booksQuery->get_result();

                    if ($booksResult->num_rows > 0) {
                        while ($book = $booksResult->fetch_assoc()) {
                            echo "<li>
                                    <strong>Book Title:</strong> " . htmlspecialchars($book['bookTitle']) . "<br>
                                    <strong>Quantity:</strong> " . htmlspecialchars($book['quantity']) . "<br>
                                    <strong>Price per Book:</strong> $" . number_format($book['bookPrice'], 2) . "
                                  </li>";
                        }
                    }

                    $rentedQuery = $conn->prepare("
                    SELECT rb.rental_id, b.bookTitle, rb.rent_until, rb.rented_at, b.rentalPrice
                    FROM Rented_books rb
                    JOIN Books b ON rb.bookId = b.book_id
                    WHERE rb.orderId = ?
                ");
                
                    $rentedQuery->bind_param("i", $order['order_id']);
                    $rentedQuery->execute();
                    $rentedResult = $rentedQuery->get_result();

                    if ($rentedResult->num_rows > 0) {
                        echo "<h5>Rented Books:</h5>";
                        while ($rented = $rentedResult->fetch_assoc()) {
                            echo "<li>
                                    <strong>Book Title:</strong> " . htmlspecialchars($rented['bookTitle']) . "<br>
                                    <strong>Rented Until:</strong> " . htmlspecialchars($rented['rent_until']) . "<br>
                                    <strong>Rented At:</strong> " . htmlspecialchars($rented['rented_at']) . "<br>
                                    <strong>Rental Price:</strong> $" . number_format($rented['rentalPrice'], 2) . "
                                  </li>";
                        }
                    }
                    
                    if ($booksResult->num_rows === 0 && $rentedResult->num_rows === 0) {
                        echo "<li>No books found for this order.</li>";
                    }
                    ?>
                </ul>
            </li>
        <?php endwhile; ?>
    </ul>
</div>


    <div class="requests-section">
        <h2>Graphic Design Requests</h2>
        <ul>
            <?php while ($request = $requestResult->fetch_assoc()): ?>
                <li>Request ID: <?php echo $request['request_id']; ?>, 
                    Details: <?php echo htmlspecialchars($request['request_type']); ?>, 
                    Date: <?php echo $request['created_at']; ?>, 
                    Status: <?php echo htmlspecialchars($request['status']); ?>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

</body>
</html>
