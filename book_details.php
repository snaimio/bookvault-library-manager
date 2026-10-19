<?php
/**
 * book_details.php
 * Shows the full details of a single book, with the larger cover image.
 * Receives book_id via POST from index.php.
 */

declare(strict_types=1);

require_once 'auth.php';

require('database.php');
require('functions.php');

// Get the book_id from the POST
$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);

// If no valid ID was sent, go back to the list
if ($book_id === null || $book_id === false) {
    header('Location: index.php');
    die();
}

// Fetch the book with its format name
$query = 'SELECT b.bookID, b.title, b.author, b.genre, b.isbn, b.publishedDate,
                 b.imageName, f.formatName
          FROM books b
          LEFT JOIN formats f ON b.formatID = f.formatID
          WHERE b.bookID = :bookID';

$statement = $db->prepare($query);
$statement->bindValue(':bookID', $book_id);
$statement->execute();
$book = $statement->fetch();
$statement->closeCursor();

// If the book doesn't exist, go back to the list
if (!$book) {
    header('Location: index.php');
    die();
}

// ----- Build the 400px image filename -----

$image_name = (!empty($book['imageName']) && file_exists('images/' . $book['imageName']))
    ? $book['imageName']
    : 'placeholder_100.jpg';

// Extract the base name and extension
$dot_pos = strrpos($image_name, '.');
$base_name = substr($image_name, 0, $dot_pos);
$extension = substr($image_name, $dot_pos);

// Remove the "_100" suffix if present, so we can switch to "_400"
if (str_ends_with($base_name, '_100')) {
    $base_name = substr($base_name, 0, -4);
}

$image_name_400 = $base_name . '_400' . $extension;

// If the 400px version doesn't exist on disk, fallback to placeholder_400.jpg
if (!file_exists('images/' . $image_name_400)) {
    $image_name_400 = 'placeholder_400.jpg';
}

$pageTitle = 'Book Details';

require('header.php');
?>

<main>
    <div class="container">
        <h2>Book Details</h2>

        <img
            class="book-image"
            src="images/<?= e($image_name_400) ?>"
            alt="<?= e($book['title']) ?>"
        />

        <div class="book-info">
            <p><strong>Title:</strong> <?= e($book['title']) ?></p>
            <p><strong>Author:</strong> <?= e($book['author']) ?></p>
            <p><strong>Genre:</strong> <?= e($book['genre']) ?></p>
            <p><strong>ISBN:</strong> <?= e($book['isbn']) ?></p>
            <p><strong>Published Date:</strong> <?= e($book['publishedDate']) ?></p>
            <p><strong>Format:</strong> <?= e($book['formatName']) ?></p>
        </div>

        <a class="back-link" href="index.php">Back to Book List</a>
    </div>
</main>

<?php require('footer.php'); ?>