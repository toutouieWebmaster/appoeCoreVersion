<?php

namespace App;

use PDO;

class Option
{
    private $tableName = '`' . TABLEPREFIX . 'appoe_options`';
    private $id;
    private $type;
    private $description = null;
    private $key;
    private $val;
    private $updated_at;

    /**
     * Option constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {

        if (!empty($data['type']) && !empty($data['key']) && !empty($data['val'])) {

            $this->type = $data['type'];
            $this->key = $data['key'];
            $this->val = $data['val'];

            if (!$this->exist()) {
                $this->save();
            } else {
                $this->update();
            }
        }
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $this->tableName . ' (
                `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`),
                `type` VARCHAR(100) NOT NULL,
                `description` VARCHAR(255) NULL DEFAULT NULL,
                `key` VARCHAR(255) NOT NULL,
                `val` TEXT NOT NULL,
                UNIQUE (`type`, `key`),
                `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
				INSERT INTO ' . $this->tableName . ' (`id`, `type`, `description`, `key`, `val`, `updated_at`) VALUES
                (1, "PREFERENCE", "Mode maintenance", "maintenance", "false", NOW()),
                (2, "PREFERENCE", "Forcer le site en HTTPS", "forceHTTPS", "false", NOW()),
                (3, "PREFERENCE", "Autoriser la mise en cache des fichiers", "cacheProcess", "false", NOW()),
                (4, "PREFERENCE", "Autoriser le travail sur la même page", "sharingWork", "false", NOW()),
                (5, "PREFERENCE", "Autoriser l\'API", "allowApi", "false", NOW()),
                (6, "DATA", "Clé API", "apiToken", "", NOW()),
                (7, "DATA", "Adresse Email par défaut", "defaultEmail", "", NOW()),
                (8, "THEME", "", "--colorPrimary", "#3eb293", NOW()),
                (9, "THEME", "", "--colorPrimaryOpacity", "rgba(62, 178, 147, 0.7)", NOW()),
                (10, "THEME", "", "--textBgColorPrimary", "#FFF", NOW()),
                (11, "THEME", "", "--colorSecondary", "#FF9373", NOW()),
                (12, "THEME", "", "--colorSecondaryOpacity", "rgba(255, 147, 117, 0.7)", NOW()),
                (13, "THEME", "", "--textBgColorSecondary", "#FFF", NOW()),
                (14, "THEME", "", "--colorTertiary", "#3eb293", NOW()),
                (15, "THEME", "", "--colorTertiaryOpacity", "rgba(62, 178, 147, 0.7)", NOW()),
                (16, "THEME", "", "--textBgColorTertiary", "#FFF", NOW());';
        return (bool)DB::exec($sql);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param null $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return null
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * @param null $val
     */
    public function setVal($val)
    {
        $this->val = $val;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return bool|array
     */
    public function show()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id` = :id';
        $params = array(':id' => $this->id);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool|array
     */
    public function showByType()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `type` = :type';
        $params = array(':type' => $this->type);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool|object
     */
    public function showByKey()
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `type` = :type AND `key` = :key';
        $params = array(':type' => $this->type, ':key' => $this->key);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return mixed|false
     */
    public function getValByKey(): mixed
    {
        if ($option = $this->showByKey()) {
            return $option->val;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO ' . $this->tableName . ' (`type`, `description`, `key`, `val`) VALUES (:type, :description, :key, :val)';
        $params = array(':type' => $this->type, ':description' => $this->description, ':key' => $this->key, ':val' => $this->val);
        if (DB::exec($sql, $params)) {
            appLog('Add option -> type: ' . $this->type . ' key:' . $this->key . ' val:' . $this->val);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . $this->tableName . ' SET `val` = :val WHERE `type` = :type AND `key` = :key';
        $params = array(':type' => $this->type, ':key' => $this->key, ':val' => $this->val);
        if (DB::exec($sql, $params)) {
            appLog('Update option -> type: ' . $this->type . ' key:' . $this->key . ' val:' . $this->val);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {
        $sql = 'SELECT `id` FROM ' . $this->tableName . ' WHERE `type` = :type AND `key` = :key';
        $params = array(':type' => $this->type, ':key' => $this->key);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchColumn();
        }
        return false;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `id` = :id';
        if (DB::exec($sql, [':id' => $this->id])) {
            appLog('Delete option -> id: ' . $this->id);
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function deleteByTypeAndKey()
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `type` = :type AND `key` = :key';
        if (DB::exec($sql, [':type' => $this->type, ':key' => $this->key])) {
            appLog('Delete option -> type: ' . $this->type . ' key: ' . $this->key);
            return true;
        }
        return false;
    }
}