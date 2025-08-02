<?php

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            if (!extension_loaded('pdo_mysql')) {
                throw new RuntimeException('PDO MySQL extension is not enabled.');
            }

            $host = getenv('MYSQLHOST');
            $port = getenv('MYSQLPORT') ?: 3306;
            $user = getenv('MYSQLUSER');
            $pass = getenv('MYSQLPASSWORD');
            $db   = getenv('MYSQLDATABASE');

            if (!$host || !$user || !$pass || !$db) {
                error_log('Missing environment variables for DB connection.');
                throw new RuntimeException('Environment variables for DB are not set.');
            }

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
                error_log('DB connection error: ' . $e->getMessage());
                throw new RuntimeException('DB connection error: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
