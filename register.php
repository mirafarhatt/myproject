<?php
require_once 'connection.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = trim($_POST['pass']);
    $re_pass = trim($_POST['re_pass']);

    if ($pass !== $re_pass) {
        echo "<script>alert('Passwords do not match!'); window.location.href='sign-up.html';</script>";
        exit();
    }

    // Check if the email already exists
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('User already exists with this email!'); window.location.href='sign-up.html';</script>";
        $stmt->close();
        exit();
    }

    // Insert new user with default role (2 for regular user)
    $hashedPassword = password_hash($pass, PASSWORD_BCRYPT);
    $roleId = 2; // Default to regular user role
    $query = "INSERT INTO users (name, email, password, roleId) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $hashedPassword, $roleId);
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='sign-in.html';</script>";
    } else {
        echo "<script>alert('Error during registration! Please try again.'); window.location.href='sign-up.html';</script>";
    }

    $stmt->close();
}
?>
