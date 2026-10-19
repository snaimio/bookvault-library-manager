<?php
/**
 * update_book_form.php
 * Shows a pre-filled form for editing a book.
 * Receives book_id via POST from index.php.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['isLoggedIn'])) {
    header('Location: login_form.php');
    die();
}

require('database.php');
require('functions.php');

// Get the book_id from the POST (sent from index.php)
$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);

// If no valid ID was sent, go back to the list
if ($book_id === null || $book_id === false) {
    header('Location: index.php');
    die();
}

// Fetch the book (SECURED: Only fetch if it belongs to the logged-in user)
$queryBook = 'SELECT bookID, title, author, genre, isbn, publishedDate, formatID, imageName
              FROM books
              WHERE bookID = :bookID AND userID = :userID';

$statement = $db->prepare($queryBook);
$statement->bindValue(':bookID', $book_id);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$book = $statement->fetch();
$statement->closeCursor();

// If the book doesn't exist, or doesn't belong to this user, go back to the list
if (!$book) {
    header('Location: index.php');
    die();
}

// Fetch all formats for the dropdown
$queryFormats = 'SELECT formatID, formatName FROM formats ORDER BY formatName';
$statement = $db->prepare($queryFormats);
$statement->execute();
$formats = $statement->fetchAll();
$statement->closeCursor();

$pageTitle = 'Update Book';

require('header.php');
?>

<main>
    <h2>Update Book</h2>

    <form action="update_book.php" method="post" id="update_book_form" enctype="multipart/form-data">
        <!-- Hidden field carries the book ID to the handler -->
        <input type="hidden" name="book_id" value="<?= e((string)$book['bookID']) ?>" />

        <div id="data">
            <label>Title:</label>
            <input type="text" name="title" value="<?= e($book['title']) ?>"><br>

            <label>Author:</label>
            <input type="text" name="author" value="<?= e($book['author']) ?>"><br>

            <label>Genre:</label>
            <input type="text" name="genre" value="<?= e($book['genre']) ?>"><br>

            <label>ISBN:</label>
            <input type="text" name="isbn" value="<?= e($book['isbn']) ?>"><br>

            <label>Published Date:</label>
            <input type="date" name="published_date" value="<?= e($book['publishedDate']) ?>"><br>

            <label>Format:</label>
            <select name="format_id">
                <?php foreach ($formats as $format): ?>
                    <option value="<?= e((string)$format['formatID']) ?>"
                        <?php if ($format['formatID'] == $book['formatID']) echo 'selected'; ?>>
                        <?= e($format['formatName']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label>Current Image:</label>
            <div class="form-image-box">
                <?php
                $current_image = (!empty($book['imageName']) && file_exists('images/' . $book['imageName']))
                    ? $book['imageName']
                    : 'placeholder_100.jpg';
                ?>
                <img
                    id="imagePreview"
                    src="images/<?= e($current_image) ?>"
                    alt="<?= e($book['title']) ?>"
                />
            </div>
            <br>

            <label>Update Image:</label>
            <input type="file" name="file1" id="file1" accept="image/*"><br>
        </div>

        <div id="buttons">
            <label>&nbsp;</label>
            <input type="submit" value="Update Book"><br>
        </div>
    </form>

    <p><a href="index.php">View Book List</a></p>
</main>

<script>
    const fileInput = document.getElementById('file1');
    const imagePreview = document.getElementById('imagePreview');

    if (fileInput && imagePreview) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const imageURL = URL.createObjectURL(file);
                imagePreview.src = imageURL;
            }
        });
    }
</script>

<?php require('footer.php'); ?>