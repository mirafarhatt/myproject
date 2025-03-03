<?php
include('connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['bookId'], $data['rating'], $data['comment'])) {
        $bookId = $data['bookId'];
        $rating = $data['rating'];
        $comment = $data['comment'];
        $userId = $_SESSION['user_id']; // Get user ID from session

        
        // Insert review into the BookReview table
        $sql = "INSERT INTO BookReview (userId, bookId, rating, comment, reviewDate) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iiis', $userId, $bookId, $rating, $comment);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error submitting review']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    }
}
?>
