<?php
session_start();

// Check if the session variables exist
$response = [
    'logged_in' => isset($_SESSION['user_id']),  // Check if user is logged in
    'user_email' => $_SESSION['user_email'] ?? null,  // Get the user's email from session if set
];

// Set the content type to JSON
header('Content-Type: application/json');

// Encode the array to JSON and send it as the response
echo json_encode($response);

exit();
?>
