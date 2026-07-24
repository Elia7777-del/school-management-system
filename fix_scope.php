<?php
define("APP_ROOT", "C:/xampp/htdocs/school-management-system");
require_once APP_ROOT . "/config/database.php";
$db = Database::getInstance()->getConnection();

$stmt = $db->query("SHOW COLUMNS FROM users LIKE 'admin_scope'");
$col = $stmt->fetch();
if (!$col) {
    $db->exec("ALTER TABLE users ADD COLUMN admin_scope ENUM('all','primary','secondary') NOT NULL DEFAULT 'all' AFTER role_id");
    echo "Column admin_scope added.\n";
} else {
    echo "Column admin_scope already present.\n";
}

$n1 = $db->exec("UPDATE users SET admin_scope='primary' WHERE username='primary_admin'");
echo "primary_admin updated: $n1 row(s).\n";

$n2 = $db->exec("UPDATE users SET admin_scope='secondary' WHERE username='secondary_admin'");
echo "secondary_admin updated: $n2 row(s).\n";

$rows = $db->query("SELECT username, admin_scope FROM users WHERE username IN ('primary_admin','secondary_admin')")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r["username"] . " => " . $r["admin_scope"] . "\n";
}
echo "Done.\n";
