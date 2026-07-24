<?php
define("APP_ROOT", "C:/xampp/htdocs/school-management-system");
require_once APP_ROOT . "/config/database.php";
$db = Database::getInstance()->getConnection();

$db->exec("
CREATE TABLE IF NOT EXISTS parent_comments (
    id          INT           NOT NULL AUTO_INCREMENT,
    student_id  INT           NOT NULL,
    parent_id   INT           NOT NULL,
    comment     TEXT          NOT NULL,
    created_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_pc_student (student_id),
    INDEX idx_pc_parent  (parent_id),
    CONSTRAINT fk_pc_student FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    CONSTRAINT fk_pc_parent  FOREIGN KEY (parent_id)  REFERENCES parents(id)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

echo "Table parent_comments created (or already exists).\n";
