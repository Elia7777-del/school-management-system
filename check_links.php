<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';
$db = Database::getInstance()->getConnection();

// Show all parent links
$rows = $db->query("
    SELECT sp.id as link_id, sp.parent_id, sp.student_id,
           p.first_name as parent_fname, p.last_name as parent_lname,
           u.username as parent_user,
           s.first_name as student_fname, s.last_name as student_lname,
           s.admission_number
    FROM student_parents sp
    JOIN parents p ON sp.parent_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN students s ON sp.student_id = s.id
    ORDER BY p.id, s.id
")->fetchAll();

echo "=== student_parents links ===\n";
foreach ($rows as $r) {
    echo "Link #{$r['link_id']}: Parent [{$r['parent_user']}] ({$r['parent_fname']} {$r['parent_lname']}) "
       . "-> Student: {$r['student_fname']} {$r['student_lname']} ({$r['admission_number']})\n";
}

// Also show all parents
echo "\n=== parents table ===\n";
$parents = $db->query("
    SELECT p.id, p.first_name, p.last_name, p.relationship, u.username, u.id as user_id
    FROM parents p JOIN users u ON p.user_id = u.id
")->fetchAll();
foreach ($parents as $p) {
    echo "Parent #{$p['id']}: {$p['first_name']} {$p['last_name']} (user: {$p['username']}, user_id: {$p['user_id']})\n";
}
