<?php
/**
 * Migration: Create Sessions Table
 * This creates the table needed for database-backed sessions to support load balancing.
 */
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';

$db = Database::getInstance()->getConnection();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Creating Sessions Table ===\n\n";

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS `sessions` (
            `id` VARCHAR(128) NOT NULL PRIMARY KEY,
            `data` LONGTEXT NOT NULL,
            `last_accessed` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "   ✓ sessions table ready.\n";
} catch (Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Migration Complete! ===\n";
