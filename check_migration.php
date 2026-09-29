<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';
$db = Database::getInstance()->getConnection();

echo "=== TABLES IN DATABASE ===\n";
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo implode("\n", $tables) . "\n\n";

echo "=== CHECK school_id COLUMNS ===\n";
$checkTables = ['users', 'students', 'teachers', 'classes', 'subjects', 'exams', 'invoices', 'announcements'];
foreach ($checkTables as $t) {
    $cols = $db->query("SHOW COLUMNS FROM `$t` LIKE 'school_id'")->fetch();
    echo "$t.school_id: " . ($cols ? "EXISTS ✓" : "MISSING ✗") . "\n";
}

echo "\n=== schools TABLE ===\n";
$exists = $db->query("SHOW TABLES LIKE 'schools'")->fetch();
echo "schools table: " . ($exists ? "EXISTS ✓" : "MISSING ✗") . "\n";

echo "\n=== school_subscriptions TABLE ===\n";
$exists2 = $db->query("SHOW TABLES LIKE 'school_subscriptions'")->fetch();
echo "school_subscriptions table: " . ($exists2 ? "EXISTS ✓" : "MISSING ✗") . "\n";

echo "\n=== system_admin ROLE ===\n";
$role = $db->query("SELECT id FROM roles WHERE role_name = 'system_admin'")->fetch();
echo "system_admin role: " . ($role ? "EXISTS (id={$role['id']}) ✓" : "MISSING ✗") . "\n";

echo "\n=== sysadmin USER ===\n";
$sysuser = $db->query("SELECT id, username, email FROM users WHERE username = 'sysadmin'")->fetch();
echo "sysadmin user: " . ($sysuser ? "EXISTS ✓ (id={$sysuser['id']})" : "MISSING ✗") . "\n";
