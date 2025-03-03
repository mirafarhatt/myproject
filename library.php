<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body */
        body {
            font-family: Arial, sans-serif;
            color: #555;
            padding-top: 50px;
            background: #f4f4f4;
        }

        /* Library Title */
        .library-title h1 {
            text-align: center;
            font-size: 3rem;
            color: #6a1b9a; /* Purple */
            text-transform: uppercase;
            margin-bottom: 50px;
        }

        /* Search Box */
        .search-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .search-container input {
            width: 50%;
            margin: 0 auto;
        }

        /* Book Grid */
        .book-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            padding: 0 15px;
        }

        .book-item {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .book-item:hover {
            transform: scale(1.05);
        }

        .book-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .book-item .book-info {
            padding: 15px;
        }

        .book-item .book-info h4 {
            font-size: 1.2rem;
            color: #333;
        }

        .book-item .book-info p {
            color: #777;
            font-size: 0.9rem;
        }

        /* Rent and Buy Buttons */
        .book-item .actions {
            margin-top: 10px;
            display: flex;
            justify-content: space-around;
            padding: 10px;
        }

        .book-item .actions button {
            padding: 10px 20px;
            background-color: #d32f2f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .book-item .actions button:hover {
            background-color: #c62828;
        }

        /* Review Form Styles */
        .review-form {
            margin-top: 20px;
            display: none;
            padding: 15px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .review-form select, .review-form textarea {
            width: 100%;
            padding: 8px;
            margin: 5px 0;
        }

        .review-form button {
            background-color: #3e8e41;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
        }

        .review-form button:hover {
            background-color: #2e7031;
        }
    </style>
</head>

<body>
    <header style="margin-bottom: 100px;"></header>

    <div class="library-title">
        <h1>Library Collection</h1>
    </div>

    <!-- Search Box -->
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search for books..." class="form-control">
    </div>

    <div style="margin-left: 50px; margin-right: 50px;">
        <div class="book-grid" id="book-grid">
            <!-- Dynamic book items will be injected here by JavaScript -->
        </div>
    </div>

    <footer style="margin-top: 50px;"></footer>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/html/footer.js"></script>
    <script src="js/html/header.js"></script>
    <script>
    // Correct way to inject PHP session value into JS
    const userId = <?php echo isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null'; ?>;

    // Fetch books and render them on the page
    fetch('getbooks.php')
        .then(response => response.json())
        .then(books => {
            const bookGrid = document.getElementById('book-grid');

            books.forEach(book => {
                // Create book item element
                const bookItem = document.createElement('div');
                bookItem.classList.add('book-item');

                // Create a string for reviews section
                let reviewsHTML = '';
                if (book.reviews && book.reviews.length > 0) {
                    reviewsHTML = '<div class="reviews-section">';
                    book.reviews.forEach(review => {
                        reviewsHTML += `
                            <div class="review-item">
                                <p><strong>Rating:</strong> ${review.rating} Stars</p>
                                <p><strong>Comment:</strong> ${review.comment}</p>
                            </div>
                             <hr class="review-divider">
                        `;
                    });
                    reviewsHTML += '</div>';
                } else {
                    reviewsHTML = '<p>No reviews yet.</p>';
                }

                // Populate the book item with HTML
                bookItem.innerHTML = `
                    <img src="${book.image}" alt="${book.bookTitle}">
                    <div class="book-info">
                        <h4>${book.bookTitle}</h4>
                        <p>${book.genre}</p>
                        <p>${book.description}</p>
                        <p><strong>Price: $${book.bookPrice}</strong></p>
                        <p><strong>Rental Price: $${book.rentalPrice}</strong></p> <!-- Rental price added here -->
                    </div>
                    <div class="actions">
                        <button class="rent-btn" data-id="${book.book_id}" data-rental-price="${book.rentalPrice}">Rent</button>
                        <button class="buy-btn" data-id="${book.book_id}" data-price="${book.bookPrice}">Buy</button>
                        <button class="review-btn" data-id="${book.book_id}">Review</button>
                    </div>

                    <!-- Review Form -->
                    <div class="review-form" id="review-form-${book.book_id}">
                        <h5>Write a Review</h5>
                        <label for="rating-${book.book_id}">Rating:</label>
                        <select id="rating-${book.book_id}">
                            <option value="1">1 Star</option>
                            <option value="2">2 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="5">5 Stars</option>
                            <option value="6">6 Star</option>
                            <option value="7">7 Stars</option>
                            <option value="8">8 Stars</option>
                            <option value="9">9 Stars</option>
                            <option value="10">10 Stars</option>
                        </select>
                        <br>
                        <label for="comment-${book.book_id}">Comment:</label>
                        <textarea id="comment-${book.book_id}" rows="4" cols="50"></textarea>
                        <br>
                        <button class="submit-review" data-id="${book.book_id}">Submit Review</button>
                    </div>

                    <!-- Reviews section -->
                    <div class="reviews-container" id="reviews-container-${book.book_id}" style="display: none;">
                        ${reviewsHTML}
                    </div>
                `;
                bookItem.querySelector('.rent-btn').addEventListener('click', (e) => {
                    const bookId = e.target.getAttribute('data-id');
                    const rentalPrice = parseFloat(e.target.getAttribute('data-rental-price')).toFixed(2); // Ensure it is decimal formatted

                    console.log(`Book ID: ${bookId}, Rental Price: $${rentalPrice}`); // Log rentalPrice here

                    if (userId === null) {
                        alert('Please log in to rent items.');
                        return;
                    }

                    // Show rental price in a confirmation dialog
                    if (!confirm(`The rental price for this book is $${rentalPrice}. Do you want to proceed?`)) {
                        return;
                    }

                    fetch('addToCartForRent.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ userId, bookId, rentalPrice, isRental: 1, quantity: 1 })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Book has been rented successfully.');
                        } else {
                            alert(data.message || 'Failed to rent the book.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });

                // Handle Buy button click
                bookItem.querySelector('.buy-btn').addEventListener('click', (e) => {
                    const bookId = e.target.getAttribute('data-id');
                    const price = e.target.getAttribute('data-price');

                    if (userId === null) {
                        alert('Please log in to buy items.');
                        return;
                    }
                    fetch('addToCartForPurchase.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ userId, bookId, price, isRental: 0 })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Book has been added to cart for purchase.');
                        } else {
                            alert(data.message || 'Failed to add book to cart for purchase.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });

                // Append the book item to the grid
                bookGrid.appendChild(bookItem);

                // Show review form when "Review" button is clicked
                bookItem.querySelector('.review-btn').addEventListener('click', () => {
                    const allReviewForms = document.querySelectorAll('.review-form');
                    allReviewForms.forEach(form => {
                        if (form.style.display === 'block') {
                            form.style.display = 'none';
                        }
                    });

                    const reviewForm = document.getElementById(`review-form-${book.book_id}`);
                    reviewForm.style.display = reviewForm.style.display === 'block' ? 'none' : 'block';
                });

                // Toggle reviews visibility when book item is clicked
                bookItem.addEventListener('click', () => {
                    const reviewsContainer = document.getElementById(`reviews-container-${book.book_id}`);
                    reviewsContainer.style.display = reviewsContainer.style.display === 'none' ? 'block' : 'none';
                });

                // Submit review
                bookItem.querySelector('.submit-review').addEventListener('click', () => {
                    console.log('Submit Review button clicked!'); // Debug log
                    const rating = document.getElementById(`rating-${book.book_id}`).value;
                    const comment = document.getElementById(`comment-${book.book_id}`).value;
                    if (userId === null) {
                        alert('Please log in to review the book!');
                        return;
                    }


                    fetch('submitReview.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            userId,
                            bookId: book.book_id,
                            rating,
                            comment,
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Review submitted successfully.');
                            // Hide the review form
                            document.getElementById(`review-form-${book.book_id}`).style.display = 'none';   location.reload();
                        } else {
                            alert(data.message || 'Failed to submit the review.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        })
        .catch(error => console.error('Error:', error));

    // Search functionality
    document.getElementById('search-input').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const bookItems = document.querySelectorAll('.book-item');

        bookItems.forEach(bookItem => {
            const title = bookItem.querySelector('.book-info h4').textContent.toLowerCase();
            const genre = bookItem.querySelector('.book-info p').textContent.toLowerCase();

            if (title.includes(searchTerm) || genre.includes(searchTerm)) {
                bookItem.style.display = 'block';
            } else {
                bookItem.style.display = 'none';
            }
        });
    });
    </script>

</body>

</html>
