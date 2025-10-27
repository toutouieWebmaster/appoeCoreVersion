<?php

namespace App;

use PDO;

/**
 *
 */
class Category
{
    /**
     * @var int
     */
    private int $id;
    /**
     * @var string
     */
    private string $type;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var int
     */
    private int $parentId;
    /**
     * @var int
     */
    private int $position = 999;
    /**
     * @var int
     */
    private int $status = 1;

    /**
     * @var ?PDO
     */
    private ?PDO $dbh = null;

    /**
     * @param ?int $idCategory
     */
    public function __construct(?int $idCategory = null)
    {
        $this->dbh ??= DB::connect();

        if ($idCategory !== null) {
            $this->id = $idCategory;
            $this->show();
        }
    }

    /**
     * @return bool
     */
    public function show(): bool
    {

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_categories WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $row = $stmt->fetch(PDO::FETCH_OBJ);
            $this->feed($row);

            return true;
        }
    }

    /**
     * Feed class attributs
     * @param $data
     */
    public function feed($data): void
    {
        if (isset($data)) {
            foreach ($data as $attribut => $value) {
                $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

                if (is_callable(array($this, $method))) {
                    $this->$method($value);
                }
            }
        }
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
    public function setId(int $id): void
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_categories` (
  					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                    `type` VARCHAR(150) NOT NULL,
                    `name` VARCHAR(250) NOT NULL,
                    `parentId` INT(11) UNSIGNED NOT NULL,
                    UNIQUE (`type`, `name`, `parentId`),
                    `position` INT(11) NOT NULL DEFAULT "999",
                    `status` TINYINT(1) NOT NULL DEFAULT 1,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;ALTER TABLE `' . TABLEPREFIX . 'appoe_categories` AUTO_INCREMENT = 11;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }

    /**
     * @param ?int $parentId
     * @return array|object|bool
     */
    public function showByType(?int $parentId = null): array|object|bool
    {

        $params = array(':type' => $this->type);
        $sqlAdd = '';

        if (is_numeric($parentId)) {
            $params['parentId'] = $parentId;
            $sqlAdd .= ' parentId = :parentId AND ';
        }

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_categories WHERE ' . $sqlAdd . ' type = :type AND status = 1 ORDER BY position ASC, parentId ASC';
        $return = DB::exec($sql, $params);

        if ($return) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @param bool $categoriesCount
     * @return array|object|bool|int
     */
    public function showAll(bool $categoriesCount = false): array|object|bool|int
    {

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_categories WHERE status = 1 ORDER BY name ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            $data = $stmt->fetchAll(PDO::FETCH_OBJ);

            return (!$categoriesCount) ? $data : $count;
        }
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_categories (type, name, parentId, position) VALUES(:type, :name, :parentId, :position)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':parentId', $this->parentId);
        $stmt->bindParam(':position', $this->position);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Creating category -> type: ' . $this->type . ' name:' . $this->name . ' parentId:' . $this->parentId . ' position:' . $this->position);
            return true;
        }
    }

    /**
     * @param bool $forUpdate
     *
     * @return bool
     */
    public function notExist(bool $forUpdate = false): bool
    {

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_categories WHERE name = :name AND type = :type AND parentId = :parentId';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':parentId', $this->parentId);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {

                $data = $stmt->fetch(PDO::FETCH_OBJ);

                if ($forUpdate) {
                    if ($data->id == $this->id) {
                        return true;
                    }
                }

                $this->feed($data);

                return false;
            } else {
                return true;
            }
        }
    }

    /**
     *
     * @return bool
     */
    public function delete(): bool
    {
        $this->status = 0;
        return $this->update();
    }

    /**
     * @return bool
     */
    public function update(): bool
    {
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_categories SET name = :name, parentId = :parentId, position = :position, status = :status WHERE id = :id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':parentId', $this->parentId);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating category -> id:' . $this->id . ' name:' . $this->name . ' parentId:' . $this->parentId . ' position: ' . $this->position . ' status:' . $this->status);
            return true;
        }
    }
}