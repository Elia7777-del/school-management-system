<?php
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    $sql = "
    CREATE TABLE IF NOT EXISTS `invoices` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `student_id` INT NOT NULL,
        `academic_year_id` INT NOT NULL,
        `term_id` INT DEFAULT NULL,
        `invoice_number` VARCHAR(50) NOT NULL UNIQUE,
        `amount` DECIMAL(12,2) NOT NULL,
        `amount_paid` DECIMAL(12,2) NOT NULL DEFAULT 0,
        `status` ENUM('pending', 'partial', 'paid') NOT NULL DEFAULT 'pending',
        `due_date` DATE DEFAULT NULL,
        `description` VARCHAR(255) DEFAULT NULL,
        `created_by` INT NOT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        CONSTRAINT `fk_inv_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
        CONSTRAINT `fk_inv_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
        CONSTRAINT `fk_inv_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    
    CREATE TABLE IF NOT EXISTS `payment_transactions` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `invoice_id` INT DEFAULT NULL,
        `student_id` INT NOT NULL,
        `reference` VARCHAR(100) NOT NULL UNIQUE,
        `azampay_transaction_id` VARCHAR(100) DEFAULT NULL,
        `amount` DECIMAL(12,2) NOT NULL,
        `provider` VARCHAR(50) NOT NULL,
        `account_number` VARCHAR(50) NOT NULL,
        `status` ENUM('initiated', 'pending', 'success', 'failed') NOT NULL DEFAULT 'initiated',
        `response_message` TEXT DEFAULT NULL,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        CONSTRAINT `fk_pt_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
        CONSTRAINT `fk_pt_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    $db->exec($sql);
    echo "Tables created successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
