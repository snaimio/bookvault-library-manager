-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 19, 2026 at 05:17 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `book_library`
--

-- --------------------------------------------------------

--
-- Table structure for table `formats`
--

CREATE TABLE `formats` (
  `formatID` int(11) NOT NULL,
  `formatName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `formats`
--

INSERT INTO `formats` (`formatID`, `formatName`) VALUES
(1, 'Hardcover'),
(2, 'Paperback'),
(3, 'eBook'),
(4, 'Audiobook'),
(5, 'PDF');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `userID` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `emailAddress` varchar(50) NOT NULL,
  `failedAttempts` int(11) DEFAULT 0,
  `lastFailedLogin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`userID`, `userName`, `password`, `emailAddress`, `failedAttempts`, `lastFailedLogin`) VALUES
(1, 'testuser', '$2y$10$L56/lbEQUg8SRxKeSS1OhuCo9dNQKAi012Z6T5ef9h.mwzV5mxuaW', 'test@example.com', 3, '2026-10-19 13:07:25'),
(2, 'testuser2', '$2y$10$Nd3nofRVhvy6ka3rBHqKTepJmR7Ya6iullnkB6uiqmW8CEwIiNsSK', 'test2@example.com', 3, '2026-10-19 13:09:41'),
(4, 'testuser3', '$2y$10$zIo77f12Pmry4/wmG1dZrOYAYKSMQcTusKu6CxVPBxrByqOnuz2l2', 'testuser3@example.com', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `bookID` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `author` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `publishedDate` date NOT NULL,
  `formatID` int(11) NOT NULL,
  `imageName` varchar(150) DEFAULT 'placeholder_100.jpg',
  `userID` int(11) NOT NULL DEFAULT 1,
  `readingStatus` enum('unread','reading','finished','abandoned') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`bookID`, `title`, `author`, `genre`, `isbn`, `publishedDate`, `formatID`, `imageName`, `userID`, `readingStatus`) VALUES
(1, 'Atomic Habits', 'James Clear', 'Self-Improvement', '978-0-7352-1129-2', '2018-10-16', 2, 'atomic_habits_cover_100.jpg', 1, 'unread'),
(2, 'The Art of Thinking Clearly', 'Rolf Dobelli', 'Psychology', '978-0-06-221968-8', '2013-04-01', 1, 'the_art_of_thinking_clearly_cover_100.jpg', 1, 'unread'),
(3, 'Influence: The Psychology of Persuasion', 'Robert Cialdini', 'Psychology', '978-0-06-124189-5', '1984-01-01', 2, 'influence_the_psychology_of_persuasion_cover_100.jpg', 1, 'unread'),
(4, 'Mindset: The New Psychology of Success', 'Carol S. Dweck', 'Psychology', '978-0-345-47232-8', '2006-02-28', 1, 'mindset_the_new_psychology_of_success_cover_100.jpeg', 1, 'unread'),
(5, 'Man\'s Search for Meaning', 'Viktor E. Frankl', 'Memoir', '978-0-8070-1429-5', '1946-01-01', 2, 'man\'s_search_for_meaning_cover_100.jpg', 1, 'unread'),
(6, 'The Psychology of Money', 'Morgan Housel', 'Finance', '978-0-85719-769-6', '2020-09-08', 3, 'the_psychology_of_money_cover_100.jpg', 1, 'unread'),
(7, 'Family Fortunes', 'Bill Bonner & Will Bonner', 'Finance', '978-0-470-45519-6', '2011-04-05', 1, 'family_fortunes_cover_100.jpg', 1, 'unread'),
(8, 'Hold On to Your Kids', 'Gordon Neufeld & Gabor Maté', 'Parenting', '978-0-345-40128-0', '2004-01-01', 2, 'hold_on_to_your_kids_cover_100.jpg', 1, 'unread'),
(9, 'Girls on the Edge', 'Leonard Sax', 'Parenting', '978-0-465-02031-4', '2010-08-24', 1, 'girls_on_the_edge_cover_100.jpg', 1, 'unread'),
(10, 'The Whole-Brain Child', 'Daniel J. Siegel & Tina Payne Bryson', 'Parenting', '978-0-553-38669-1', '2011-10-04', 4, 'the_whole_brain_child_cover_100.jpg', 1, 'unread'),
(11, 'Deep Work', 'Cal Newport', 'Productivity', '978-1-4555-8669-1', '2016-01-05', 1, 'deep_work_cover_100.jpeg', 1, 'unread'),
(12, 'Thinking, Fast and Slow', 'Daniel Kahneman', 'Psychology', '978-0-374-53355-7', '2011-10-25', 2, 'thinking_fast_and_slow_cover_100.jpeg', 1, 'unread'),
(13, 'The Body Keeps the Score', 'Bessel van der Kolk', 'Psychology', '978-0-670-78593-3', '2014-09-25', 2, 'the_body_keeps_the_score_cover_100.jpeg', 1, 'unread'),
(15, 'test book 2', 'test author', 'testing', '234-4444444-444', '2026-09-28', 4, 'Bugs_Bunny_100.png', 1, 'unread'),
(16, 'testuser2title', 'testuser2author', 'testuser2genre', '111222333444', '2001-06-07', 3, 'placeholder_100.jpg', 2, 'unread'),
(17, 'testuer3Title', 'testuer3Author', 'testuer3Testing', '34523413345531345', '1991-07-08', 1, 'Coyote_100.png', 4, 'unread');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `formats`
--
ALTER TABLE `formats`
  ADD PRIMARY KEY (`formatID`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `unique_userName` (`userName`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`bookID`),
  ADD KEY `fk_books_format` (`formatID`),
  ADD KEY `fk_books_user` (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `formats`
--
ALTER TABLE `formats`
  MODIFY `formatID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `bookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_format` FOREIGN KEY (`formatID`) REFERENCES `formats` (`formatID`),
  ADD CONSTRAINT `fk_books_user` FOREIGN KEY (`userID`) REFERENCES `registrations` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
