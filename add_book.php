<?php
/**
 * add_book.php
 * Handles the Add Book form submission.
 * Requires the user to be logged in.
 */

declare(strict_types=1);

require_once 'auth.php';

// Get form data
$title          = filter_input(INPUT_POST, 'title');
$author         = filter_input(INPUT_POST, 'author');
$genre          = filter_input(INPUT_POST, 'genre');
$isbn           = filter_input(INPUT_POST, 'isbn');
$published_date = filter_input(INPUT_POST, 'published_date');
$format_id      = filter_input(INPUT_POST, 'format_id', FILTER_VALIDATE_INT);

$image = $_FILES['file1'];
$base_dir = 'images/';

require_once('database.php');
require_once('image_util.php');

// ----- Validation -----
if ($title === null || $title === '' ||
    $author === null || $author === '' ||
    $genre === null || $genre === '' ||
    $isbn === null || $isbn === '' ||
    $published_date === null || $published_date === '' ||
    $format_id === null || $format_id === false) {

    $_SESSION['add_error'] = 'Invalid book data. Please fill in all fields and try again.';
    header('Location: add_error.php');
    die();
}

// Duplicate ISBN check
$queryCheck = 'SELECT bookID FROM books WHERE isbn = :isbn';
$statement = $db->prepare($queryCheck);
$statement->bindValue(':isbn', $isbn);
$statement->execute();
$existing = $statement->fetch();
$statement->closeCursor();

if ($existing) {
    $_SESSION['add_error'] = 'A book with that ISBN already exists. Please check the ISBN and try again.';
    header('Location: add_error.php');
    die();
}

// Upload error checks
if ($image !== null && $image['error'] !== UPLOAD_ERR_OK && $image['error'] !== UPLOAD_ERR_NO_FILE) {
    $_SESSION['add_error'] = 'There was a problem uploading your image. Please try again with a smaller file.';
    header('Location: add_error.php');
    die();
}

if ($image !== null && $image['error'] === UPLOAD_ERR_OK && $image['size'] > 5 * 1024 * 1024) {
    $_SESSION['add_error'] = 'The image is too large. Please use a file under 5MB.';
    header('Location: add_error.php');
    die();
}

if ($image !== null && $image['error'] === UPLOAD_ERR_OK) {
    $image_size = getimagesize($image['tmp_name']);
    if ($image_size === false) {
        $_SESSION['add_error'] = 'The uploaded file is not a valid image. Please upload a JPG, PNG, or GIF.';
        header('Location: add_error.php');
        die();
    }
    if ($image_size[0] > 5000 || $image_size[1] > 5000) {
        $_SESSION['add_error'] = 'The image dimensions are too large. Maximum is 5000 × 5000 pixels.';
        header('Location: add_error.php');
        die();
    }
}

// ----- Handle the cover image -----
$image_name = 'placeholder_100.jpg';

if ($image && $image['error'] == UPLOAD_ERR_OK) {
    $original_filename = basename($image['name']);
    $upload_path = $base_dir . $original_filename;
    move_uploaded_file($image['tmp_name'], $upload_path);

    process_image($base_dir, $original_filename);

    $dot_pos = strrpos($original_filename, '.');
    $name_100 = substr($original_filename, 0, $dot_pos) . '_100' . substr($original_filename, $dot_pos);
    $image_name = $name_100;
}

// ----- Insert -----
$query = 'INSERT INTO books (title, author, genre, isbn, publishedDate, formatID, imageName, userID)
          VALUES (:title, :author, :genre, :isbn, :publishedDate, :formatID, :imageName, :userID)';

$statement = $db->prepare($query);
$statement->bindValue(':title', $title);
$statement->bindValue(':author', $author);
$statement->bindValue(':genre', $genre);
$statement->bindValue(':isbn', $isbn);
$statement->bindValue(':publishedDate', $published_date);
$statement->bindValue(':formatID', $format_id);
$statement->bindValue(':imageName', $image_name);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$statement->closeCursor();

$_SESSION['added_title'] = $title;

header('Location: add_book_confirmation.php');
die();