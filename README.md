# 🎓 Student Registration Management System

> A complete, production-ready Student Registration Management System built with Core PHP, MySQL, HTML, CSS, and JavaScript — submitted as a practical assignment for **CreedAlly WordPress Development Trainee** position.

---

## 📋 Assignment Requirements — Completion Status

| Requirement | Status |
|---|---|
| Registration form with all 9 specified fields | ✅ |
| Semantic HTML with labels for all fields | ✅ |
| Styled forms, tables, buttons with hover effects | ✅ |
| Responsive layout | ✅ |
| JS validation with exact required error messages | ✅ |
| PHP POST method form handling | ✅ |
| PHP server-side validation of all fields | ✅ |
| Secure image upload | ✅ |
| Store data in MySQL with proper field mapping | ✅ |
| Display students in table format | ✅ |
| CRUD operations (Create, Read, Update, Delete) | ✅ |
| Search filter above student list | ✅ |
| Search by Name, Email, Phone Number, Country | ✅ |
| OOP concepts throughout | ✅ |
| Proper error handling | ✅ |
| Clean code, no duplication | ✅ |
| SQL database file | ✅ |
| Screenshots | ✅ |
| ZIP file | ✅ |
| Short explanation document | ✅ |

---

## ✨ Features

- **Full CRUD** — Add, View, Edit, Delete students
- **Advanced Search** — Filter by Name, Email, Phone, Country (debounced live search)
- **Sortable Table** — Click any column header to sort ASC/DESC
- **Pagination** — Smart windowed pagination
- **Image Upload** — Secure profile image with MIME-type validation
- **Dashboard Stats** — Total · Male · Female · Added This Week
- **Toast Notifications** — Auto-dismissing success/error/info messages
- **Delete Confirmation Modal** — Glassmorphism overlay with keyboard support
- **Responsive Design** — Mobile-first; table converts to cards on small screens

---

## 📁 Project Structure

```
STUDENT-MANAGEMENT-REGISTRATION-SYSTEM/
│
├── config/
│   └── config.php              ← App constants, DB credentials, upload settings
│
├── classes/                    ← OOP Classes (PHP Standards)
│   ├── Database.php            ← PDO Singleton — one connection per request
│   ├── Student.php             ← Student model — all CRUD + search + stats
│   ├── Validator.php           ← Fluent server-side validator + sanitisers
│   └── ImageUploader.php       ← Secure file upload with MIME verification
│
├── controllers/
│   └── StudentController.php   ← Routes requests, calls model, loads view
│
├── views/                      ← HTML Templates (semantic HTML5)
│   ├── header.php              ← Shared <head>, <header>, delete modal
│   ├── footer.php              ← Closing tags, <footer>, JS include
│   ├── dashboard.php           ← Stats cards + sortable, paginated table
│   ├── form.php                ← Add/Edit form with all 9 PDF-specified fields
│   └── profile.php             ← Read-only student profile view
│
├── assets/
│   ├── css/style.css           ← Full responsive glassmorphism stylesheet
│   └── js/app.js               ← Client-side validation, toasts, modal, search
│
├── uploads/                    ← Profile image storage
│   ├── .htaccess               ← Blocks PHP execution in this folder
│   └── index.php               ← Fallback 403 redirect
│
├── database/
│   └── schema.sql              ← CREATE DATABASE + TABLE + sample data
│
├── screenshots/                ← Project screenshots (see screenshots/README.md)
│
├── index.php                   ← Entry point + class autoloader
├── .htaccess                   ← Apache security configuration
├── EXPLANATION.md              ← Short project explanation document
└── README.md                   ← This file
```

---

## 🧑‍💻 Form Fields (PDF Specification)

| # | Field | Input Type | Validation |
|---|---|---|---|
| 1 | Full Name | `type="text"` | Required — "Please enter your full name" |
| 2 | Email Address | `type="email"` | Required — "Please enter a valid email address" |
| 3 | Phone Number | `type="number"` | Required — "Phone number must contain 10 digits" |
| 4 | Gender | Radio Buttons | Required — Male / Female / Other |
| 5 | Date of Birth | `type="date"` | Required, must be past date |
| 6 | Country | `<select>` Dropdown | Required — "Country must be selected" |
| 7 | Skills | Checkboxes | Optional — 20 skills to choose from |
| 8 | Address | `<textarea>` | Optional |
| 9 | Profile Image | `type="file"` | Optional — JPG/PNG/WEBP, max 2MB |
| — | Submit | `type="submit"` | Triggers JS + PHP validation |

---

## 🚀 Installation (XAMPP)

### Step 1 — Place project
```
C:\xampp\htdocs\STUDENT-MANAGEMENT-REGISTRATION-SYSTEM\
```

### Step 2 — Create database
1. Start Apache + MySQL in XAMPP Control Panel
2. Go to `http://localhost/phpmyadmin`
3. Click **Import** → Choose `database/schema.sql` → **Go**

### Step 3 — Configure (if needed)
Edit `config/config.php`:
```php
define('BASE_URL', 'http://localhost/STUDENT-MANAGEMENT-REGISTRATION-SYSTEM');
define('DB_USER',  'root');
define('DB_PASS',  '');   // Your MySQL password if set
```

### Step 4 — Run
Open: `http://localhost/STUDENT-MANAGEMENT-REGISTRATION-SYSTEM`

---

## 🔒 Security

| Threat | Protection |
|---|---|
| SQL Injection | PDO prepared statements with bound parameters on every query |
| XSS | `htmlspecialchars(ENT_QUOTES)` on all output |
| File Upload Attacks | MIME verified by `finfo` (not filename), extension whitelist, 2MB cap, unique rename |
| Directory Traversal | `basename()` on all file paths |
| Clickjacking | `X-Frame-Options: SAMEORIGIN` |
| MIME Sniffing | `X-Content-Type-Options: nosniff` |
| Directory Listing | `Options -Indexes` in `.htaccess` |
| PHP in Uploads | Blocked via `uploads/.htaccess` |
| ORDER BY Injection | Column names whitelisted before SQL interpolation |

---

## 🏗️ OOP Concepts Applied

| Principle | Implementation |
|---|---|
| **Encapsulation** | All class properties `private`, accessed via public methods |
| **Single Responsibility** | Each class does exactly one thing |
| **Singleton Pattern** | `Database::getInstance()` — one PDO connection per request |
| **Separation of Concerns** | Model / Controller / View completely separated |
| **Reusability** | `Validator` and `ImageUploader` are context-agnostic |
| **Fluent Interface** | `$v->required()->email()->phone()` method chaining |
| **Type Safety** | `declare(strict_types=1)`, typed parameters and returns |

---

## 🛠️ Technologies

- **PHP 8.0+** — Core PHP, OOP, strict types, match expressions
- **MySQL 8 / MariaDB** — InnoDB, UTF-8mb4, indexed columns
- **PDO** — Prepared statements throughout
- **HTML5** — Semantic markup, ARIA attributes, `<fieldset>/<legend>`
- **CSS3** — Custom properties, Grid, Flexbox, glassmorphism, media queries
- **Vanilla JavaScript (ES6+)** — No jQuery; module pattern
- **Google Fonts** — Plus Jakarta Sans

---

*Submitted by Anubhav | CreedAlly WordPress Development Trainee Practical*
