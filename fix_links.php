<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';
$db = Database::getInstance()->getConnection();

// Remove the wrong link: diouf kihegulo -> bernad makono (link_id = 6)
$stmt = $db->prepare("DELETE FROM student_parents WHERE id = ?");
$result = $stmt->execute([6]);

if ($result) {
    echo "Link #6 (diouf kihegulo -> bernad makono) removed successfully.\n";
} else {
    echo "Failed to remove link.\n";
}

// Verify remaining links
$rows = $db->query("
    SELECT sp.id as link_id,
           u.username as parent_user,
           s.first_name as student_fname, s.last_name as student_lname,
           s.admission_number
    FROM student_parents sp
    JOIN parents p ON sp.parent_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN students s ON sp.student_id = s.id
    ORDER BY p.id, s.id
")->fetchAll();

echo "\n=== Remaining links ===\n";
foreach ($rows as $r) {
    echo "Link #{$r['link_id']}: Parent [{$r['parent_user']}] -> Student: {$r['student_fname']} {$r['student_lname']} ({$r['admission_number']})\n";
}
