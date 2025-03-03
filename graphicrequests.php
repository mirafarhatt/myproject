<?php
session_start();
require_once 'connection.php';

// Handle graphic design request submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Assume user is logged in
    $requestType = $_POST['requestType'];
    $description = $_POST['description'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $uploadedFile = null;

    // Handle file upload if any
    if (isset($_FILES['uploadFile']) && $_FILES['uploadFile']['error'] === UPLOAD_ERR_OK) {
        // Define upload directory
        $uploadDir = 'uploads/';
        
        // Sanitize file name to avoid issues with special characters
        $fileName = basename($_FILES['uploadFile']['name']);
        $filePath = $uploadDir . $fileName;
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'], $filePath)) {
            $uploadedFile = $fileName;  // Store the file name in the database (not the full path)
        } else {
            echo "<script>alert('Error uploading file!');</script>";
            exit();
        }
    }

    // Prepare SQL statement to insert design request into the database
    $stmt = $conn->prepare("INSERT INTO design_requests (user_id, request_type, description, uploaded_file, email, phone) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $requestType, $description, $uploadedFile, $email, $phone);

    // Execute the statement and handle success or failure
    if ($stmt->execute()) {
        echo "<script>alert('Design request submitted successfully!'); window.location.href='Home.html';</script>";
    } else {
        echo "<script>alert('Error submitting design request!');</script>";
    }
}
?>
