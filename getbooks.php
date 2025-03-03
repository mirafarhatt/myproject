<?php
include('connection.php');

$sql = "
    SELECT b.*, 
           GROUP_CONCAT(r.rating SEPARATOR '|') AS ratings, 
           GROUP_CONCAT(r.comment SEPARATOR '|') AS comments, 
           b.rentalPrice
    FROM Books b
    LEFT JOIN BookReview r ON b.book_id = r.bookId
    GROUP BY b.book_id
";

$result = $conn->query($sql);

$books = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Safeguard for empty ratings and comments
        $ratings = !empty($row['ratings']) ? explode('|', $row['ratings']) : [];
        $comments = !empty($row['comments']) ? explode('|', $row['comments']) : [];

        // Combine ratings and comments into reviews array
        $reviews = [];
        foreach ($ratings as $index => $rating) {
            $reviews[] = [
                'rating' => $rating,
                'comment' => $comments[$index] ?? ''
            ];
        }

        // Add reviews to the book data
        $row['reviews'] = $reviews;

        // Include rental price (ensure it's numeric or null)
        $row['rentalPrice'] = $row['rentalPrice'] ?? null;

        // Remove unnecessary keys
        unset($row['ratings'], $row['comments']);

        // Add the processed book to the books array
        $books[] = $row;
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($books);

$conn->close();
