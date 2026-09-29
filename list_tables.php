<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT.'/config/database.php';
$db = Database::getInstance()->getConnection();
$tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
echo "Tables:\n" . implode("\n", $tables);
