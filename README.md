# School Management System (MVP) — Tanzania

A simple, complete, and robust school management system built using PHP (OOP, MVC Architecture), MySQL, and Bootstrap 5. It is designed specifically for primary and secondary schools in Tanzania (Standard I–VII & Form I–IV).

## Technology Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend**: PHP (OOP, MVC Architecture, clean Front Controller routing)
- **Database**: MySQL (3NF Normalized Structure, PDO prepared statements)
- **Authentication**: PHP Sessions with BCRYPT password hashing

---

## Features By User Role

### 1. Super Admin
- Dashboard with full statistics (Total Students, Teachers, Classes, Subjects, Revenue)
- Manage system users (Admin, Teachers, Students, Parents)
- System setup: Classes, Subjects, Academic Years, Terms

### 2. School Admin
- Register students, teachers, parents and link parent-child profiles
- Assign teachers to classes and subjects
- Fee structures and payment recording
- Access reports (Roster, Directory, Fee collection statement)

### 3. Teacher
- Record daily class attendance (Present, Absent, Late)
- Enter exam marks (Midterm, Terminal, Annual) with automatic Tanzanian grading
- View assigned classes and student details

### 4. Student & Parent
- Student: view profile, attendance rate, and exam results
- Parent: track multiple children, attendance summaries, fee balance, and academic grades

---

## Installation & Configuration

### Prerequisites
- **Apache Web Server** & **MySQL** (via XAMPP, WampServer, or native Ubuntu LAMP stack)
- **PHP 8.0+**

### Step 1: Database Setup
1. Open phpMyAdmin or your MySQL client.
2. Import the database schema from: `database/schema.sql` (Creates `school_ms` database).
3. Import the default sample seed data from: `database/seed.sql` to populate roles, classes, subjects, users, and transactions.

### Step 2: Virtual Host / URL Rewrite
The application uses URL rewriting via `.htaccess` in the root and `/public` directories.
Make sure Apache has `mod_rewrite` enabled.

If running on XAMPP under `htdocs/school-management-system`:
- Update `BASE_URL` in `config/app.php` to point to:
  `http://localhost/school-management-system/public`

### Step 3: Default Credentials
All passwords are set to `password` in the seed data.

| Username | Role | Default Password |
|---|---|---|
| `superadmin` | Super Admin | `password` |
| `schooladmin` | School Admin | `password` |
| `teacher1` | Teacher | `password` |
| `student1` | Student | `password` |
| `parent1` | Parent | `password` |
commit
