# Student Registration Management System — Project Explanation

**Submitted by:** Anubhav  
**Position:** WordPress Development Trainee  
**Company:** CreedAlly  
**Deadline:** Monday, 08/06/2026 by 6:00 PM

---

## What This Project Does

This is a complete **Student Registration Management System** built with Core PHP, MySQL, HTML, CSS, and JavaScript. It allows an administrator to register, view, update, delete, and search student records through a professional web-based admin dashboard.

---

## How It Works

### Architecture
The project follows an **MVC-like OOP architecture**:
- **Model** (`classes/Student.php`) — handles all database queries
- **Controller** (`controllers/StudentController.php`) — handles requests and business logic
- **Views** (`views/`) — render the HTML output

### Request Flow
1. Every request enters through `index.php`
2. The autoloader loads the required class files
3. `StudentController::handleRequest()` reads the `?action=` parameter
4. The appropriate method runs (list/add/edit/delete/view)
5. The controller passes data to the view for rendering

---

## Key Technical Decisions

### 1. PDO Prepared Statements
All database queries use PDO with bound parameters — no user input is ever interpolated directly into SQL strings. This prevents SQL injection completely.

### 2. Server-Side + Client-Side Validation
- **JavaScript** validates before submit (Full Name, Email, Phone, Country) with inline error messages matching the requirements
- **PHP** re-validates every field server-side because JavaScript can be bypassed
- Both layers use the same validation rules for consistency

### 3. Secure Image Upload
The `ImageUploader` class uses PHP's `finfo` extension to check the **actual MIME type** of uploaded files (not just the filename extension, which can be faked). Files are renamed to random unique strings before saving.

### 4. OOP Principles Applied
- **Encapsulation** — all class properties are `private`
- **Single Responsibility** — each class has exactly one job
- **Singleton Pattern** — `Database` class uses a singleton so only one DB connection is created per request
- **Separation of Concerns** — data, logic, and presentation are fully separated

### 5. XSS Prevention
Every variable output in HTML is wrapped in `htmlspecialchars()` with `ENT_QUOTES`. No raw user data is ever echoed directly.

---

## Database Design

Single table `students` with:
- Auto-increment primary key
- `UNIQUE` constraint on email
- Indexed columns: `full_name`, `email`, `country`, `gender`, `created_at`
- `updated_at` with `ON UPDATE CURRENT_TIMESTAMP` for automatic tracking
- `utf8mb4` charset to support all international characters

---

## Form Fields (as per requirements)

| Field | Input Type | Validation |
|---|---|---|
| Full Name | Text | Required |
| Email Address | Email | Required, valid format, unique |
| Phone Number | Number | Required, exactly 10 digits |
| Gender | Radio Button | Required |
| Date of Birth | Date | Required, must be past date |
| Country | Select Dropdown | Required |
| Skills | Checkboxes | Optional, stored comma-separated |
| Address | Textarea | Optional |
| Profile Image | File Upload | Optional, JPG/PNG/WEBP, max 2MB |

---

## How to Install

1. Copy folder to `C:\xampp\htdocs\STUDENT-MANAGEMENT-REGISTRATION-SYSTEM\`
2. Import `database/schema.sql` via phpMyAdmin
3. Open `http://localhost/STUDENT-MANAGEMENT-REGISTRATION-SYSTEM`

No additional libraries, frameworks, or Composer packages are required.

---

## Files Submitted

```
STUDENT-MANAGEMENT-REGISTRATION-SYSTEM/
├── config/config.php          ← DB credentials & app constants
├── classes/                   ← OOP class files (Database, Student, Validator, ImageUploader)
├── controllers/               ← Request routing & business logic
├── views/                     ← HTML templates (dashboard, form, profile)
├── assets/css/style.css       ← Full responsive stylesheet
├── assets/js/app.js           ← Client-side validation & UI interactions
├── database/schema.sql        ← MySQL DDL + sample data
├── uploads/                   ← Profile image storage
├── index.php                  ← Entry point
├── .htaccess                  ← Apache security configuration
├── screenshots/               ← Project screenshots
├── README.md                  ← Full documentation
└── EXPLANATION.md             ← This file
```
