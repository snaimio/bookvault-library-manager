<?php
/**
 * update_book.php
 * Handles the Update Book form submission.
 * Validates input, handles image upload, then UPDATEs the book in the database.
 */

declare(strict_types=1);

require_once 'auth.php';

// Get form data
$book_id        = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
$title          = filter_input(INPUT_POST, 'title');
$author         = filter_input(INPUT_POST, 'author');
$genre          = filter_input(INPUT_POST, 'genre');
$isbn           = filter_input(INPUT_POST, 'isbn');
$published_date = filter_input(INPUT_POST, 'published_date');
$format_id      = filter_input(INPUT_POST, 'format_id', FILTER_VALIDATE_INT);

// Get the uploaded file (if any)
$image = $_FILES['file1'];

require_once('database.php');
require_once('image_util.php');

$base_dir = 'images/';

// ----- Validation -----
if ($book_id === null || $book_id === false) {
    $_SESSION['update_error'] = 'Invalid book reference. Please try again.';
    header('Location: update_error.php');
    die();
}

if ($title === null || $title === '' ||
    $author === null || $author === '' ||
    $genre === null || $genre === '' ||
    $isbn === null || $isbn === '' ||
    $published_date === null || $published_date === '' ||
    $format_id === null || $format_id === false) {

    $_SESSION['update_error'] = 'Invalid book data. Please fill in all fields and try again.';
    header('Location: update_error.php');
    die();
}

// Check for duplicate ISBN — EXCLUDING the current book
$queryCheck = 'SELECT bookID FROM books WHERE isbn = :isbn AND bookID != :bookID';
$statement = $db->prepare($queryCheck);
$statement->bindValue(':isbn', $isbn);
$statement->bindValue(':bookID', $book_id);
$statement->execute();
$existing = $statement->fetch();
$statement->closeCursor();

if ($existing) {
    $_SESSION['update_error'] = 'Another book already uses that ISBN. Please check the ISBN and try again.';
    header('Location: update_error.php');
    die();
}

// ----- Fetch the current book (SECURED) -----
$queryBook = 'SELECT imageName FROM books WHERE bookID = :bookID AND userID = :userID';
$statement = $db->prepare($queryBook);
$statement->bindValue(':bookID', $book_id);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$current = $statement->fetch();
$statement->closeCursor();

if (!$current) {
    $_SESSION['update_error'] = 'Book not found or you do not have permission to edit it.';
    header('Location: update_error.php');
    die();
}

// Check for upload errors
if ($image !== null && $image['error'] !== UPLOAD_ERR_OK && $image['error'] !== UPLOAD_ERR_NO_FILE) {
    $_SESSION['update_error'] = 'There was a problem uploading your image. Please try again with a smaller file.';
    header('Location: update_error.php');
    die();
}

// Check for file size limit (5MB max)
if ($image !== null && $image['error'] === UPLOAD_ERR_OK && $image['size'] > 5 * 1024 * 1024) {
    $_SESSION['update_error'] = 'The image is too large. Please use a file under 5MB.';
    header('Location: update_error.php');
    die();
}

// Check for valid image content and dimensions (max 5000x5000)
if ($image !== null && $image['error'] === UPLOAD_ERR_OK) {
    $image_size = getimagesize($image['tmp_name']);

    if ($image_size === false) {
        $_SESSION['update_error'] = 'The uploaded file is not a valid image. Please upload a JPG, PNG, or GIF.';
        header('Location: update_error.php');
        die();
    }

    $image_width  = $image_size[0];
    $image_height = $image_size[1];

    if ($image_width > 5000 || $image_height > 5000) {
        $_SESSION['update_error'] = 'The image dimensions are too large. Maximum is 5000 × 5000 pixels.';
        header('Location: update_error.php');
        die();
    }
}

// ----- Handle the cover image -----
$image_name = $current['imageName'] ?? 'placeholder_100.jpg';
$old_image_name = $image_name;

if ($image && $image['error'] == UPLOAD_ERR_OK) {
    $original_filename = basename($image['name']);
    $upload_path = $base_dir . $original_filename;
    move_uploaded_file($image['tmp_name'], $upload_path);

    process_image($base_dir, $original_filename);

    $dot_pos = strrpos($original_filename, '.');
    $new_image_name = substr($original_filename, 0, $dot_pos) . '_100' . substr($original_filename, $dot_pos);

    $image_name = $new_image_name;

    if ($old_image_name != 'placeholder_100.jpg') {
        $old_base = substr($old_image_name, 0, strrpos($old_image_name, '_100'));
        $old_ext  = substr($old_image_name, strrpos($old_image_name, '.'));
        $original_old = $old_base . $old_ext;

        $img100 = $old_base . '_100' . $old_ext;
        $img400 = $old_base . '_400' . $old_ext;

        foreach ([$original_old, $img100, $img400] as $file) {
            $path = $base_dir . $file;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}

// ----- Update the book (SECURED) -----
$query = 'UPDATE books
          SET title = :title,
              author = :author,
              genre = :genre,
              isbn = :isbn,
              publishedDate = :publishedDate,
              formatID = :formatID,
              imageName = :imageName
          WHERE bookID = :bookID AND userID = :userID';

$statement = $db->prepare($query);
$statement->bindValue(':title', $title);
$statement->bindValue(':author', $author);
$statement->bindValue(':genre', $genre);
$statement->bindValue(':isbn', $isbn);
$statement->bindValue(':publishedDate', $published_date);
$statement->bindValue(':formatID', $format_id);
$statement->bindValue(':imageName', $image_name);
$statement->bindValue(':bookID', $book_id);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$statement->closeCursor();

$_SESSION['updated_title'] = $title;

header('Location: update_book_confirmation.php');
die();