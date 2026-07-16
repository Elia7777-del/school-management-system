<?php
/**
 * Database Configuration
 * 
 * Singleton class for PDO database connection.
 * Uses prepared statements and UTF-8 encoding.
 * 
 * @package SchoolManagementSystem
 */

class Database
{
    /** @var Database|null Singleton instance */
    private static ?Database $instance = null;

    /** @var PDO Database connection */
    private PDO $connection;

    /** @var string Database host */
    private string $host = 'localhost';

    /** @var string Database name */
    private string $db_name = 'school_ms';

    /** @var string Database username */
    private string $username = 'root';

    /** @var string Database password */
    private string $password = '';

    /**
     * Private constructor — establishes PDO connection.
     */
    private function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Database Connection Failed: " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance of Database.
     *
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the PDO connection object.
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Prevent cloning of singleton.
     */
    private function __clone() {}
}
