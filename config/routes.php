<?php
/**
 * Route Definitions
 * 
 * Maps URL paths to [ControllerClass, method] pairs.
 * Used by the front controller (public/index.php).
 * 
 * @package SchoolManagementSystem
 */

$routes = [
    // ─── Default & Auth ─────────────────────────────────────────
    ''                          => ['AuthController', 'login'],
    'login'                     => ['AuthController', 'login'],
    'logout'                    => ['AuthController', 'logout'],
    'forgot-password'           => ['AuthController', 'forgotPassword'],
    'reset-password'            => ['AuthController', 'resetPassword'],
    'profile'                   => ['AuthController', 'profile'],
    'profile/update'            => ['AuthController', 'updateProfile'],
    'change-password'           => ['AuthController', 'changePassword'],
    'change-password/update'    => ['AuthController', 'updatePassword'],

    // ─── Dashboard ──────────────────────────────────────────────
    'dashboard'                 => ['DashboardController', 'index'],

    // ─── Student Management ─────────────────────────────────────
    'students'                  => ['StudentController', 'index'],
    'students/create'           => ['StudentController', 'create'],
    'students/store'            => ['StudentController', 'store'],
    'students/show'             => ['StudentController', 'show'],
    'students/edit'             => ['StudentController', 'edit'],
    'students/update'           => ['StudentController', 'update'],
    'students/delete'           => ['StudentController', 'delete'],

    // ─── Teacher Management ─────────────────────────────────────
    'teachers'                  => ['TeacherController', 'index'],
    'teachers/create'           => ['TeacherController', 'create'],
    'teachers/store'            => ['TeacherController', 'store'],
    'teachers/show'             => ['TeacherController', 'show'],
    'teachers/edit'             => ['TeacherController', 'edit'],
    'teachers/update'           => ['TeacherController', 'update'],
    'teachers/delete'           => ['TeacherController', 'delete'],

    // ─── Class Management ───────────────────────────────────────
    'classes'                   => ['ClassController', 'index'],
    'classes/create'            => ['ClassController', 'create'],
    'classes/store'             => ['ClassController', 'store'],
    'classes/edit'              => ['ClassController', 'edit'],
    'classes/update'            => ['ClassController', 'update'],
    'classes/delete'            => ['ClassController', 'delete'],

    // ─── Subject Management ─────────────────────────────────────
    'subjects'                  => ['SubjectController', 'index'],
    'subjects/create'           => ['SubjectController', 'create'],
    'subjects/store'            => ['SubjectController', 'store'],
    'subjects/edit'             => ['SubjectController', 'edit'],
    'subjects/update'           => ['SubjectController', 'update'],
    'subjects/delete'           => ['SubjectController', 'delete'],

    // ─── Attendance Module ──────────────────────────────────────
    'attendance'                => ['AttendanceController', 'index'],
    'attendance/record'         => ['AttendanceController', 'record'],
    'attendance/store'          => ['AttendanceController', 'store'],
    'attendance/report'         => ['AttendanceController', 'report'],

    // ─── Examination Module ─────────────────────────────────────
    'exams'                     => ['ExamController', 'index'],
    'exams/create'              => ['ExamController', 'create'],
    'exams/store'               => ['ExamController', 'store'],
    'exams/marks'               => ['ExamController', 'enterMarks'],
    'exams/marks/store'         => ['ExamController', 'storeMarks'],
    'exams/results'             => ['ExamController', 'results'],
    'exams/delete'              => ['ExamController', 'delete'],

    // ─── Fee Management ─────────────────────────────────────────
    'fees'                      => ['FeeController', 'index'],
    'fees/structure'            => ['FeeController', 'structure'],
    'fees/structure/store'      => ['FeeController', 'storeStructure'],
    'fees/record'               => ['FeeController', 'recordPayment'],
    'fees/store'                => ['FeeController', 'storePayment'],
    'fees/report'               => ['FeeController', 'report'],

    // ─── Timetable Module ───────────────────────────────────────
    'timetable'                 => ['TimetableController', 'index'],
    'timetable/manage'          => ['TimetableController', 'manage'],
    'timetable/store'           => ['TimetableController', 'store'],
    'timetable/delete'          => ['TimetableController', 'delete'],
    'timetable/view'            => ['TimetableController', 'view'],

    // ─── Academic Year & Terms ──────────────────────────────────
    'academic-years'            => ['AcademicYearController', 'index'],
    'academic-years/create'     => ['AcademicYearController', 'create'],
    'academic-years/store'      => ['AcademicYearController', 'store'],
    'academic-years/edit'       => ['AcademicYearController', 'edit'],
    'academic-years/update'     => ['AcademicYearController', 'update'],
    'academic-years/activate'   => ['AcademicYearController', 'activate'],
    'academic-years/terms'      => ['AcademicYearController', 'terms'],
    'academic-years/terms/store'    => ['AcademicYearController', 'storeTerm'],
    'academic-years/terms/activate' => ['AcademicYearController', 'activateTerm'],

    // ─── User Management (Super Admin) ──────────────────────────
    'users'                     => ['UserController', 'index'],
    'users/create'              => ['UserController', 'create'],
    'users/store'               => ['UserController', 'store'],
    'users/edit'                => ['UserController', 'edit'],
    'users/update'              => ['UserController', 'update'],
    'users/delete'              => ['UserController', 'delete'],

    // ─── Reports ────────────────────────────────────────────────
    'reports'                   => ['ReportController', 'index'],
    'reports/students'          => ['ReportController', 'students'],
    'reports/teachers'          => ['ReportController', 'teachers'],
    'reports/attendance'        => ['ReportController', 'attendance'],
    'reports/exams'             => ['ReportController', 'exams'],
    'reports/fees'              => ['ReportController', 'fees'],
    'reports/classes'           => ['ReportController', 'classes'],
];
