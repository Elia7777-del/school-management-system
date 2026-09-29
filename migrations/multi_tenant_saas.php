<?php
/**
 * Migration: Multi-Tenant SaaS Architecture
 * Adds schools, school_subscriptions tables and school_id to all relevant tables.
 */
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';

$db = Database::getInstance()->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Multi-Tenant Migration ===\n\n";

// 1. Create schools table
echo "[1] Creating schools table...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS `schools` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(100) NOT NULL UNIQUE COMMENT 'URL-safe identifier',
        `email` VARCHAR(191) DEFAULT NULL,
        `phone` VARCHAR(30) DEFAULT NULL,
        `address` TEXT DEFAULT NULL,
        `logo` VARCHAR(255) DEFAULT NULL,
        `status` ENUM('active','suspended','expired') NOT NULL DEFAULT 'active',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "   ✓ schools table ready.\n";

// 2. Create school_subscriptions table
echo "[2] Creating school_subscriptions table...\n";
$db->exec("
    CREATE TABLE IF NOT EXISTS `school_subscriptions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `school_id` INT NOT NULL,
        `plan` ENUM('trial','basic','premium','enterprise') NOT NULL DEFAULT 'trial',
        `start_date` DATE NOT NULL,
        `end_date` DATE NOT NULL,
        `amount_paid` DECIMAL(12,2) DEFAULT 0.00,
        `currency` VARCHAR(10) DEFAULT 'TZS',
        `payment_reference` VARCHAR(100) DEFAULT NULL,
        `notes` TEXT DEFAULT NULL,
        `created_by` INT DEFAULT NULL COMMENT 'system_admin user id',
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`school_id`) REFERENCES `schools`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "   ✓ school_subscriptions table ready.\n";

// 3. Insert the default school (existing data)
echo "[3] Inserting default school...\n";
$existing = $db->query("SELECT id FROM schools WHERE id = 1")->fetch();
if (!$existing) {
    $db->exec("
        INSERT INTO schools (id, name, slug, status) VALUES (1, 'Default School', 'default-school', 'active')
    ");
    echo "   ✓ Default school inserted (id=1).\n";
} else {
    echo "   → Default school already exists.\n";
}

// 4. Add school_id to tables
$tables = [
    'users'         => "ALTER TABLE `users` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1 COMMENT 'NULL = system admin'",
    'students'      => "ALTER TABLE `students` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'teachers'      => "ALTER TABLE `teachers` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'parents'       => "ALTER TABLE `parents` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'classes'       => "ALTER TABLE `classes` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'subjects'      => "ALTER TABLE `subjects` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'exams'         => "ALTER TABLE `exams` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'academic_years'=> "ALTER TABLE `academic_years` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'school_terms'  => "ALTER TABLE `school_terms` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'invoices'      => "ALTER TABLE `invoices` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'fee_payments'  => "ALTER TABLE `fee_payments` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'fee_structures'=> "ALTER TABLE `fee_structures` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'announcements' => "ALTER TABLE `announcements` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
    'timetables'    => "ALTER TABLE `timetables` ADD COLUMN IF NOT EXISTS `school_id` INT NOT NULL DEFAULT 1",
];

echo "[4] Adding school_id columns...\n";
foreach ($tables as $table => $sql) {
    try {
        $db->exec($sql);
        echo "   ✓ $table.school_id added/exists.\n";
    } catch (Exception $e) {
        echo "   ✗ $table: " . $e->getMessage() . "\n";
    }
}

// 5. Allow users.school_id to be NULL (system admin has no school)
echo "[5] Allowing NULL school_id on users table (for system admins)...\n";
try {
    $db->exec("ALTER TABLE `users` MODIFY `school_id` INT NULL DEFAULT 1");
    echo "   ✓ users.school_id now nullable.\n";
} catch (Exception $e) {
    echo "   → " . $e->getMessage() . "\n";
}

// 6. Add default subscription for the default school
echo "[6] Adding default subscription for school #1...\n";
$existingSub = $db->query("SELECT id FROM school_subscriptions WHERE school_id = 1")->fetch();
if (!$existingSub) {
    $db->exec("
        INSERT INTO school_subscriptions (school_id, plan, start_date, end_date, amount_paid)
        VALUES (1, 'enterprise', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 10 YEAR), 0)
    ");
    echo "   ✓ Enterprise subscription added (10 years).\n";
} else {
    echo "   → Subscription already exists.\n";
}

// 7. Update roles table — add system_admin role if not exists
echo "[7] Adding system_admin role...\n";
$sysRole = $db->query("SELECT id FROM roles WHERE role_name = 'system_admin'")->fetch();
if (!$sysRole) {
    $db->exec("INSERT INTO roles (role_name, description) VALUES ('system_admin', 'Platform-level administrator managing all schools')");
    echo "   ✓ system_admin role added.\n";
} else {
    echo "   → system_admin role already exists.\n";
}

$sysRoleId = $db->query("SELECT id FROM roles WHERE role_name = 'system_admin'")->fetch()['id'];

// 8. Create default system admin user
echo "[8] Creating system admin user...\n";
$existingSysAdmin = $db->query("SELECT id FROM users WHERE username = 'sysadmin'")->fetch();
if (!$existingSysAdmin) {
    $hashed = password_hash('SysAdmin@2024', PASSWORD_DEFAULT);
    $db->exec("
        INSERT INTO users (username, email, password, role_id, school_id, status)
        VALUES ('sysadmin', 'sysadmin@platform.com', '$hashed', $sysRoleId, NULL, 'active')
    ");
    echo "   ✓ System admin created.\n";
    echo "   → Username: sysadmin | Password: SysAdmin@2024\n";
} else {
    echo "   → System admin already exists.\n";
}

echo "\n=== Migration Complete! ===\n";
