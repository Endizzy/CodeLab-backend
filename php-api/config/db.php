<?php
class Database {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {

            $host = getenv('MYSQLHOST');           
            $port = getenv('MYSQLPORT');           
            $db = getenv('MYSQL_DATABASE');        
            $user = getenv('MYSQLUSER');           
            $pass = getenv('MYSQL_ROOT_PASSWORD'); 

            if (!$host || !$port || !$db || !$user || !$pass) {
                error_log('Missing one or more MySQL environment variables.');
                throw new RuntimeException('Ошибка: Не настроены переменные окружения для подключения к БД.');
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
                error_log('Ошибка подключения к БД: ' . $e->getMessage() . ' (DSN: ' . $dsn . ')');
                throw new RuntimeException('Ошибка подключения к БД: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
