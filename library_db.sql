-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 03, 2025 at 03:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `authors`
--

CREATE TABLE `authors` (
  `author_id` int(11) NOT NULL,
  `authorFname` varchar(100) NOT NULL,
  `authorLname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `authors`
--

INSERT INTO `authors` (`author_id`, `authorFname`, `authorLname`) VALUES
(1, 'F. Scott', 'Fitzgerald'),
(2, 'J.K.', 'Rowling'),
(3, 'Robert', 'Galbraith'),
(4, 'Paulo', 'Coelho'),
(5, 'Jane', 'Austen'),
(6, 'Harper', 'Lee'),
(7, 'Victor', 'Hugo'),
(14, 'J.K.', 'farhat');

-- --------------------------------------------------------

--
-- Table structure for table `bookreview`
--

CREATE TABLE `bookreview` (
  `review_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` varchar(100) DEFAULT NULL,
  `reviewDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookreview`
--

INSERT INTO `bookreview` (`review_id`, `userId`, `bookId`, `rating`, `comment`, `reviewDate`) VALUES
(7, 1, 4, 8, 'Great Book!', '2025-01-18');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `bookTitle` varchar(200) NOT NULL,
  `categoryId` int(11) NOT NULL,
  `authorId` int(11) NOT NULL,
  `publisherId` int(11) NOT NULL,
  `bookPrice` float NOT NULL,
  `stockQuantity` int(11) NOT NULL DEFAULT 0,
  `rentalPrice` int(11) NOT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `language` varchar(50) NOT NULL,
  `image` varchar(255) NOT NULL,
  `genre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`book_id`, `bookTitle`, `categoryId`, `authorId`, `publisherId`, `bookPrice`, `stockQuantity`, `rentalPrice`, `description`, `language`, `image`, `genre`) VALUES
(1, 'The Gatsby', 1, 1, 1, 10.99, 98, 4, 'A novel set in the Jazz Age, exploring themes of wealth and social mobility.', 'English', 'images/gatsby.jpg', 'Fiction'),
(2, 'Harry Potter and the Sorcerer\'s Stone', 2, 2, 2, 15.99, 197, 6, 'A young wizard embarks on his first year at Hogwarts School of Witchcraft and Wizardry.', 'English', 'images/harry.jpg', 'Fantasy'),
(3, 'The Ink Black Heart', 1, 3, 3, 12.99, 144, 5, 'A detective story set in a contemporary world, exploring technology and murder.', 'English', 'images/theink.jpg', 'Mystery'),
(4, 'The Alchemist', 1, 4, 4, 9.99, 103, 3, 'A young shepherd sets out on a journey to find a hidden treasure.', 'English', 'images/thealch.jpg', 'Adventure'),
(5, 'Pride and Prejudice', 1, 5, 5, 7.99, 75, 3, 'A romantic novel set in the English countryside, focusing on Elizabeth Bennet and Mr. Darcy.', 'English', 'images/pp.jpg', 'Romance'),
(6, 'To Kill a Mockingbird', 1, 6, 4, 8.99, 48, 3, 'A coming-of-age story set in the American South, focusing on racial inequality.', 'English', 'images/mockingbird.png', 'Historical'),
(7, 'Les Miserables', 1, 7, 5, 15.9, 0, 7, 'A historical novel set in post-revolutionary France, focusing on themes of justice and redemption.', 'French', 'images/les.jpg', 'Historical Fiction');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` time NOT NULL,
  `isRental` tinyint(1) NOT NULL DEFAULT 0,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `categoryName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `categoryName`) VALUES
(1, 'Fiction'),
(2, 'Fantasy'),
(3, 'Classic'),
(4, 'Romance'),
(5, 'Historical Fiction');

-- --------------------------------------------------------

--
-- Table structure for table `contact_form_submission`
--

CREATE TABLE `contact_form_submission` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `message` text NOT NULL,
  `submitted_at` datetime NOT NULL,
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_form_submission`
--

INSERT INTO `contact_form_submission` (`id`, `first_name`, `last_name`, `email`, `phone`, `message`, `submitted_at`, `userId`) VALUES
(14, 'mira', 'farhat', 'mohmd_awada@outlook.com', '71667617', 'hi', '2025-01-19 15:20:43', 1);

-- --------------------------------------------------------

--
-- Table structure for table `design_requests`
--

CREATE TABLE `design_requests` (
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_type` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending',
  `description` varchar(1000) NOT NULL,
  `uploaded_file` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `design_requests`
--

INSERT INTO `design_requests` (`request_id`, `user_id`, `request_type`, `created_at`, `status`, `description`, `uploaded_file`, `email`, `phone`) VALUES
(4, 4, 'other', '2025-01-20 10:09:20', 'Pending', 'book', 'Screenshot 2022-10-26 174036.png', 'mira2004farhat@gmail.com', '+96171667617');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `totalPrice` float NOT NULL,
  `userId` int(11) NOT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `totalPrice`, `userId`, `phoneNumber`, `street`, `city`, `address`) VALUES
(20, 19.9, 4, '71667617', 'beirut,borj el brajneh,rweis', 'borj el brajneh', 'beirut,borj el brajneh,rweis');

-- --------------------------------------------------------

--
-- Table structure for table `order_books`
--

CREATE TABLE `order_books` (
  `order_book_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_books`
--

INSERT INTO `order_books` (`order_book_id`, `order_id`, `book_id`, `quantity`) VALUES
(21, 20, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE `publishers` (
  `publisher_id` int(11) NOT NULL,
  `publisherName` varchar(50) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publishers`
--

INSERT INTO `publishers` (`publisher_id`, `publisherName`, `address`) VALUES
(1, 'Scribner', '123 Scribner St, New York'),
(2, 'Bloomsbury', '50 Bedford Square, London'),
(3, 'Little, Brown and Company', '1290 Avenue of the Americas, New York'),
(4, 'HarperCollins', '195 Broadway, New York'),
(5, 'Penguin Classics', '80 Strand, London'),
(12, 'w', ''),
(13, 'd', '');

-- --------------------------------------------------------

--
-- Table structure for table `rented_books`
--

CREATE TABLE `rented_books` (
  `rental_id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `bookId` int(11) NOT NULL,
  `rent_until` date NOT NULL,
  `rented_at` date NOT NULL,
  `orderId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rented_books`
--

INSERT INTO `rented_books` (`rental_id`, `userId`, `bookId`, `rent_until`, `rented_at`, `orderId`) VALUES
(14, 4, 1, '2025-01-27', '2025-01-20', 20);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT 'N/A',
  `email` varchar(100) NOT NULL,
  `roleId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `password`, `name`, `email`, `roleId`) VALUES
(1, '$2y$10$jbb95JoLGDloXn.ex9OaRuf6uXq5LZl7H9ScDH8/CIrEQQGWWarPi', 'mira farhat', 'marofarhat2004@gmail.com', 2),
(2, '$2y$10$NNNNG.m3hazOKXA9lclVf.H7iHeTFPl.hssNa.EXeP27CugI5aq2W', 'mahdi', 'mahdi@gmail.com', 2),
(3, '$2y$10$K5sgTcZpWlBnr1ThLVMHluKp56TRvLSMP8FmUWI0fP6ahNLqmXgvC', 'hadeel fahes', 'hadeel@gmail.com', 2),
(4, '$2y$10$nDB3yl6mtH4TmrM.8hH9Hu2N9STJoZ2yRwzhOe.CQhOJfrKj4JM9y', 'dr mustapha', 'm@bbb.com', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`author_id`);

--
-- Indexes for table `bookreview`
--
ALTER TABLE `bookreview`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `bookId` (`bookId`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD UNIQUE KEY `bookTitle` (`bookTitle`),
  ADD KEY `categoryId` (`categoryId`),
  ADD KEY `authorId` (`authorId`),
  ADD KEY `publisherId` (`publisherId`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `bookId` (`bookId`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `contact_form_submission`
--
ALTER TABLE `contact_form_submission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `design_requests`
--
ALTER TABLE `design_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `order_books`
--
ALTER TABLE `order_books`
  ADD PRIMARY KEY (`order_book_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `publishers`
--
ALTER TABLE `publishers`
  ADD PRIMARY KEY (`publisher_id`),
  ADD UNIQUE KEY `publisherName` (`publisherName`);

--
-- Indexes for table `rented_books`
--
ALTER TABLE `rented_books`
  ADD PRIMARY KEY (`rental_id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `bookId` (`bookId`),
  ADD KEY `orderId` (`orderId`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `roleId` (`roleId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `authors`
--
ALTER TABLE `authors`
  MODIFY `author_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bookreview`
--
ALTER TABLE `bookreview`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `contact_form_submission`
--
ALTER TABLE `contact_form_submission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `design_requests`
--
ALTER TABLE `design_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_books`
--
ALTER TABLE `order_books`
  MODIFY `order_book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `publishers`
--
ALTER TABLE `publishers`
  MODIFY `publisher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `rented_books`
--
ALTER TABLE `rented_books`
  MODIFY `rental_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookreview`
--
ALTER TABLE `bookreview`
  ADD CONSTRAINT `bookreview_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookreview_ibfk_2` FOREIGN KEY (`bookId`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `category` (`category_id`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`authorId`) REFERENCES `authors` (`author_id`),
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`publisherId`) REFERENCES `publishers` (`publisher_id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`bookId`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `contact_form_submission`
--
ALTER TABLE `contact_form_submission`
  ADD CONSTRAINT `contact_form_submission_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `design_requests`
--
ALTER TABLE `design_requests`
  ADD CONSTRAINT `design_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_books`
--
ALTER TABLE `order_books`
  ADD CONSTRAINT `order_books_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_books_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`);

--
-- Constraints for table `rented_books`
--
ALTER TABLE `rented_books`
  ADD CONSTRAINT `rented_books_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `rented_books_ibfk_2` FOREIGN KEY (`bookId`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `rented_books_ibfk_3` FOREIGN KEY (`orderId`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`roleId`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
