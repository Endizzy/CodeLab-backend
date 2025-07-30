<?php

class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            $host = getenv('MYSQLHOST');
            $port = getenv('MYSQLPORT') ?: 3306;
            $user = getenv('MYSQLUSER');
            $pass = getenv('MYSQLPASSWORD');
            $db   = getenv('MYSQLDATABASE');

            if (!$host || !$user || !$pass || !$db) {
                error_log('Отсутствуют переменные окружения для подключения к базе данных.');
                throw new RuntimeException('Переменные окружения для БД не заданы.');
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
                error_log('Ошибка подключения к БД: ' . $e->getMessage());
                throw new RuntimeException('Ошибка подключения к БД: ' . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
