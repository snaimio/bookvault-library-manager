# 📚 Book Library — Full-Stack PHP & MySQL Web Application

[![PHP Version](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-MariaDB-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Web Server](https://img.shields.io/badge/Server-Apache%20%2F%20XAMPP-D22128?style=flat-square&logo=apache&logoColor=white)](https://www.apachefriends.org/)
[![License](https://img.shields.io/badge/License-MIT-blue.svg?style=flat-square)](LICENSE)

A robust, secure, and production-grade library management application built with native **PHP 8** and **MySQL/MariaDB**. Developed incrementally across five curriculum milestones (**Assignment 1 through Assignment 5**), the system features relational data modeling, full CRUD workflows, an automated GD image resizing pipeline, multi-tenant per-user data isolation, and brute-force account lockout protection.

---

## 📑 Table of Contents
- [Assignment Progression](#-assignment-progression)
- [Key Features](#-key-features)
- [Screenshots & Visual Walkthrough](#-screenshots--visual-walkthrough)
  - [Authentication Flow](#1-authentication--account-security)
  - [Catalog & Management Flow](#2-catalog-management--book-details)
- [System Architecture & Database Schema](#-system-architecture--database-schema)
- [Installation & Setup](#-installation--setup)
- [Testing Per-User Isolation](#-testing-per-user-isolation)
- [Security Architecture](#-security-architecture)
- [Project Structure](#-project-structure)
- [Author & License](#-author--license)

---

## 🚀 Assignment Progression

| Milestone | Focus Area | Core Implementations |
| :--- | :--- | :--- |
| **Assignment 1** | **Database Architecture** | Relational schema design (`books`, `formats`), foreign key constraints, centralized PDO database connection, and basic catalog view. |
| **Assignment 2** | **Full CRUD Operations** | Server-side validation, duplicate ISBN collision detection, prepared statements, and complete Create, Read, Update, and Delete workflows. |
| **Assignment 3** | **Image Processing & Details** | Multipart file upload management, automated GD thumbnail generation (`_100` thumbnails, `_400` details images), and dedicated book details view. |
| **Assignment 4** | **Authentication & Lockout** | User registration, Bcrypt password hashing, session state tracking, brute-force lockout (3 failed attempts / 5 minutes), and live countdown timer. |
| **Assignment 5** | **Data Isolation & Hardening** | Multi-tenant user data isolation (`userID` foreign key scoping across all queries), centralized auth guard (`auth.php`), and error logging. |

---

## ✨ Key Features

- **Multi-Tenant User Isolation:** Every book entry is mapped to a specific `userID`. Users strictly interact with their own private catalog. Cross-user data contamination and unauthorized access are prevented at both the application and database layer.
- **Secure Authentication & Session Lifecycle:** User registration and login utilizing native PHP password hashing (`password_hash` with `PASSWORD_DEFAULT`). Sessions are protected against session fixation through `session_regenerate_id(true)`.
- **Brute-Force Lockout Defense:** Automatically tracks consecutive failed login attempts in the database. When an account reaches 3 failed attempts, it is locked for 5 minutes (300 seconds), supported by a real-time JavaScript countdown timer on the login screen.
- **Centralized Authentication Guard:** Dedicated `auth.php` entry guard enforcing strict session checks and automatic redirection to `login_form.php` across all protected routes.
- **Full CRUD Book Management:** Intuitive interfaces for creating, listing, viewing, updating, and deleting book records with relational format mapping.
- **Automated GD Image Processing Pipeline:**
  - Automated image resizing preserving aspect ratios.
  - Generates `_100` thumbnails ($100\times100\text{px}$) for catalog and form previews.
  - Generates `_400` medium images ($300\times300\text{px}$) for the detailed viewer.
  - Automated cleanup of replaced cover files while preserving default fallback assets.
- **Responsive & Accessible Interface:** Clean CSS architecture with custom variables, mobile-friendly table scrolling (`.table-responsive`), and tablet/phone media query breakpoints.

---

## 📸 Screenshots & Visual Walkthrough

### 1. Authentication & Account Security

| User Registration | User Login |
| :---: | :---: |
| ![User Registration](screenshots/register_user_form.png) | ![User Login](screenshots/login_form.png) |
| *Server-side validation for unique username, password, and email* | *Secure login with credential verification and brute-force tracking* |

| Inline Validation & Errors | Authenticated Welcome |
| :---: | :---: |
| ![Login Error](screenshots/login_error.png) | ![Login Confirmation](screenshots/login_confirmation.png) |
| *Clear feedback on missing fields or incorrect credentials* | *Session-confirmed dashboard redirect after successful login* |

---

### 2. Catalog Management & Book Details

#### Catalog View (Per-User Books)
![Catalog View](screenshots/all_books_catalog.png)
*Personalized book catalog displaying only the authenticated user's collection, cover thumbnails, format badges, and action controls.*

#### Update Book & Dynamic Image Preview
![Update Book Form](screenshots/update_book_form.png)
*Pre-populated editing form featuring real-time client-side image preview and format selection.*

#### Comprehensive Book Details View
| Book Details View (1) | Book Details View (2) |
| :---: | :---: |
| ![Book Details 1](screenshots/book_details_1.png) | ![Book Details 2](screenshots/book_details_2.png) |
| *High-resolution cover presentation with full relational metadata* | *Clean metadata display and navigation back to catalog* |

---

## 🗄️ System Architecture & Database Schema

The database utilizes InnoDB with strict foreign key constraints and UTF-8 collation (`utf8mb4_general_ci`).

```
                    ┌───────────────────────────┐
                    │       registrations       │
                    ├───────────────────────────┤
                    │ PK  userID (INT, AI)      │
                    │     userName (VARCHAR)    │
                    │     password (VARCHAR)    │
                    │     emailAddress (VARCHAR)│
                    │     failedAttempts (INT)  │
                    │     lastFailedLogin (DATETIME)
                    └─────────────┬─────────────┘
                                  │
                                  │ 1:N (fk_books_user)
                                  ▼
┌───────────────────────┐   ┌───────────────────────────┐
│        formats        │   │           books           │
├───────────────────────┤   ├───────────────────────────┤
│ PK  formatID (INT, AI)│   │ PK  bookID (INT, AI)      │
│     formatName(VARCHAR│   │     title (VARCHAR)       │
└───────────┬───────────┘   │     author (VARCHAR)      │
            │               │     genre (VARCHAR)       │
            │ 1:N           │     isbn (VARCHAR)        │
            │ (fk_books_format) publishedDate (DATE)    │
            └──────────────►│ FK  formatID (INT)        │
                            │     imageName (VARCHAR)   │
                            │ FK  userID (INT)          │
                            │     readingStatus (ENUM)  │
                            └───────────────────────────┘
```

---

## ⚙️ Installation & Setup

### Prerequisites
- **XAMPP** (Apache 2.4+ and MariaDB 10.4+ / MySQL 8.0+)
- **PHP 8.0+** with `pdo_mysql` and `gd` extensions enabled

### Step-by-Step Installation

1. **Deploy Repository:**
   Clone or copy this repository into your local web server root directory:
   ```bash
   # macOS XAMPP default path:
   cd /Applications/XAMPP/xamppfiles/htdocs/
   git clone https://github.com/snaimio/PHPAssignment1.git
   ```

2. **Start Services:**
   Open the XAMPP Control Panel and start both **Apache** and **MySQL**.

3. **Database Import:**
   - Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
   - Select the **Import** tab.
   - Choose `database.sql` from the root of the project and click **Import**.
   - *This creates the `book_library` database, sets up all 3 tables with foreign key constraints, and seeds initial data.*

4. **Verify Database Configuration:**
   Inspect `database.php` to ensure the DSN matches your local MySQL environment:
   ```php
   $dsn = 'mysql:host=localhost;dbname=book_library;charset=utf8mb4';
   $user = 'root';
   $password = ''; // Default is blank in XAMPP
   ```

5. **Launch Application:**
   Navigate to the entry point in your browser:
   ```
   http://localhost/PHPAssignment1/login_form.php
   ```

---

## 🧪 Testing Per-User Isolation

To verify that user data isolation is enforced:

1. **Create First Account:**
   - Register user `userA` and log in.
   - Add two new books to the catalog.
   - Verify both books appear in the catalog table.
2. **Create Second Account:**
   - Log out and register a new user `userB`.
   - Log in as `userB`.
   - **Verification:** Confirm the catalog displays `0` books (no books from `userA` are visible).
3. **Add Data as Second User:**
   - Add a single book as `userB`.
   - Confirm only `userB`'s new book is listed.
4. **Re-verify First Account:**
   - Log out and log back in as `userA`.
   - **Verification:** Confirm that `userA` sees only their original two books, and cannot view, edit, or delete `userB`'s book.

---

## 🔒 Security Architecture

| Security Domain | Implementation Standard |
| :--- | :--- |
| **SQL Injection Prevention** | 100% of queries use PDO prepared statements with explicit parameter binding (`bindValue`). Emulated prepares are disabled (`PDO::ATTR_EMULATE_PREPARES => false`). |
| **Cross-Site Scripting (XSS)** | All user-supplied data rendered in HTML templates is sanitized using the centralized `e()` helper function wrapping `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. |
| **Password Storage** | Passwords are cryptographically hashed using PHP's standard `password_hash($password, PASSWORD_DEFAULT)` and verified using `password_verify()`. |
| **Brute-Force Prevention** | Accounts are automatically locked for 300 seconds following 3 consecutive unsuccessful login attempts. |
| **Session Protection** | Session identifiers are refreshed via `session_regenerate_id(true)` upon successful authentication to eliminate session fixation vectors. |
| **Per-User Scoping** | All `SELECT`, `UPDATE`, and `DELETE` queries strictly bind `:userID` to `$_SESSION['userID']`. |
| **File Upload Validation** | Uploaded cover images are validated for real MIME types, allowed extensions, file size limits, and sanitized through GD re-encoding. |
| **Error Handling & Logging** | Database exceptions log details internally via `error_log()` while displaying sanitized, user-friendly error views to clients. |

---

## 📁 Project Structure

```
PHPAssignment1/
├── css/
│   └── books2.css                   # Main application stylesheet with responsive queries
├── images/                          # Image assets (originals, _100 thumbnails, _400 details)
│   ├── placeholder.jpg              # Default fallback image
│   ├── placeholder_100.jpg          # Standard 100px thumbnail
│   └── placeholder_400.jpg          # Standard 400px details image
├── js/
│   └── login_countdown.js           # Client-side countdown timer for account lockout
├── screenshots/                     # Documentation UI walkthrough screenshots
│   ├── all_books_catalog.png        # Screenshot: Catalog table view
│   ├── book_details_1.png           # Screenshot: Book details viewer (1)
│   ├── book_details_2.png           # Screenshot: Book details viewer (2)
│   ├── login_confirmation.png       # Screenshot: Successful login welcome view
│   ├── login_error.png              # Screenshot: Login validation error feedback
│   ├── login_form.png               # Screenshot: User login portal
│   ├── register_user_form.png       # Screenshot: User registration interface
│   └── update_book_form.png         # Screenshot: Edit book form with image preview
├── add_book.php                     # Handler: Insert book and process image upload
├── add_book_confirmation.php        # View: Add book success screen
├── add_book_form.php                # View: Add book form (auth protected)
├── add_error.php                    # View: Book creation error view
├── auth.php                         # Module: Centralized authentication gate & session guard
├── book_details.php                 # View: Full details and high-resolution cover
├── database.php                     # Module: PDO database connection & error handler
├── database.sql                     # Schema: Complete SQL dump with constraints and seed data
├── database_error.php               # View: Database connection failure page
├── delete_book.php                  # Handler: Delete book & remove associated images
├── footer.php                       # Template: Global HTML footer
├── functions.php                    # Module: Shared utility helpers (e() escape function)
├── header.php                       # Template: Global HTML header & navigation
├── image_util.php                   # Module: GD image resizing & thumbnail generation
├── index.php                        # View: Main catalog table (auth protected)
├── LICENSE                          # License: MIT License documentation
├── login.php                        # Handler: User authentication & lockout processing
├── login_confirmation.php           # View: Welcome confirmation screen upon login
├── login_form.php                   # View: User login portal with countdown timer
├── logout.php                       # Handler: Session destruction & redirect
├── README.md                        # Documentation: Project architectural guide
├── register_user.php                # Handler: User registration & password hashing
├── register_user_confirmation.php   # View: Registration confirmation screen
├── register_user_form.php           # View: User registration form
├── update_book.php                  # Handler: Update book record & refresh image assets
├── update_book_confirmation.php     # View: Update success screen
├── update_book_form.php             # View: Edit book form with live preview (auth protected)
├── update_error.php                 # View: Update error view
└── user_error.php                   # View: User registration error feedback
```

---

## 📄 License

This project is licensed under the **MIT License**. Refer to the [LICENSE](LICENSE) file for complete terms.

---

## 👤 Author

**Sheikh Naim**  
*Web Application Development — PHP & MySQL*