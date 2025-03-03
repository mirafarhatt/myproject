<?php
session_start(); // Start session management

// Include the connection file
require_once 'connection.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        echo "<script>alert('Email or Password cannot be empty!'); window.location.href='sign-in.html';</script>";
        exit();
    }

    // Check for Admin login with hardcoded credentials
    if ($email === 'admin@us.com' && $password === 'admin') {
        // If it's the admin login, set session variables
        $_SESSION['user_id'] = 'admin'; // This is set for admin login, you might handle admin differently
        $_SESSION['user_email'] = $email;
        
        // Redirect to admin dashboard
        echo "<script>window.location.href='admin_dashboard.php';</script>";
        exit();
    }

    // Query to check if the user exists for regular users
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_email'] = $user['email'];
            
            // Redirect after successful login
            echo "<script>window.location.href='Home.html';</script>";
            exit();
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='sign-in.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('User does not exist!'); window.location.href='sign-in.html';</script>";
        exit();
    }
}
?>
