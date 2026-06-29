<?php
// ============================================================
// SRI MANIKANTA POOJA STORES â€” DATABASE CONNECTION
// ============================================================

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $port = defined('DB_PORT') ? DB_PORT : 3306;
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                DB_HOST, $port, DB_NAME, DB_CHARSET
            );
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_FOUND_ROWS   => true,
                ]);
            } catch (PDOException $e) {
                if (DEBUG_MODE) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Service temporarily unavailable. Please try again later.']);
                }
                exit;
            }
        }
        return self::$instance;
    }
}

// Shorthand helper
function db(): PDO {
    return Database::getConnection();
}

