<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' . APP_NAME : APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="<?php echo isLoggedIn() ? 'has-sidebar' : 'auth-page'; ?>">

<?php if (isLoggedIn()): ?>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header d-flex align-items-center justify-content-between">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="sidebar-brand d-flex align-items-center text-decoration-none">
                <i class="bi bi-mortarboard-fill fs-3 text-cyan me-2"></i>
                <span class="brand-text fw-bold text-white"><?php echo APP_NAME; ?></span>
            </a>
            <button class="btn btn-link text-white d-md-none p-0" id="sidebar-close">
                <i class="bi bi-x-lg fs-4"></i>
            </button>
        </div>

        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-link <?php echo isActiveMenu('dashboard'); ?>">
                        <i class="bi bi-grid-1x2-fill me-3"></i> Dashboard
                    </a>
                </li>

                <?php if (hasRole('super_admin')): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/users" class="nav-link <?php echo isActiveMenu('users'); ?>">
                            <i class="bi bi-people-fill me-3"></i> System Users
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/students" class="nav-link <?php echo isActiveMenu('students'); ?>">
                            <i class="bi bi-person-fill-gear me-3"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/teachers" class="nav-link <?php echo isActiveMenu('teachers'); ?>">
                            <i class="bi bi-person-badge-fill me-3"></i> Teachers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/classes" class="nav-link <?php echo isActiveMenu('classes'); ?>">
                            <i class="bi bi-building-fill me-3"></i> Classes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/subjects" class="nav-link <?php echo isActiveMenu('subjects'); ?>">
                            <i class="bi bi-book-half me-3"></i> Subjects
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isAdmin() || hasRole('teacher')): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/attendance" class="nav-link <?php echo isActiveMenu('attendance'); ?>">
                            <i class="bi bi-calendar-check-fill me-3"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/exams" class="nav-link <?php echo isActiveMenu('exams'); ?>">
                            <i class="bi bi-journal-check me-3"></i> Examinations
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasRole('student')): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/exams/my-results" class="nav-link <?php echo isActiveMenu('exams/my-results'); ?>">
                            <i class="bi bi-award-fill me-3 text-warning"></i> My Results
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/my-fees" class="nav-link <?php echo isActiveMenu('my-fees'); ?>">
                            <i class="bi bi-wallet2 me-3 text-success"></i> My Fees
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (hasRole('parent')): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/dashboard" class="nav-link <?php echo isActiveMenu('dashboard'); ?>">
                            <i class="bi bi-person-hearts me-3 text-cyan"></i> My Children
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/my-fees" class="nav-link <?php echo isActiveMenu('my-fees'); ?>">
                            <i class="bi bi-wallet2 me-3 text-success"></i> Payments & Invoices
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/fees" class="nav-link <?php echo isActiveMenu('fees'); ?>">
                            <i class="bi bi-cash-coin me-3"></i> Fee Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/invoices" class="nav-link <?php echo isActiveMenu('invoices'); ?>">
                            <i class="bi bi-receipt me-3"></i> Invoices
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/timetable" class="nav-link <?php echo isActiveMenu('timetable'); ?>">
                        <i class="bi bi-calendar-week-fill me-3"></i> Timetable
                    </a>
                </li>

                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/academic-years" class="nav-link <?php echo isActiveMenu('academic-years'); ?>">
                            <i class="bi bi-calendar3-range-fill me-3"></i> Academic Calendar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/reports" class="nav-link <?php echo isActiveMenu('reports'); ?>">
                            <i class="bi bi-bar-chart-line-fill me-3"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/audit" class="nav-link <?php echo isActiveMenu('audit'); ?>">
                            <i class="bi bi-shield-check me-3"></i> Activity Logs
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (isSysAdmin()): ?>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/schools" class="nav-link <?php echo isActiveMenu('schools'); ?>">
                            <i class="bi bi-building me-3 text-cyan"></i> Schools
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?php echo BASE_URL; ?>/schools/create" class="nav-link <?php echo isActiveMenu('schools/create'); ?>">
                            <i class="bi bi-building-add me-3 text-success"></i> Register School
                        </a>
                    </li>
                <?php endif; ?>

                <li class="sidebar-divider my-3"></li>


                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/profile" class="nav-link <?php echo isActiveMenu('profile'); ?>">
                        <i class="bi bi-person-bounding-box me-3"></i> My Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo BASE_URL; ?>/change-password" class="nav-link <?php echo isActiveMenu('change-password'); ?>">
                        <i class="bi bi-shield-lock-fill me-3"></i> Change Password
                    </a>
                </li>
                <li class="nav-item mt-auto">
                    <a href="<?php echo BASE_URL; ?>/logout" class="nav-link text-danger-hover">
                        <i class="bi bi-box-arrow-left me-3 text-danger"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper" id="main-wrapper">
        <!-- Top Navbar -->
        <header class="top-navbar d-flex align-items-center justify-content-between px-3 px-md-4">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-link text-white d-md-none p-0" id="sidebar-toggle" aria-label="Open menu">
                    <i class="bi bi-list fs-2"></i>
                </button>
                <h5 class="page-title text-white mb-0 fs-6 fs-md-5"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h5>
            </div>

            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-link text-white text-decoration-none dropdown-toggle d-flex align-items-center p-0 gap-1" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 d-md-none"></i>
                        <span class="d-none d-md-inline text-muted small">Hello,</span>
                        <span class="text-white fw-medium d-none d-sm-inline small"><?php echo currentUser()['username']; ?></span>
                        <span class="badge bg-cyan text-dark d-none d-sm-inline"><?php echo ucfirst(str_replace('_', ' ', currentUser()['role'])); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 bg-dark-card mt-2">
                        <li class="d-sm-none px-3 py-2 border-bottom border-secondary">
                            <div class="text-white fw-medium"><?php echo currentUser()['username']; ?></div>
                            <div class="text-muted small"><?php echo ucfirst(str_replace('_', ' ', currentUser()['role'])); ?></div>
                        </li>
                        <li><a class="dropdown-item text-white" href="<?php echo BASE_URL; ?>/profile"><i class="bi bi-person me-2"></i> Profile</a></li>
                        <li><a class="dropdown-item text-white" href="<?php echo BASE_URL; ?>/change-password"><i class="bi bi-shield-lock me-2"></i> Change Password</a></li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                        <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/logout"><i class="bi bi-box-arrow-left me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content p-3 p-md-4">
            <!-- Flash Messages -->
            <?php echo displayFlashMessages(); ?>
<?php endif; ?>


