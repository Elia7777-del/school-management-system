# SCHOOL MANAGEMENT SYSTEM — IMPLEMENTATION DOCUMENT

**Version:** 1.0.0  
**Date:** September 2026  
**Platform:** Multi-Tenant SaaS  
**Hosting:** Amazon Web Services (AWS)

---

## TABLE OF CONTENTS

1. [Introduction](#1-introduction)
2. [System Overview](#2-system-overview)
3. [Technology Stack](#3-technology-stack)
4. [System Architecture](#4-system-architecture)
5. [Database Design](#5-database-design)
6. [Multi-Tenancy (SaaS)](#6-multi-tenancy-saas)
7. [User Roles & Permissions](#7-user-roles--permissions)
8. [Feature Modules](#8-feature-modules)
9. [Security Implementation](#9-security-implementation)
10. [AWS Deployment](#10-aws-deployment)
11. [API & Payment Integration](#11-api--payment-integration)
12. [Directory Structure](#12-directory-structure)
13. [Request Lifecycle](#13-request-lifecycle)
14. [Default Accounts](#14-default-accounts)
15. [Grading System](#15-grading-system)

---

## 1. INTRODUCTION

### 1.1 Purpose
This document provides a comprehensive technical description of the School Management System (SMS), a web-based Software-as-a-Service (SaaS) platform designed for the Tanzanian education system. The system allows multiple schools to manage their academic and administrative operations through a single shared platform.

### 1.2 Scope
The system covers:
- Student enrollment and management
- Teacher management and class assignment
- Examination management with automated Tanzanian grading
- Attendance tracking
- Fee management with mobile money integration (AzamPay)
- Parent portal for guardians
- Timetable management
- Multi-school management with subscription billing
- Reports and audit logs

### 1.3 Target Users
| User Type | Description |
|---|---|
| System Administrator | Platform owner managing all schools and subscriptions |
| School Administrator | Admin of a specific school managing all school operations |
| Teacher | Manages attendance, marks, and assigned classes |
| Student | Views own results, fees, and timetable |
| Parent/Guardian | Views linked children's academic progress and finances |

---

## 2. SYSTEM OVERVIEW

The School Management System is a **Multi-Tenant SaaS** application where:
- **One codebase** serves all schools
- **One database** stores all data with school-level isolation
- **Each school** has its own admin, students, teachers, and data
- **The platform owner** (System Admin) manages school registrations and subscriptions
- **Subscription enforcement** blocks access for schools with expired plans

### 2.1 Key Characteristics
| Characteristic | Description |
|---|---|
| Multi-Tenant | Multiple schools share one application instance |
| Data Isolation | Each school can only see its own data via `school_id` scoping |
| Stateless | Sessions stored in database for load balancer compatibility |
| Role-Based Access | 6 user roles with granular permissions |
| Education System | Tanzanian grading (Primary Std I-VII, Secondary Form I-IV) |

---

## 3. TECHNOLOGY STACK

### 3.1 Backend
| Component | Technology |
|---|---|
| Language | PHP 8.x |
| Architecture | Custom MVC (Model-View-Controller) |
| Framework | None (Pure Object-Oriented PHP) |
| Database Driver | PDO (PHP Data Objects) with prepared statements |
| Password Hashing | bcrypt via `password_hash()` |
| Session Management | Custom `DatabaseSessionHandler` (implements `SessionHandlerInterface`) |

### 3.2 Frontend
| Component | Technology |
|---|---|
| CSS Framework | Bootstrap 5 (Dark Theme) |
| JavaScript | Vanilla JS + jQuery |
| Icons | Bootstrap Icons |
| Responsive Design | Yes (mobile, tablet, desktop) |

### 3.3 Database
| Component | Technology |
|---|---|
| Engine | MySQL 10.4 (MariaDB) / AWS RDS |
| Storage Engine | InnoDB |
| Character Set | utf8mb4 (full Unicode support) |
| Collation | utf8mb4_unicode_ci |

### 3.4 Infrastructure (AWS)
| Service | Purpose |
|---|---|
| EC2 | Application servers (Apache + PHP-FPM) |
| RDS | Managed MySQL database |
| ALB | Application Load Balancer |
| Route 53 | DNS management (optional) |
| CloudFront | CDN for static assets (optional) |

---

## 4. SYSTEM ARCHITECTURE

### 4.1 Architecture Pattern: MVC

```
┌───────────────────────────────────────────────────────────┐
│                    BROWSER (Client)                       │
└─────────────────────────┬─────────────────────────────────┘
                          │ HTTP Request
                          ▼
┌───────────────────────────────────────────────────────────┐
│              public/index.php (Front Controller)          │
│  ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐ │
│  │ config/     │  │ helpers/     │  │ config/routes.php │ │
│  │ app.php     │  │ session.php  │  │ URL → Controller  │ │
│  │ database.php│  │ auth.php     │  │    mapping        │ │
│  └─────────────┘  │ functions.php│  └──────────────────┘ │
│                    └──────────────┘                        │
└─────────────────────────┬─────────────────────────────────┘
                          │ Route Match
                          ▼
┌───────────────────────────────────────────────────────────┐
│                    CONTROLLERS                            │
│  AuthController, DashboardController, SchoolController,   │
│  StudentController, TeacherController, ExamController,    │
│  AttendanceController, FeeController, InvoiceController,  │
│  PaymentController, TimetableController, etc.             │
└────────┬─────────────────────────────┬────────────────────┘
         │ Data Access                 │ Render
         ▼                             ▼
┌─────────────────┐          ┌─────────────────────┐
│     MODELS      │          │       VIEWS          │
│  Student.php    │          │  views/students/     │
│  Teacher.php    │          │  views/dashboard/    │
│  School.php     │          │  views/layouts/      │
│  Exam.php       │          │    header.php        │
│  Invoice.php    │          │    footer.php        │
│  etc.           │          │  etc.                │
└────────┬────────┘          └─────────────────────┘
         │ PDO
         ▼
┌───────────────────────────────────────────────────────────┐
│                 MySQL DATABASE (AWS RDS)                   │
│              Database Name: school_ms                      │
│              Tables: 28 total                              │
└───────────────────────────────────────────────────────────┘
```

### 4.2 AWS Deployment Architecture

```
                    Internet
                       │
                       ▼
              ┌────────────────┐
              │   Route 53     │  (DNS)
              └────────┬───────┘
                       ▼
              ┌────────────────┐
              │  CloudFront    │  (CDN - optional)
              └────────┬───────┘
                       ▼
              ┌────────────────┐
              │     ALB        │  (Application Load Balancer)
              └───┬────┬───┬──┘
                  │    │   │
          ┌───────┘    │   └───────┐
          ▼            ▼           ▼
    ┌──────────┐ ┌──────────┐ ┌──────────┐
    │  EC2 #1  │ │  EC2 #2  │ │  EC2 #3  │  (PHP-FPM + Apache)
    │  App     │ │  App     │ │  App     │
    └────┬─────┘ └────┬─────┘ └────┬─────┘
         │            │            │
         └────────────┼────────────┘
                      │
               ┌──────▼──────┐
               │   AWS RDS   │
               │   MySQL     │  (Multi-AZ, shared DB)
               │  school_ms  │
               └─────────────┘
```

**Why this works:**
- Sessions are stored in the RDS database, not on local disk
- Any EC2 instance can serve any user request
- ALB distributes traffic evenly across instances
- If one EC2 instance fails, traffic goes to the remaining instances

---

## 5. DATABASE DESIGN

### 5.1 Entity-Relationship Summary

The system contains **28 database tables** organized into 5 categories:

#### Core Tables (5)
| Table | Purpose | Key Columns |
|---|---|---|
| `roles` | User role definitions | role_name, description |
| `users` | All user accounts | username, email, password, role_id, school_id |
| `schools` | School profiles | name, slug, email, phone, status |
| `school_subscriptions` | Subscription billing | school_id, plan, start_date, end_date, amount_paid |
| `sessions` | DB-backed PHP sessions | id, data, last_accessed |

#### Academic Tables (6)
| Table | Purpose | Key Columns |
|---|---|---|
| `academic_years` | School calendar years | year_name, start_date, end_date, is_active |
| `school_terms` | Terms within years | academic_year_id, term_name, is_active |
| `classes` | Class/Form definitions | class_name, education_level, section, capacity |
| `subjects` | Subject catalog | subject_name, subject_code, education_level |
| `class_subjects` | Subjects assigned to classes | class_id, subject_id |
| `timetables` | Weekly schedule | class_id, subject_id, teacher_id, day_of_week, time |

#### People Tables (6)
| Table | Purpose | Key Columns |
|---|---|---|
| `students` | Student records | admission_number, name, class_id, education_level |
| `teachers` | Teacher records | teacher_number, name, qualification, specialization |
| `parents` | Parent/Guardian records | name, phone, relationship |
| `student_parents` | Links parents to students | student_id, parent_id |
| `teacher_subjects` | Teacher subject assignments | teacher_id, subject_id |
| `teacher_classes` | Teacher class assignments | teacher_id, class_id, is_class_teacher |

#### Assessment & Tracking Tables (4)
| Table | Purpose | Key Columns |
|---|---|---|
| `exams` | Exam definitions | exam_name, exam_type, class_id, status |
| `exam_results` | Student marks per subject | exam_id, student_id, subject_id, marks, grade, points |
| `attendance` | Daily attendance | student_id, date, status (present/absent/late) |
| `parent_comments` | Parent feedback | student_id, parent_id, comment |

#### Finance Tables (4)
| Table | Purpose | Key Columns |
|---|---|---|
| `fee_structures` | Fee amounts per class | class_id, academic_year_id, amount |
| `fee_payments` | Payment receipts | student_id, amount_paid, payment_method, receipt_number |
| `invoices` | Student invoices | student_id, invoice_number, amount, amount_paid, status |
| `payment_transactions` | Mobile money payments | invoice_id, reference, provider, status |

#### System Tables (3)
| Table | Purpose | Key Columns |
|---|---|---|
| `announcements` | School announcements | title, body, audience |
| `teacher_activity_logs` | Audit trail | user_id, action_type, description, ip_address |
| `parent_comments` | Parent feedback | student_id, parent_id, comment |

### 5.2 Multi-Tenant Column
The following 14 tables have a `school_id` column for data isolation:
`users`, `students`, `teachers`, `parents`, `classes`, `subjects`, `exams`, `academic_years`, `school_terms`, `invoices`, `fee_payments`, `fee_structures`, `announcements`, `timetables`

---

## 6. MULTI-TENANCY (SaaS)

### 6.1 Strategy: Shared Database with Row-Level Isolation

All schools share one database (`school_ms`). Each school's data is separated by a `school_id` column present on every major table.

### 6.2 How It Works

**Step 1 — Login:**
```
User enters credentials → AuthController::login()
  ├── Find user in `users` table
  ├── Load `schools` record → check status = 'active'
  ├── Load `school_subscriptions` → check end_date >= today
  ├── If expired → "Your school's subscription has expired"
  ├── If suspended → "Your school account is suspended"
  └── Store school_id in $_SESSION['school_id']
```

**Step 2 — Every Data Query:**
```php
// The helper function currentSchoolId() reads from session
$schoolId = currentSchoolId(); // Returns int or null

// Every model automatically filters by school
$sql = "SELECT * FROM students WHERE deleted_at IS NULL";
if ($schoolId) {
    $sql .= " AND school_id = ?";
    $params[] = $schoolId;
}
```

**Step 3 — Every Data Creation:**
```php
// When creating a new record, school_id is injected automatically
$schoolId = currentSchoolId() ?? 1;
$stmt->execute([..., $schoolId]);
```

### 6.3 Subscription Plans
| Plan | Description |
|---|---|
| Trial | Free trial period |
| Basic | Standard features |
| Premium | All features |
| Enterprise | Unlimited, priority support |

### 6.4 School States
| Status | Effect |
|---|---|
| `active` | Normal access |
| `suspended` | All users blocked from login |
| `expired` | Subscription expired, login blocked |

---

## 7. USER ROLES & PERMISSIONS

### 7.1 Role Hierarchy

| Role | Scope | Capabilities |
|---|---|---|
| `system_admin` | Platform-wide (no school) | Manage all schools, subscriptions, billing |
| `super_admin` | Within one school | Full control of school operations |
| `school_admin` | Within one school | Administrative operations (may have scope: primary/secondary/all) |
| `teacher` | Within one school | Manage assigned classes, enter marks, record attendance |
| `student` | Within one school | View own results, fees, timetable |
| `parent` | Within one school | View linked children's data, submit feedback |

### 7.2 Access Control Implementation

```php
// Guard functions in helpers/auth.php

requireLogin();                    // Must be logged in
requireRole('super_admin');        // Must have specific role
requireRole(['super_admin', 'school_admin']); // Must have one of these roles
requireScopeAccess('primary');     // Must have primary-level access
isSysAdmin();                      // Check if system admin
isAdmin();                         // Check if any admin role
currentSchoolId();                 // Get current school context
```

---

## 8. FEATURE MODULES

### 8.1 Authentication Module
**Controller:** `AuthController.php`  
**Features:**
- Login with username/password
- Login with student admission number + password
- Login with parent's child admission number + parent password
- CSRF protection on all forms
- Password change functionality
- Session management with database storage
- Subscription validation on login

### 8.2 School Management Module (System Admin)
**Controller:** `SchoolController.php`  
**Model:** `School.php`  
**Features:**
- Register new schools with admin credentials
- View all schools with statistics (students, users, subscription status)
- Edit school details (name, email, phone, address)
- Suspend / Reactivate schools
- Add subscriptions (plan, dates, payment tracking)
- View subscription history per school

### 8.3 Student Management Module
**Controller:** `StudentController.php`  
**Model:** `Student.php`  
**Features:**
- Full CRUD (Create, Read, Update, Delete with soft-delete)
- Auto-generate admission numbers
- Filter by class, education level, status
- Link parent accounts to students
- Auto-create user login accounts for students
- Pagination and search

### 8.4 Teacher Management Module
**Controller:** `TeacherController.php`  
**Model:** `Teacher.php`  
**Features:**
- Full CRUD with teacher numbers
- Assign subjects to teachers
- Assign teachers to classes (with class teacher designation)
- Auto-create user login accounts
- Filter and search

### 8.5 Examination Module
**Controller:** `ExamController.php`  
**Models:** `Exam.php`, `ExamResult.php`  
**Features:**
- Create exams (midterm, terminal, annual)
- Enter marks per subject per student
- Bulk mark entry per class
- Individual student mark entry
- Auto-calculate grades using Tanzanian grading system
- View results with rankings

### 8.6 Attendance Module
**Controller:** `AttendanceController.php`  
**Model:** `Attendance.php`  
**Features:**
- Daily attendance recording per class
- Status: Present, Absent, Late
- Date-range reporting
- Per-student attendance history

### 8.7 Fee Management Module
**Controller:** `FeeController.php`  
**Model:** `Fee.php`  
**Features:**
- Fee structure definition per class per year/term
- Payment recording (cash, bank, mobile money)
- Receipt generation
- Fee collection reports
- Outstanding balance tracking

### 8.8 Invoice & Payment Module
**Controllers:** `InvoiceController.php`, `PaymentController.php`  
**Models:** `Invoice.php`, `PaymentTransaction.php`  
**Features:**
- Invoice generation per student
- Payment status tracking (pending, partial, paid)
- AzamPay USSD Push mobile payment integration
- Webhook for automatic payment confirmation
- Student/Parent can view own invoices

### 8.9 Timetable Module
**Controller:** `TimetableController.php`  
**Model:** `Timetable.php`  
**Features:**
- Weekly timetable management per class
- Teacher-subject-time slot assignment
- View timetable for any class

### 8.10 Parent Portal
**Controller:** `ParentController.php`  
**Model:** `ParentComment.php`  
**Features:**
- View linked children's information
- View children's exam results
- View children's attendance records
- Submit feedback/comments about children
- View invoices and payment status

### 8.11 Reports Module
**Controller:** `ReportController.php`  
**Features:**
- Student enrollment reports
- Teacher statistics
- Attendance summaries
- Exam results analysis
- Fee collection reports
- Class-level reports

### 8.12 Audit Module
**Controller:** `AuditController.php`  
**Model:** `ActivityLog.php`  
**Features:**
- Log all user actions (login, CRUD operations)
- IP address and URL tracking
- Searchable activity history

---

## 9. SECURITY IMPLEMENTATION

| Threat | Countermeasure | Implementation |
|---|---|---|
| SQL Injection | Parameterized Queries | All queries use PDO prepared statements with `?` placeholders |
| Cross-Site Scripting (XSS) | Output Encoding | `htmlspecialchars()` on all user-generated output |
| Cross-Site Request Forgery (CSRF) | Token Validation | Hidden token field on every form, validated server-side |
| Brute Force Login | Account Lockout | Account status check on each login attempt |
| Session Hijacking | Session Regeneration | `session_regenerate_id(true)` on successful login |
| Session Fixation | New Session ID | Session ID regenerated after authentication |
| Password Exposure | Hashing | `password_hash()` with bcrypt algorithm |
| Unauthorized Access | Role Guards | `requireLogin()`, `requireRole()` on every controller |
| Data Leakage Between Schools | Tenant Isolation | `school_id` filter on every database query |
| Subscription Bypass | Login Enforcement | Subscription validity checked at login time |

---

## 10. AWS DEPLOYMENT

### 10.1 Services Used
| AWS Service | Role |
|---|---|
| **EC2** | Hosts the PHP application (Apache + PHP-FPM) |
| **RDS (MySQL)** | Managed database — stores all data + sessions |
| **ALB** | Distributes traffic across multiple EC2 instances |

### 10.2 Stateless Application Design
The application is designed to be **fully stateless** at the server level:
- **Sessions** are stored in the `sessions` table in RDS (not on local disk)
- **No local file uploads** — no file system dependency
- **No in-memory state** — all state lives in the database

This means:
- ALB can route any request to any EC2 instance
- EC2 instances can be added or removed dynamically (Auto Scaling)
- If one EC2 instance fails, users are not affected

### 10.3 Session Handler
A custom `DatabaseSessionHandler` class implements PHP's `SessionHandlerInterface`:

| Method | Database Operation |
|---|---|
| `read($id)` | `SELECT data FROM sessions WHERE id = ?` |
| `write($id, $data)` | `INSERT ... ON DUPLICATE KEY UPDATE` |
| `destroy($id)` | `DELETE FROM sessions WHERE id = ?` |
| `gc($maxLifetime)` | `DELETE FROM sessions WHERE last_accessed < NOW() - INTERVAL ? SECOND` |

---

## 11. API & PAYMENT INTEGRATION

### 11.1 AzamPay Mobile Payment (USSD Push)

**Flow:**
```
1. Student/Parent clicks "Pay Now"
2. Selects provider (M-Pesa, TigoPesa, Airtel Money)
3. Enters phone number
4. System creates a `payment_transactions` record (status: initiated)
5. System sends USSD Push request to AzamPay API
6. Student receives PIN prompt on their phone
7. Student enters PIN to authorize payment
8. AzamPay sends webhook callback to /api/azampay/webhook
9. System updates transaction status to "success"
10. Invoice amount_paid is updated automatically
11. Fee payment receipt is created
```

**Endpoints:**
| Endpoint | Method | Purpose |
|---|---|---|
| `/api/azampay/checkout` | POST | Initiate a payment |
| `/api/azampay/webhook` | POST | Receive payment confirmation from AzamPay |

**Status:** Mock implementation ready. Requires AzamPay Merchant API keys for production.

---

## 12. DIRECTORY STRUCTURE

```
school-management-system/
│
├── config/
│   ├── app.php                    # App constants, Tanzanian grading functions
│   ├── database.php               # PDO Singleton database connection
│   └── routes.php                 # URL → [Controller, method] mapping
│
├── controllers/                   # 18 Controllers
│   ├── AuthController.php         # Login, logout, password, profile
│   ├── DashboardController.php    # Role-specific dashboards
│   ├── SchoolController.php       # [SaaS] School + subscription management
│   ├── StudentController.php      # Student CRUD
│   ├── TeacherController.php      # Teacher CRUD
│   ├── ClassController.php        # Class management
│   ├── SubjectController.php      # Subject management
│   ├── ExamController.php         # Exams + mark entry + results
│   ├── AttendanceController.php   # Daily attendance
│   ├── FeeController.php          # Fee structures + payments
│   ├── InvoiceController.php      # Invoice generation
│   ├── PaymentController.php      # AzamPay integration
│   ├── TimetableController.php    # Weekly timetable
│   ├── AcademicYearController.php # Academic years + terms
│   ├── ParentController.php       # Parent portal
│   ├── UserController.php         # User account management
│   ├── ReportController.php       # Reports
│   └── AuditController.php        # Activity logs
│
├── models/                        # 16 Models
│   ├── School.php                 # Schools + subscriptions
│   ├── User.php
│   ├── Student.php
│   ├── Teacher.php
│   ├── SchoolClass.php
│   ├── Subject.php
│   ├── Exam.php
│   ├── ExamResult.php
│   ├── Attendance.php
│   ├── Fee.php
│   ├── Invoice.php
│   ├── PaymentTransaction.php
│   ├── Timetable.php
│   ├── AcademicYear.php
│   ├── ParentComment.php
│   └── ActivityLog.php
│
├── views/                         # 17 View Directories
│   ├── layouts/                   # header.php (sidebar + navbar), footer.php
│   ├── auth/                      # login, forgot_password, profile, change_password
│   ├── dashboard/                 # admin, teacher, student, parent dashboards
│   ├── schools/                   # [SaaS] index, create, edit
│   ├── students/                  # index, create, edit, show
│   ├── teachers/                  # index, create, edit, show
│   ├── classes/                   # index, create, edit
│   ├── subjects/                  # index, create, edit
│   ├── exams/                     # index, create, marks, results
│   ├── attendance/                # index, record, report
│   ├── fees/                      # index, structure, record, report
│   ├── invoices/                  # index, create
│   ├── timetable/                 # index, manage, view
│   ├── academic/                  # index, create, edit, terms
│   ├── reports/                   # index, students, teachers, etc.
│   ├── users/                     # index, create, edit
│   └── audit/                     # index
│
├── helpers/
│   ├── session.php                # Session start with DB handler registration
│   ├── DatabaseSessionHandler.php # Custom SessionHandlerInterface for ALB
│   ├── auth.php                   # Authentication guards + school helpers
│   └── functions.php              # Utilities (sanitize, redirect, flash, etc.)
│
├── database/
│   └── schema.sql                 # Complete database schema
│
├── migrations/
│   ├── multi_tenant_saas.php      # SaaS migration (schools, subscriptions, school_id)
│   ├── create_sessions_table.php  # DB-backed sessions migration
│   └── create_parent_comments.php # Parent comments table
│
└── public/
    └── index.php                  # Front controller (single entry point)
```

---

## 13. REQUEST LIFECYCLE

```
1. User opens: http://domain.com/school-management-system/public/students

2. .htaccess rewrites to: public/index.php?url=students

3. public/index.php (Front Controller):
   a. Load config/app.php          → defines APP_NAME, BASE_URL, grading functions
   b. Load config/database.php     → PDO Singleton
   c. Load helpers/session.php     → starts DB-backed session
   d. Load helpers/auth.php        → authentication helpers
   e. Load helpers/functions.php   → utility functions
   f. Call startSession()          → DatabaseSessionHandler → RDS
   g. Load config/routes.php       → load route definitions
   h. Parse URL: "students"
   i. Match: $routes['students'] = ['StudentController', 'index']

4. Load controllers/StudentController.php
   a. Constructor: requireRole(['super_admin', 'school_admin'])
   b. Check: user logged in? correct role? school active?
   c. Call index() method

5. StudentController::index():
   a. $model = new Student()
   b. $students = $model->getAll()
      → SQL includes: AND school_id = ? (from session)
   c. require views/layouts/header.php    → sidebar + nav
   d. require views/students/index.php   → student table
   e. require views/layouts/footer.php   → scripts

6. Response: HTML page rendered → sent to browser
```

---

## 14. DEFAULT ACCOUNTS

| Role | Username | Password | School |
|---|---|---|---|
| System Admin | `sysadmin` | `SysAdmin@2024` | None (platform-level) |
| School Admin | *(created when registering a school)* | *(set during registration)* | Specific school |

---

## 15. GRADING SYSTEM

### 15.1 Primary Education (Standard I–VII)
| Marks | Grade | Remarks |
|---|---|---|
| 81 – 100 | A | Excellent |
| 61 – 80 | B | Very Good |
| 41 – 60 | C | Good |
| 21 – 40 | D | Satisfactory |
| 0 – 20 | F | Fail |

### 15.2 Secondary Education (Form I–IV)
| Marks | Grade | Points | Remarks |
|---|---|---|---|
| 75 – 100 | A | 1 | Excellent |
| 65 – 74 | B+ | 2 | Very Good |
| 55 – 64 | B | 3 | Good |
| 45 – 54 | C | 4 | Satisfactory |
| 30 – 44 | D | 5 | Pass |
| 0 – 29 | F | 7 | Fail |

### 15.3 Division Calculation (Secondary)
Best 7 subjects' points are summed:
| Total Points | Division |
|---|---|
| 7 – 17 | Division I |
| 18 – 21 | Division II |
| 22 – 25 | Division III |
| 26 – 33 | Division IV |
| 34+ | Division 0 (Fail) |

---

**END OF DOCUMENT**
