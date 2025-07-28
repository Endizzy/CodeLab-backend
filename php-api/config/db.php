<?php
class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            $dbUrl = getenv('DATABASE_URL');

            if (!$dbUrl) {
                error_log('Missing DATABASE_URL environment variable.');
                throw new RuntimeException('Ошибка: DATABASE_URL не настроена для подключения к БД.');
            }

            $urlParts = parse_url($dbUrl);

            $host = $urlParts['host'];
            $port = $urlParts['port'] ?? 3306; 
            $user = $urlParts['user'];
            $pass = $urlParts['pass'];
            $db = ltrim($urlParts['path'], '/'); 

            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$pdo = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log('Ошибка подключения к БД: ' . $e->getMessage() . ' (DSN: ' . $dsn . ')');
                throw new RuntimeException('Ошибка подключения к БД: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
