<?php
/**
 * add_book_form.php
 * Displays an empty form to add a new book.
 * Requires the user to be logged in.
 */

declare(strict_types=1);

require_once 'auth.php';

require('database.php');
require('functions.php');

// Fetch all formats for the dropdown
$query = 'SELECT formatID, formatName FROM formats ORDER BY formatName';
$statement = $db->prepare($query);
$statement->execute();
$formats = $statement->fetchAll();
$statement->closeCursor();

$pageTitle = 'Add Book';

require('header.php');
?>

<main>
    <h2>Add Book</h2>

    <form action="add_book.php" method="post" id="add_book_form" enctype="multipart/form-data">
        <div id="data">
            <label>Title:</label>
            <input type="text" name="title"><br>

            <label>Author:</label>
            <input type="text" name="author"><br>

            <label>Genre:</label>
            <input type="text" name="genre"><br>

            <label>ISBN:</label>
            <input type="text" name="isbn"><br>

            <label>Published Date:</label>
            <input type="date" name="published_date"><br>

            <label>Format:</label>
            <select name="format_id">
                <?php foreach ($formats as $format): ?>
                    <option value="<?= e((string)$format['formatID']) ?>">
                        <?= e($format['formatName']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label>Cover Image:</label>
            <input type="file" name="file1" accept="image/*"><br>
        </div>

        <div id="buttons">
            <label>&nbsp;</label>
            <input type="submit" value="Add Book"><br>
        </div>
    </form>

    <p><a href="index.php">View Book List</a></p>
</main>

<?php require('footer.php'); ?>