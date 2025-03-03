<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] !== 'admin') {
    echo "<script>alert('Access denied! Admins only.'); window.location.href='sign-in.html';</script>";
    exit();
}

// Add a new book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $bookTitle = trim($_POST['bookTitle']);
    $category = trim($_POST['category']);
    $authorFName = trim($_POST['authorFName']);
    $authorLName = trim($_POST['authorLName']);
    $publisherName = trim($_POST['publisherName']);
    $bookPrice = floatval($_POST['bookPrice']);
    $rentalPrice = floatval($_POST['rentalPrice']);  // Get rental price
    $stockQuantity = intval($_POST['stockQuantity']);
    $genre = trim($_POST['genre']);
    $description = trim($_POST['description']);
    $language = trim($_POST['language']);

    if (empty($bookTitle) || empty($category) || empty($authorFName) || empty($authorLName) || empty($publisherName) || empty($bookPrice) || empty($rentalPrice) || empty($stockQuantity) || empty($genre) || empty($language)) {
        echo "<script>alert('All fields are required!');</script>";
    } else {
      // Handle image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $imageTmpPath = $_FILES['image']['tmp_name'];
    $imageName = basename($_FILES['image']['name']);
    $imageUploadPath = 'uploads/' . $imageName; // Add uploads/ at the beginning

    // Move the uploaded file to the desired folder
    if (move_uploaded_file($imageTmpPath, $imageUploadPath)) {
        // Image upload successful, proceed with the DB insert
        $conn->begin_transaction();
        try {
            // Category
            $stmt = $conn->prepare("SELECT category_id FROM category WHERE categoryName = ?");
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $result = $stmt->get_result();
            $categoryData = $result->fetch_assoc();
            if (!$categoryData) {
                $stmt = $conn->prepare("INSERT INTO category (categoryName) VALUES (?)");
                $stmt->bind_param("s", $category);
                $stmt->execute();
                $categoryId = $conn->insert_id;
            } else {
                $categoryId = $categoryData['category_id'];
            }

            // Author
            $stmt = $conn->prepare("SELECT author_id FROM authors WHERE authorFname = ? AND authorLname = ?");
            $stmt->bind_param("ss", $authorFName, $authorLName);
            $stmt->execute();
            $result = $stmt->get_result();
            $authorData = $result->fetch_assoc();
            if (!$authorData) {
                $stmt = $conn->prepare("INSERT INTO authors (authorFname, authorLname) VALUES (?, ?)");
                $stmt->bind_param("ss", $authorFName, $authorLName);
                $stmt->execute();
                $authorId = $conn->insert_id;
            } else {
                $authorId = $authorData['author_id'];
            }

            // Publisher
            $stmt = $conn->prepare("SELECT publisher_id FROM publishers WHERE publisherName = ?");
            $stmt->bind_param("s", $publisherName);
            $stmt->execute();
            $result = $stmt->get_result();
            $publisherData = $result->fetch_assoc();
            if (!$publisherData) {
                $stmt = $conn->prepare("INSERT INTO publishers (publisherName) VALUES (?)");
                $stmt->bind_param("s", $publisherName);
                $stmt->execute();
                $publisherId = $conn->insert_id;
            } else {
                $publisherId = $publisherData['publisher_id'];
            }

            // Add Book with full image path (uploads/filename.jpg)
            $stmt = $conn->prepare("INSERT INTO books (bookTitle, categoryId, authorId, publisherId, bookPrice, rentalPrice, stockQuantity, genre, description, language, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siidissssss", $bookTitle, $categoryId, $authorId, $publisherId, $bookPrice, $rentalPrice, $stockQuantity, $genre, $description, $language, $imageUploadPath);  // Use $imageUploadPath
            $stmt->execute();
        
            // Commit transaction and show success
            $conn->commit();
            echo "<script>alert('Book added successfully!'); window.location.href='admin_dashboard.php';</script>";
        } catch (Exception $e) {
            // If an exception is thrown, rollback the transaction
            $conn->rollback();
            echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
        }
    } else {
        echo "<script>alert('Error uploading image!');</script>";
    }
} else {
    echo "<script>alert('Image upload error!');</script>";
}

        
    }
}

// Update a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $bookId = intval($_POST['bookId']);
    $bookPrice = floatval($_POST['bookPrice']);
    $rentalPrice = floatval($_POST['rentalPrice']);
    $stockQuantity = intval($_POST['stockQuantity']);

    $stmt = $conn->prepare("UPDATE books SET bookPrice = ?, rentalPrice = ?, stockQuantity = ? WHERE book_id = ?");
    $stmt->bind_param("ddii", $bookPrice, $rentalPrice, $stockQuantity, $bookId);

    if ($stmt->execute()) {
        echo "<script>alert('Book updated successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating book!');</script>";
    }
}

// Delete a book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $bookId = intval($_POST['bookId']);

    $stmt = $conn->prepare("DELETE FROM books WHERE book_id = ?");
    $stmt->bind_param("i", $bookId);

    if ($stmt->execute()) {
        echo "<script>alert('Book deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting book!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        h1, h2 {
            text-align: center;
            margin-top: 20px;
            color: #444;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        form input, 
        form textarea, 
        form button {
            width: calc(100% - 20px);
            margin: 10px 10px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form textarea {
            height: 100px;
            resize: none;
        }

        form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
        }

        /* Table styles */
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        table thead {
            background-color: #007bff;
            color: white;
        }

        table th, 
        table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Action buttons */
        form[style*="display:inline;"] button {
            width: auto;
            display: inline-block;
            padding: 5px 10px;
            margin: 5px;
            font-size: 14px;
        }

        form[style*="display:inline;"] button[type="submit"] {
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        form[style*="display:inline;"] button[type="submit"]:hover {
            background-color: #218838;
        }

        form[style*="display:inline;"] button[type="submit"]:last-of-type {
            background-color: #dc3545;
        }

        form[style*="display:inline;"] button[type="submit"]:last-of-type:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="admin-dashboard">
        <h1>Admin Dashboard</h1>
        <div style="text-align: right; margin: 20px;">
            <a href="logout.php" style="text-decoration: none; background-color: #dc3545; color: white; padding: 10px 15px; border-radius: 5px; font-size: 14px;">Logout</a>
        </div>

        <!-- Add Book Form -->
        <form action="admin_dashboard.php" method="POST" enctype="multipart/form-data">
    <h2>Add Book</h2>
    <input type="hidden" name="action" value="add">
    
    <label>Book Title:</label><input type="text" name="bookTitle" required>
    <label>Category:</label><input type="text" name="category" required>
    <label>Author First Name:</label><input type="text" name="authorFName" required>
    <label>Author Last Name:</label><input type="text" name="authorLName" required>
    <label>Publisher Name:</label><input type="text" name="publisherName" required>
    <label>Price:</label><input type="number" step="0.01" name="bookPrice" required>
    <label>Rental Price:</label><input type="number" step="0.01" name="rentalPrice" required> <!-- Rental Price -->
    <label>Stock Quantity:</label><input type="number" name="stockQuantity" required>
    <label>Genre:</label><input type="text" name="genre" required>
    <label>Description:</label><textarea name="description"></textarea>
    <label>Language:</label><input type="text" name="language" required>
    <label for="image">Upload Image:</label>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit">Add Book</button>
</form>


        <!-- Manage Books -->
      <!-- Manage Books -->
<h2>Manage Books</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Price</th>
            <th>Rental Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("SELECT book_id, bookTitle, bookPrice, rentalPrice, stockQuantity FROM books");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['book_id']}</td>
                    <td>{$row['bookTitle']}</td>
                    <td>{$row['bookPrice']}</td>
                    <td>{$row['rentalPrice']}</td>
                    <td>{$row['stockQuantity']}</td>
                    <td>
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='action' value='update'>
                            <input type='hidden' name='bookId' value='{$row['book_id']}' />
                            <input type='number' name='bookPrice' step='0.01' value='{$row['bookPrice']}' required />
                            <input type='number' name='rentalPrice' step='0.01' value='{$row['rentalPrice']}' required />
                            <input type='number' name='stockQuantity' value='{$row['stockQuantity']}' required />
                            <button type='submit'>Update</button>
                        </form>
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='action' value='delete'>
                            <input type='hidden' name='bookId' value='{$row['book_id']}' />
                            <button type='submit'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>


<!-- Graphic Design Requests -->
<h2>Graphic Design Requests</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Status</th>
            <th>Uploaded Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
        // Handle status update
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] === 'update_status') {
                $requestId = intval($_POST['requestId']);
                $status = $_POST['status'];

                // Ensure the status is valid
                $validStatuses = ['pending', 'in-progress', 'completed'];
                if (in_array($status, $validStatuses)) {
                    $stmt = $conn->prepare("UPDATE design_requests SET status = ? WHERE request_id = ?");
                    $stmt->bind_param("si", $status, $requestId);

                    if ($stmt->execute()) {
                        echo "<script>alert('Request status updated successfully!'); window.location.href='admin_dashboard.php';</script>";
                    } else {
                        echo "<script>alert('Error updating status: " . $stmt->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid status value!');</script>";
                }
            } elseif ($_POST['action'] === 'delete_request') {
                $requestId = intval($_POST['requestId']);

                // Prepare the DELETE statement to remove the request
                $stmt = $conn->prepare("DELETE FROM design_requests WHERE request_id = ?");
                $stmt->bind_param("i", $requestId);

                // Check if the query executes successfully
                if ($stmt->execute()) {
                    echo "<script>alert('Graphic design request deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
                } else {
                    echo "<script>alert('Error deleting request: " . $stmt->error . "');</script>";
                }
            }
        }

        // Fetch design requests from the database
        $result = $conn->query("SELECT request_id, description, uploaded_file, status FROM design_requests");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['request_id']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['status']}</td>
                    <td>";
            // Check if the uploaded file exists
            if (!empty($row['uploaded_file'])) {
                echo "<img src='uploads/{$row['uploaded_file']}' width='100' height='100' alt='Uploaded Image'>";
            } else {
                echo "No image uploaded";
            }
            echo "</td>
                    <td>
                        <!-- Update Status Form -->
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='action' value='update_status'>
                            <input type='hidden' name='requestId' value='{$row['request_id']}' />
                            <select name='status'>
                                <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
                                <option value='in-progress' " . ($row['status'] == 'in-progress' ? 'selected' : '') . ">In Progress</option>
                                <option value='completed' " . ($row['status'] == 'completed' ? 'selected' : '') . ">Completed</option>
                            </select>
                            <button type='submit'>Update</button>
                        </form>

                        <!-- Delete Request Form -->
                        <form method='POST' style='display:inline-block;'>
                            <input type='hidden' name='action' value='delete_request'>
                            <input type='hidden' name='requestId' value='{$row['request_id']}' />
                            <button type='submit'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>
        </table>
<!-- Orders Section -->
<h2>Orders</h2>
<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Total Price</th>
            <th>User ID</th>
            <th>Phone Number</th>
            <th>Street</th>
            <th>City</th>
            <th>Address</th>
            <th>Books (Purchased/Rented)</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // Fetch orders and related book information (purchased and rented)
        $result = $conn->query("SELECT o.order_id, o.totalPrice, o.userId, o.phoneNumber, o.street, o.city, o.address
                                FROM orders o");

        while ($row = $result->fetch_assoc()) {
            $orderId = $row['order_id'];

            // Fetch books in the order (purchased)
            $orderBooksResult = $conn->query("SELECT ob.book_id, b.bookTitle, ob.quantity
                                              FROM order_books ob
                                              JOIN books b ON ob.book_id = b.book_id
                                              WHERE ob.order_id = $orderId");

            // Fetch rented books for the order
            $rentedBooksResult = $conn->query("SELECT rb.bookId, b.bookTitle, rb.rent_until
                                               FROM rented_books rb
                                               JOIN books b ON rb.bookId = b.book_id
                                               WHERE rb.orderId = $orderId");

            // Combine book information for display
            $booksInfo = '';
            while ($orderBook = $orderBooksResult->fetch_assoc()) {
                $booksInfo .= "Purchased: " . $orderBook['bookTitle'] . " (Quantity: " . $orderBook['quantity'] . ")<br>";
            }

            while ($rentedBook = $rentedBooksResult->fetch_assoc()) {
                $booksInfo .= "Rented: " . $rentedBook['bookTitle'] . " (Rent until: " . $rentedBook['rent_until'] . ")<br>";
            }

            echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['totalPrice']}</td>
                    <td>{$row['userId']}</td>
                    <td>{$row['phoneNumber']}</td>
                    <td>{$row['street']}</td>
                    <td>{$row['city']}</td>
                    <td>{$row['address']}</td>
                    <td>$booksInfo</td>
                    <td>
                        <form method='POST' action='delete_order.php' style='display:inline-block;'>
                            <input type='hidden' name='order_id' value='{$row['order_id']}'>
                            <button type='submit' onclick='return confirm(\"Are you sure you want to delete this order?\");'>
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

        <!-- Messages Section -->
        <h2>Messages</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Submitted At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $messagesResult = $conn->query("SELECT id, first_name, last_name, email, phone, message, submitted_at FROM contact_form_submission");
                while ($message = $messagesResult->fetch_assoc()) {
                    echo "<tr>
                            <td>{$message['id']}</td>
                            <td>{$message['first_name']}</td>
                            <td>{$message['last_name']}</td>
                            <td>{$message['email']}</td>
                            <td>{$message['phone']}</td>
                            <td>{$message['message']}</td>
                            <td>{$message['submitted_at']}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>