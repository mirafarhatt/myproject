<?php
// Include the database connection file
require 'connection.php';

// Response array
$response = [
    'status' => 'error',
    'message' => 'An error occurred while submitting the form.'
];

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize inputs
    $firstName = htmlspecialchars($_POST['first-name']);
    $lastName = htmlspecialchars($_POST['last-name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);
    $userId = 1; // Replace with logic to get the user's ID if applicable

    // Insert data into the table
    $sql = "INSERT INTO contact_form_submission 
            (first_name, last_name, email, phone, message, submitted_at, userId) 
            VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param('sssssi', $firstName, $lastName, $email, $phone, $message, $userId);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Form submitted successfully!';
        } else {
            $response['message'] = 'Error executing query: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'Error preparing query: ' . $conn->error;
    }
}

$conn->close();

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
