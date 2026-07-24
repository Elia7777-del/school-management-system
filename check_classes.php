<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';
$db = Database::getInstance()->getConnection();
$cols = $db->query('DESCRIBE classes')->fetchAll(PDO::FETCH_COLUMN);
echo "Classes columns: " . implode(', ', $cols) . "\n";
$cols2 = $db->query('DESCRIBE students')->fetchAll(PDO::FETCH_COLUMN);
echo "Students columns: " . implode(', ', $cols2) . "\n";
