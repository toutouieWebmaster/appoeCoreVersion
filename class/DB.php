<?php

namespace App;

use PDO;
use PDOException;

class DB
{
    private static $instance;
    protected static $dbh = null;

    public function __construct()
    {
        self::$dbh = self::connect();
    }

    /**
     * @return null
     */
    public static function connect()
    {
        if (is_null(self::$dbh)) {

            $attempts = NUM_OF_ATTEMPTS;

            while ($attempts > 0) {
                try {
                    self::$dbh = new PDO(DBPATH, DBUSER, DBPASS,
                        [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8;SET time_zone = "' . date('P') . '"']);
                    $attempts = 0;

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
    public static function initialize()
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
    public static function exec($sql, array $params = array())
    {
        if (self::$dbh = self::connect()) {
            try {
                $stmt = self::$dbh->prepare($sql);
                $stmt->execute($params);
                $stmt->lastInsertId = self::$dbh->lastInsertId();
                return $stmt;
            } catch (Exception $e) {
                setSqlError($e->getMessage());
            }
        }
        return false;
    }

    /**
     * @param $class
     * @param array $where
     * @return bool
     */
    public static function show($class, array $where = ['id'])
    {
        $params = array();
        $sql = 'SELECT * FROM ' . self::collect($class, 'tableName') . ' WHERE ';
        foreach ($where as $key => $value) {
            $sql .= ($key != 0 ? ' AND ' : '') . $value . ' = :' . $value;
            $params[':' . $value] = $value ? self::collect($class, $value) : null;
        }
        if ($return = self::exec($sql, $params)) {
            self::feed($class, $return->fetch(PDO::FETCH_OBJ));
            return true;
        }
        return false;
    }

    /**
     * @param $class
     * @param array $where
     * @param string $otherConditions
     * @return mixed
     */
    public static function showAll($class, array $where = [], $otherConditions = '')
    {
        $params = array();
        $sql = 'SELECT * FROM ' . self::collect($class, 'tableName');
        if (!isArrayEmpty($where)) {
            $sql .= ' WHERE ';
            foreach ($where as $key => $value) {
                $sql .= ($key != 0 ? ' AND ' : '') . $value . ' = :' . $value;
                $params[':' . $value] = $value ? self::collect($class, $value) : null;
            }
        }
        $sql .= ' ' . $otherConditions;
        if ($return = self::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param $class
     * @param array $attr
     * @return bool|object
     */
    public static function save($class, array $attr)
    {

        $params = array();
        $sql = 'INSERT INTO ' . self::collect($class, 'tableName') . ' (' . implode(', ', $attr) . ') 
                VALUES (:' . implode(', :', $attr) . ')';
        foreach ($attr as $value) {
            $params[':' . $value] = $value ? self::collect($class, $value) : null;
        }
        return self::exec($sql, $params);
    }

    /**
     * @param $class
     * @param array $attr
     * @param array $where
     * @return bool
     */
    public static function update($class, array $attr, array $where)
    {
        $params = array();
        $sql = 'UPDATE ' . self::collect($class, 'tableName') . ' SET ';
        foreach ($attr as $key => $value) {
            $sql .= ($key != 0 ? ', ' : '') . $value . ' = :' . $value;
            $params[':' . $value] = $value ? self::collect($class, $value) : null;
        }

        $sql .= ' WHERE ';
        foreach ($where as $key => $value) {
            $sql .= ($key != 0 ? ' AND ' : '') . $value . ' = :' . $value;
            $params[':' . $value] = $value ? self::collect($class, $value) : null;
        }
        return self::exec($sql, $params);
    }

    /**
     * @param $class
     * @param array $where
     * @return bool
     */
    public static function exist($class, array $where)
    {
        $params = array();
        $sql = 'SELECT `id` FROM ' . self::collect($class, 'tableName') . ' WHERE ';

        foreach ($where as $key => $value) {
            $sql .= ($key != 0 ? ' AND ' : '') . $value . ' = :' . $value;
            $params[':' . $value] = $value ? self::collect($class, $value) : null;
        }

        if ($return = self::exec($sql, $params)) {
            return $return->fetchColumn();
        }
        return false;
    }

    /**
     * @param $class
     * @param array $where
     * @return bool
     */
    public static function count($class, array $where = [])
    {
        $params = array();
        $sql = 'SELECT COUNT(id) AS COUNT FROM ' . self::collect($class, 'tableName');

        if (!isArrayEmpty($where)) {
            $sql .= ' WHERE ';
            foreach ($where as $key => $value) {
                $sql .= ($key != 0 ? ' AND ' : '') . $value . ' = :' . $value;
                $params[':' . $value] = $value ? self::collect($class, $value) : null;
            }
        }
        if ($return = self::exec($sql, $params)) {
            return $return->fetch()['COUNT'];
        }
        return false;
    }

    /**
     * @param $class
     * @param array $where
     * @return bool
     */
    public static function delete($class, array $where)
    {
        $params = array();
        $sql = 'DELETE FROM ' . self::collect($class, 'tableName') . ' WHERE ';

        foreach ($where as $key => $value) {
            $sql .= ($key != 0 ? ' AND ' : '') . $value . ' = :' . $value;
            $params[':' . $value] = $value ? self::collect($class, $value) : null;
        }
        return self::exec($sql, $params);
    }

    /**
     * Feed class attributs, ex: setId()
     *
     * @param $class
     * @param $data
     */
    public static function feed($class, $data)
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
     * @param $class
     * @param $attr
     * @return false|mixed
     */
    public static function collect($class, $attr)
    {
        $method = 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attr)));
        if (is_callable(array($class, $method))) {
            return $class->$method();
        }
        return false;
    }

    /**
     * @param $tableName
     * @return bool
     */
    public static function isTableExist($tableName)
    {
        $sql = 'DESCRIBE ' . $tableName;
        return DB::exec($sql);
    }

    /**
     * @param $tableName
     * @return bool
     */
    public static function checkTable($tableName)
    {
        $sql = 'SHOW TABLES LIKE :tableName';
        $return = self::exec($sql, array(':tableName' => '%' . $tableName . '%'));

        if ($return) {
            if ($return->rowCount() > 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $tableName
     * @return bool
     */
    public static function deleteTable($tableName)
    {
        $sql = 'DROP TABLE IF EXISTS ' . $tableName;
        $return = self::exec($sql, array());

        if ($return) {
            return true;
        }
        return false;
    }

    /**
     * @return bool|array
     */
    public static function getTables()
    {
        $sql = 'SHOW TABLES';
        $return = self::exec($sql, array());

        if ($return->rowCount() > 0) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }

        return false;
    }

    /**
     * @param $folder
     * @param $name
     * DataBase BuckUp
     */
    public static function backup($folder, $name = 'db')
    {
        $file = getenv('DOCUMENT_ROOT') . '/app/backup/' . $folder . '/' . $name . '.sql.gz';
        system('mysqldump --no-tablespace --opt -h' . DBHOST . ' -u' . DBUSER . ' -p"' . DBPASS . '" ' . DBNAME . ' | gzip > ' . $file);
    }
}