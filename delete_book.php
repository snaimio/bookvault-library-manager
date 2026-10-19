<?php
/**
 * delete_book.php
 * Handles the Delete Book button from index.php.
 * Receives book_id via POST, fetches the book's image name, deletes the book row,
 * cleans up custom uploaded image files, and redirects to the list.
 */

declare(strict_types=1);

require_once 'auth.php';

// Get the book_id from the POST
$book_id = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);

// If no valid ID was sent, go back to the list
if ($book_id === null || $book_id === false) {
    header('Location: index.php');
    die();
}

require_once('database.php');

// Fetch the book first to get its imageName (SECURED)
$queryBook = 'SELECT imageName FROM books WHERE bookID = :bookID AND userID = :userID';
$statement = $db->prepare($queryBook);
$statement->bindValue(':bookID', $book_id);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$book = $statement->fetch();
$statement->closeCursor();

// If the book doesn't exist or doesn't belong to the user
if (!$book) {
    header('Location: index.php');
    die();
}

// Delete the book (SECURED)
$query = 'DELETE FROM books WHERE bookID = :bookID AND userID = :userID';
$statement = $db->prepare($query);
$statement->bindValue(':bookID', $book_id);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$statement->closeCursor();

// Clean up associated image files (original, _100, _400) if not a placeholder
$image_name = $book['imageName'] ?? '';
$base_dir = 'images/';

if (!empty($image_name) && $image_name !== 'placeholder_100.jpg') {
    $dot_pos = strrpos($image_name, '.');
    if ($dot_pos !== false) {
        $base_name = substr($image_name, 0, $dot_pos);
        $ext = substr($image_name, $dot_pos);

        // Remove the _100 suffix if present to get the original base name
        if (str_ends_with($base_name, '_100')) {
            $base_name = substr($base_name, 0, -4);
        }

        $original_file = $base_dir . $base_name . $ext;
        $img100_file   = $base_dir . $base_name . '_100' . $ext;
        $img400_file   = $base_dir . $base_name . '_400' . $ext;

        foreach ([$original_file, $img100_file, $img400_file] as $file_path) {
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
}

// Go back to the list
header('Location: index.php');
die();