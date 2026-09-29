<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/helpers/session.php';

startSession();
echo "Testing session creation...\n";
$_SESSION['test_key'] = 'Hello AWS Load Balancer!';
echo "Session ID: " . session_id() . "\n";

// Now check if it's in the DB
$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM sessions");
$sessions = $stmt->fetchAll();

echo "Sessions in Database:\n";
print_r($sessions);
