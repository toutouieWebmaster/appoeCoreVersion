<?php

namespace App;

use PDO;
use PDOException;
use Exception;

final class DB
{
    private static ?self $instance = null;
    protected static ?PDO $dbh = null;

    private function __construct()
    {
        self::$dbh = self::connect();
    }

    /**
     * @return PDO|null
     */
    public static function connect(): ?PDO
    {
        if (is_null(self::$dbh)) {

            $attempts = NUM_OF_ATTEMPTS;

            while ($attempts > 0) {
                try {
                    self::$dbh = new PDO(
                        DBPATH,
                        DBUSER,
                        DBPASS,
                        [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4, time_zone = "' . (new \DateTime())->format('P') . '"'
                        ]
                    );
                    break;
                } catch (PDOException $e) {
                    $attempts--;
                    sleep(1);
                }
            }
        }
        return self::$dbh;
    }

    /**
     * @return DB
     */
    public static function initialize(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $sql
     * @param array $params
     * @return bool|object
     */
    public static function exec($sql, array $params = []): false|object
    {
        $dbh = self::connect();
        if ($dbh) {
            try {
                $stmt = $dbh->prepare($sql);
                $stmt->execute($params);
                return $stmt;
            } catch (Exception $e) {
                setSqlError($e->getMessage());
            }
        }
        return false;
    }

    /**
     * @return string|false
     */
    public static function lastInsertId(): string|false
    {
        $dbh = self::connect();
        return $dbh ? $dbh->lastInsertId() : false;
    }

    /**
     * @param object $class
     * @param array $where
     * @return bool
     */
    public static function show(object $class, array $where = ['id']): bool
    {
        $params = [];
        $sql = 'SELECT * FROM ' . self::collect($class, 'tableName') . ' WHERE ' . self::buildWhereClause($where, $params, $class);

        $result = self::exec($sql, $params);
        if ($result) {
            self::feed($class, $result->fetch(PDO::FETCH_OBJ));
            return true;
        }
        return false;
    }

    /**
     * @param object $class
     * @param array $where
     * @param string $otherConditions
     * @return false|array
     */
    public static function showAll($class, array $where = [], string $otherConditions = ''): false|array
    {
        $params = [];
        $sql = 'SELECT * FROM ' . self::collect($class, 'tableName');
        if (!isArrayEmpty($where)) {
            $sql .= ' WHERE ' . self::buildWhereClause($where, $params, $class);
        }
        $sql .= ' ' . $otherConditions;
        $result = self::exec($sql, $params);
        return $result ? $result->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /**
     * @param object $class
     * @param array $attr
     * @return bool|object
     */
    public static function save(object $class, array $attr): false|object
    {

        $params = [];
        $fields = implode(', ', $attr);
        $placeholders = ':' . implode(', :', $attr);

        foreach ($attr as $value) {
            $params[":$value"] = self::collect($class, $value);
        }

        $sql = "INSERT INTO " . self::collect($class, 'tableName') . " ($fields) VALUES (:$placeholders)";
        return self::exec($sql, $params);
    }

    /**
     * @param object $class
     * @param array $attr
     * @param array $where
     * @return bool
     */
    public static function update(object $class, array $attr, array $where): bool
    {
        $params = [];
        $set = self::buildSetClause($attr, $params, $class);
        $conditions = self::buildWhereClause($where, $params, $class);

        $sql = 'UPDATE ' . self::collect($class, 'tableName') . ' SET ' . $set . ' WHERE ' . $conditions;
        return (bool)self::exec($sql, $params);
    }

    /**
     * @param object $class
     * @param array $where
     * @return bool
     */
    public static function exist(object $class, array $where): bool
    {
        $params = [];
        $sql = 'SELECT id FROM ' . self::collect($class, 'tableName') . ' WHERE ' . self::buildWhereClause($where, $params, $class);

        $result = self::exec($sql, $params);
        return (bool)($result && $result->fetchColumn());
    }

    /**
     * @param object $class
     * @param array $where
     * @return bool
     */
    public static function count(object $class, array $where = []): int
    {
        $params = [];
        $sql = 'SELECT COUNT(id) AS count FROM ' . self::collect($class, 'tableName');

        if (!empty($where)) {
            $sql .= ' WHERE ' . self::buildWhereClause($where, $params, $class);
        }

        $result = self::exec($sql, $params);
        return $result ? (int)($result->fetch()['count']) : 0;
    }

    /**
     * @param object $class
     * @param array $where
     * @return bool
     */
    public static function delete(object $class, array $where): bool
    {
        $params = [];
        $sql = 'DELETE FROM ' . self::collect($class, 'tableName') . ' WHERE ' . self::buildWhereClause($where, $params, $class);

        return (bool)self::exec($sql, $params);
    }

    /**
     * Feed class attributs, ex: setId()
     *
     * @param object $class
     * @param $data
     */
    public static function feed(object $class, $data): void
    {
        if ($data) {
            foreach ($data as $attribut => $value) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

                if (is_callable(array($class, $method))) {
                    $class->$method($value);
                }
            }
        }
    }

    /**
     * Collect attribut from class, ex: getId()
     *
     * @param object $class
     * @param $attr
     * @return false|mixed
     */
    public static function collect(object $class, $attr): mixed
    {
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attr)));
        return method_exists($class, $method) ? $class->$method() : false;

    }

    /**
     * @param string $tableName
     * @return object|bool
     */
    public static function isTableExist(string $tableName): object|bool
    {
        $sql = 'DESCRIBE ' . $tableName;
        return DB::exec($sql);
    }

    /**
     * @param string $tableName
     * @return bool
     */
    public static function checkTable(string $tableName): bool
    {
        $sql = 'SHOW TABLES LIKE :tableName';
        $result = self::exec($sql, [':tableName' => $tableName]);
        return $result && $result->rowCount() > 0;
    }

    /**
     * @param string $tableName
     * @return bool
     */
    public static function deleteTable(string $tableName): bool
    {
        return (bool)self::exec('DROP TABLE IF EXISTS ' . $tableName);
    }

    /**
     * @return bool|array
     */
    public static function getTables(): false|array
    {
        $result = self::exec('SHOW TABLES');
        return $result && $result->rowCount() > 0 ? $result->fetchAll(PDO::FETCH_OBJ) : false;
    }

    /**
     * @param string $folder
     * @param string $name
     * DataBase BuckUp
     */
    //ATTENTION ADAPTE POUR LOCAL
    public static function backup(string $folder, string $name = 'db'): bool
    {
        $backupDir = __DIR__ . '/../backup/' . $folder;
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0775, true);
        }

        $file = $backupDir . '/' . $name . '.sql.gz';

        $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
        $file = realpath(dirname($file)) . DIRECTORY_SEPARATOR . basename($file);

        $dbHost = escapeshellarg(DBHOST);
        $dbUser = escapeshellarg(DBUSER);
        $dbName = escapeshellarg(DBNAME);
        $dbPass = escapeshellarg(DBPASS);
        $fileEscaped = escapeshellarg($file);

        // POUR LOCAL UNIQUEMENT !!
        $mysqldumpPath = "C:/xampp/mysql/bin/mysqldump.exe";

        $command = sprintf(
            '"%s" --no-tablespace --opt -h%s -u%s --password=%s %s | gzip > %s',
            $mysqldumpPath,
            $dbHost,
            $dbUser,
            $dbPass,
            $dbName,
            $fileEscaped
        );

        exec($command . ' 2>&1', $output, $returnVar);

        // Facultatif pour débugger
        error_log('Command: ' . $command);
        error_log('Return var: ' . $returnVar);
        error_log('Output: ' . print_r($output, true));

        return $returnVar === 0;
    }



    /**
     * @param array $where
     * @param array $params
     * @param object $class
     * @return string
     */
    private static function buildWhereClause(array $where, array &$params, object $class): string
    {
        return implode(' AND ', array_map(static function ($value) use (&$params, $class) {
            $params[":$value"] = self::collect($class, $value);
            return "$value = :$value";
        }, array_values($where)));
    }

    /**
     * @param array $attr
     * @param array $params
     * @param object $class
     * @return string
     */
    private static function buildSetClause(array $attr, array &$params, object $class): string
    {
        return implode(', ', array_map(static function ($value) use (&$params, $class) {
            $params[":$value"] = self::collect($class, $value);
            return "$value = :$value";
        }, array_values($attr)));
    }
}