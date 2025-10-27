<?php

namespace App;

use PDO;

class Option
{
    private string $tableName = '`' . TABLEPREFIX . 'appoe_options`';
    private int $id;
    private string $type;
    private ?string $description = null;
    private string $key;
    private string $val;
    private string $updated_at;

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
    public function createTable(): bool
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param ?string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * @param string $val
     */
    public function setVal(string $val): void
    {
        $this->val = $val;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    /**
     * @param string $updated_at
     */
    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return bool|array|object
     */
    public function show(): bool|array|object
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `id` = :id';
        $params = array(':id' => $this->id);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool|array|object
     */
    public function showByType(): bool|array|object
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `type` = :type';
        $params = array(':type' => $this->type);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return bool|array|object
     */
    public function showByKey(): bool|array|object
    {
        $sql = 'SELECT * FROM ' . $this->tableName . ' WHERE `type` = :type AND `key` = :key';
        $params = array(':type' => $this->type, ':key' => $this->key);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetch(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return string|false
     */
    public function getValByKey(): string|false
    {
        if ($option = $this->showByKey()) {
            return $option->val;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function save(): bool
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
    public function update(): bool
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
     * @return bool|array|object
     */
    public function exist(): bool|array|object
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
    public function delete(): bool
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
    public function deleteByTypeAndKey(): bool
    {
        $sql = 'DELETE FROM ' . $this->tableName . ' WHERE `type` = :type AND `key` = :key';
        if (DB::exec($sql, [':type' => $this->type, ':key' => $this->key])) {
            appLog('Delete option -> type: ' . $this->type . ' key: ' . $this->key);
            return true;
        }
        return false;
    }
}