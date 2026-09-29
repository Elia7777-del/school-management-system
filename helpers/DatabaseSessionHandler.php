<?php
/**
 * Database Session Handler
 * 
 * Implements SessionHandlerInterface to store PHP sessions in the MySQL database.
 * This makes the application stateless and compatible with AWS Load Balancers.
 */

class DatabaseSessionHandler implements SessionHandlerInterface
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function open(string $path, string $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $id): string|false
    {
        $stmt = $this->db->prepare("SELECT data FROM sessions WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $row['data'];
        }

        return '';
    }

    public function write(string $id, string $data): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO sessions (id, data, last_accessed)
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE data = VALUES(data), last_accessed = NOW()
        ");
        return $stmt->execute([$id, $data]);
    }

    public function destroy(string $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function gc(int $max_lifetime): int|false
    {
        $stmt = $this->db->prepare("DELETE FROM sessions WHERE last_accessed < DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$max_lifetime]);
        return $stmt->rowCount();
    }
}
