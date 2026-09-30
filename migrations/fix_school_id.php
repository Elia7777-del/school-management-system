<?php
/**
 * Fix Migration: Safely add school_id column to all required tables
 * This script uses IF NOT EXISTS checks so it's safe to run multiple times.
 */
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';

$db = Database::getInstance()->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== School ID Column Fix Migration ===\n\n";

// Tables that need school_id column
$tables = [
    'users',
    'students',
    'teachers',
    'parents',
    'classes',
    'subjects',
    'exams',
    'academic_years',
    'school_terms',
    'invoices',
    'fee_payments',
    'fee_structures',
    'announcements',
    'timetables',
];

$fixed = 0;
$skipped = 0;

foreach ($tables as $table) {
    // Check if table exists
    $tableExists = $db->query("SHOW TABLES LIKE '$table'")->fetch();
    if (!$tableExists) {
        echo "   ⚠ Table '$table' does not exist — skipping\n";
        continue;
    }

    // Check if school_id column already exists
    $colExists = $db->query("SHOW COLUMNS FROM `$table` LIKE 'school_id'")->fetch();

    if ($colExists) {
        echo "   ✓ $table.school_id already exists\n";
        $skipped++;
    } else {
        // Add the column
        $db->exec("ALTER TABLE `$table` ADD COLUMN `school_id` INT NULL DEFAULT NULL AFTER `id`");
        echo "   ✅ Added school_id to $table\n";
        $fixed++;
    }
}

// Also create the schools table if missing
echo "\n--- Checking schools table ---\n";
$schoolsExists = $db->query("SHOW TABLES LIKE 'schools'")->fetch();
if (!$schoolsExists) {
    $db->exec("
        CREATE TABLE `schools` (
            `id`         INT          NOT NULL AUTO_INCREMENT,
            `name`       VARCHAR(150) NOT NULL,
            `slug`       VARCHAR(100) NOT NULL UNIQUE,
            `email`      VARCHAR(100) DEFAULT NULL,
            `phone`      VARCHAR(30)  DEFAULT NULL,
            `address`    TEXT         DEFAULT NULL,
            `status`     ENUM('active','suspended','expired') NOT NULL DEFAULT 'active',
            `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    // Insert default school
    $db->exec("INSERT INTO schools (id, name, slug, email, status) VALUES (1, 'Default School', 'default-school', 'admin@school.com', 'active')");
    echo "   ✅ Created schools table + Default School (id=1)\n";
} else {
    // Make sure school id=1 exists
    $school1 = $db->query("SELECT id FROM schools WHERE id = 1")->fetch();
    if (!$school1) {
        $db->exec("INSERT INTO schools (id, name, slug, email, status) VALUES (1, 'Default School', 'default-school', 'admin@school.com', 'active')");
        echo "   ✅ Added Default School (id=1)\n";
    } else {
        echo "   ✓ schools table OK\n";
    }
}

// Create school_subscriptions if missing
echo "\n--- Checking school_subscriptions table ---\n";
$subsExists = $db->query("SHOW TABLES LIKE 'school_subscriptions'")->fetch();
if (!$subsExists) {
    $db->exec("
        CREATE TABLE `school_subscriptions` (
            `id`                INT           NOT NULL AUTO_INCREMENT,
            `school_id`         INT           NOT NULL,
            `plan`              ENUM('trial','basic','premium','enterprise') NOT NULL DEFAULT 'trial',
            `start_date`        DATE          NOT NULL,
            `end_date`          DATE          NOT NULL,
            `amount_paid`       DECIMAL(12,2) DEFAULT 0,
            `currency`          VARCHAR(10)   DEFAULT 'TZS',
            `payment_reference` VARCHAR(100)  DEFAULT NULL,
            `notes`             TEXT          DEFAULT NULL,
            `created_by`        INT           DEFAULT NULL,
            `created_at`        TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    // Add 10-year subscription for default school
    $db->exec("INSERT INTO school_subscriptions (school_id, plan, start_date, end_date, amount_paid, notes)
               VALUES (1, 'enterprise', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 10 YEAR), 0, 'Default school subscription')");
    echo "   ✅ Created school_subscriptions table + enterprise subscription for school 1\n";
} else {
    echo "   ✓ school_subscriptions table OK\n";
}

// Create sessions table if missing
echo "\n--- Checking sessions table ---\n";
$sessExists = $db->query("SHOW TABLES LIKE 'sessions'")->fetch();
if (!$sessExists) {
    $db->exec("
        CREATE TABLE `sessions` (
            `id`            VARCHAR(128) NOT NULL PRIMARY KEY,
            `data`          LONGTEXT     NOT NULL,
            `last_accessed` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "   ✅ Created sessions table\n";
} else {
    echo "   ✓ sessions table OK\n";
}

// Set existing data to school_id = 1
echo "\n--- Setting existing records to school_id = 1 ---\n";
foreach ($tables as $table) {
    $tableExists = $db->query("SHOW TABLES LIKE '$table'")->fetch();
    if (!$tableExists) continue;

    $colExists = $db->query("SHOW COLUMNS FROM `$table` LIKE 'school_id'")->fetch();
    if (!$colExists) continue;

    $updated = $db->exec("UPDATE `$table` SET school_id = 1 WHERE school_id IS NULL");
    if ($updated > 0) {
        echo "   ✅ Updated $updated rows in $table → school_id = 1\n";
    } else {
        echo "   ✓ $table: all rows already have school_id set\n";
    }
}

// Add system_admin role if missing
echo "\n--- Checking system_admin role ---\n";
$roleExists = $db->query("SELECT id FROM roles WHERE role_name = 'system_admin'")->fetch();
if (!$roleExists) {
    $db->exec("INSERT INTO roles (role_name, description) VALUES ('system_admin', 'Platform-level system administrator')");
    echo "   ✅ Added system_admin role\n";
} else {
    echo "   ✓ system_admin role OK (id={$roleExists['id']})\n";
}

// Add sysadmin user if missing
echo "\n--- Checking sysadmin user ---\n";
$sysuser = $db->query("SELECT id FROM users WHERE username = 'sysadmin'")->fetch();
if (!$sysuser) {
    $roleRow = $db->query("SELECT id FROM roles WHERE role_name = 'system_admin'")->fetch();
    if ($roleRow) {
        $hash = password_hash('SysAdmin@2024', PASSWORD_BCRYPT);
        $db->prepare("INSERT INTO users (username, email, password, role_id, status, school_id)
                      VALUES ('sysadmin', 'sysadmin@platform.com', ?, ?, 'active', NULL)")
           ->execute([$hash, $roleRow['id']]);
        echo "   ✅ Created sysadmin user (password: SysAdmin@2024)\n";
    }
} else {
    echo "   ✓ sysadmin user OK (id={$sysuser['id']})\n";
}

echo "\n=== Migration Complete! ===\n";
echo "Fixed: $fixed table(s)\n";
echo "Already OK: $skipped table(s)\n";
echo "\nYou can now delete this file: php migrations/fix_school_id.php\n";
