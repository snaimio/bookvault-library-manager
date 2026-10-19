<?php
/**
 * index.php
 * Home page. Fetches all books (with their format) and shows them in a table.
 * Requires the user to be logged in.
 */

declare(strict_types=1);

require_once 'auth.php';

require('database.php');
require('functions.php');

$pageTitle = 'Home';

// Pull ONLY the books that belong to the logged-in user
$query = 'SELECT b.bookID, b.title, b.author, b.genre, b.isbn, b.publishedDate,
                 b.imageName, f.formatName
          FROM books b
          LEFT JOIN formats f ON b.formatID = f.formatID
          WHERE b.userID = :userID
          ORDER BY b.title';

$statement = $db->prepare($query);
$statement->bindValue(':userID', $_SESSION['userID'], PDO::PARAM_INT);
$statement->execute();
$rows = $statement->fetchAll();
$statement->closeCursor();

require('header.php');
?>

<main>
    <h2>My Book Collection</h2>

    <p style="text-align: right;">
        Logged in as <strong><?= e($_SESSION['userName']) ?></strong> |
        <a href="logout.php">Log Out</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>Cover</th>
                <th>Title</th>
                <th>Author</th>
                <th>Genre</th>
                <th>ISBN</th>
                <th>Published</th>
                <th>Format</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr><td colspan="10">No books found in your collection.</td></tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <?php
                            $row_image = (!empty($row['imageName']) && file_exists('images/' . $row['imageName']))
                                ? $row['imageName']
                                : 'placeholder_100.jpg';
                            ?>
                            <img
                                src="images/<?= e($row_image) ?>"
                                alt="<?= e($row['title']) ?>"
                                height="60"
                            />
                        </td>
                        <td><?= e($row['title']) ?></td>
                        <td><?= e($row['author']) ?></td>
                        <td><?= e($row['genre']) ?></td>
                        <td><?= e($row['isbn']) ?></td>
                        <td><?= e($row['publishedDate']) ?></td>
                        <td><?= e($row['formatName']) ?></td>

                        <td>
                            <form action="update_book_form.php" method="post">
                                <input type="hidden" name="book_id" value="<?= e((string)$row['bookID']) ?>" />
                                <input type="submit" value="Update" class="btn-update" />
                            </form>
                        </td>

                        <td>
                            <form action="delete_book.php" method="post"
                                  onsubmit="return confirm('Delete this book?');">
                                <input type="hidden" name="book_id" value="<?= e((string)$row['bookID']) ?>" />
                                <input type="submit" value="Delete" class="btn-delete" />
                            </form>
                        </td>

                        <td>
                            <form action="book_details.php" method="post">
                                <input type="hidden" name="book_id" value="<?= e((string)$row['bookID']) ?>" />
                                <input type="submit" value="View Details" class="btn-view" />
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <p><a href="add_book_form.php">Add New Book</a></p>
</main>

<?php require('footer.php'); ?>