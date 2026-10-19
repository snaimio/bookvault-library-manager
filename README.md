# Book Library — PHP + MySQL

## Assignment Progression
- **A1:** Database design (books, formats)
- **A2:** Full CRUD with PDO
- **A3:** Image upload + thumbnail generation (GD)
- **A4:** User authentication (registration, login, lockout)
- **A5:** Per-user data isolation + security hardening

## Features
- User registration, login, and logout with secure password hashing (`password_hash`, `password_verify`)
- Account lockout after 3 failed login attempts (5-minute cooldown with live JavaScript countdown)
- Centralized authentication gate (`auth.php`) enforcing login guards across all protected routes
- Per-user book collection and data isolation (each user sees only their own books)
- Full CRUD operations on books (Add, List, Update, Delete, View Details)
- Multipart cover image uploads with automatic `_100` thumbnail and `_400` details image generation via GD
- Dedicated book details view (`book_details.php`) showcasing full book metadata and enlarged cover
- Responsive design with CSS media queries adapting layout and forms for mobile and tablet devices
- User feedback and error reporting for validation errors and account lockouts

## Screenshots

![Catalog View](screenshots/all_books_catalog.png)
![Book Details](screenshots/book_details_1.png)
![Book Details](screenshots/book_details_2.png)
![Update Book Form](screenshots/update_book_form.png)

## Setup
1. Start XAMPP (Apache + MySQL).
2. Import `database.sql` into phpMyAdmin.
3. Update `database.php` with your credentials.
4. Visit `http://localhost/PHPAssignment1/login_form.php`.

## Test Per-User Isolation
1. Register `userA` and add 3 books.
2. Log out, register `userB`, log in. Verify you see NO books.
3. Add a book as `userB`.
4. Log back in as `userA`. Verify you see your 3 books, not userB's.

## Security
- All queries use PDO prepared statements
- All output escaped with htmlspecialchars/e()
- Passwords hashed with password_hash()
- File uploads validated by extension, MIME, size, dimensions
- Session regeneration on login
- Account lockout on failed attempts
- Per-user query scoping on every SELECT/UPDATE/DELETE
- Error logging via error_log()

## Author
Sheikh Naim