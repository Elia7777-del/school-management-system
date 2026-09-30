<?php
define('APP_ROOT', 'C:/xampp/htdocs/school-management-system');
require_once APP_ROOT . '/config/database.php';
$db = Database::getInstance()->getConnection();

echo "=== Fixing orphan records ===\n\n";

// 1. Fix users with no school_id (except sysadmin)
$count = $db->exec("UPDATE users SET school_id = 1 WHERE school_id IS NULL AND username != 'sysadmin'");
echo "Fixed $count user(s) with missing school_id\n";

// 2. Check if user 'dangote mimi' (id=9) has a parents record
$stmt = $db->query("SELECT u.id, u.username FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'parent' AND u.id NOT IN (SELECT COALESCE(user_id, 0) FROM parents)");
$orphanParentUsers = $stmt->fetchAll();

foreach ($orphanParentUsers as $user) {
    echo "\nUser '{$user['username']}' (id={$user['id']}) has parent role but no parents record.\n";
    
    // Try to extract first_name / last_name from username
    $parts = explode(' ', $user['username'], 2);
    $firstName = $parts[0] ?? $user['username'];
    $lastName = $parts[1] ?? '';
    
    $stmt = $db->prepare("INSERT INTO parents (user_id, first_name, last_name, relationship, school_id) VALUES (?, ?, ?, 'guardian', 1)");
    $stmt->execute([$user['id'], $firstName, $lastName]);
    $newId = $db->lastInsertId();
    echo "  -> Created parents record id=$newId for user '{$user['username']}'\n";
}

if (empty($orphanParentUsers)) {
    echo "\nNo orphan parent users found.\n";
}

// 3. Verify final state
echo "\n=== Final State ===\n";
$r = $db->query("
    SELECT p.id, p.first_name, p.last_name, p.user_id, p.school_id, u.username
    FROM parents p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.deleted_at IS NULL
");
foreach ($r as $row) {
    echo "  parent_id={$row['id']} {$row['first_name']} {$row['last_name']} user={$row['username']} school_id={$row['school_id']}\n";
}

echo "\nDone!\n";
