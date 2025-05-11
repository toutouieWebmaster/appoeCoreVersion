<?php

namespace App;

use PDO;

class CategoryRelations
{
    private int $id;
    private $type;
    private $typeId;
    private $categoryId;

    private $data = null;
    private $dbh = null;

    public function __construct($type = null, $typeId = null)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        if (!is_null($type) && !is_null($typeId)) {
            $this->type = $type;
            $this->typeId = $typeId;
            $this->show();
        }
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
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
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param null $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * @param mixed $categoryId
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param null $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_categoryRelations` (
  					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                    `type` VARCHAR(150) NOT NULL,
                    `typeId` INT(11) UNSIGNED NOT NULL,
                    `categoryId` INT(11) UNSIGNED NOT NULL,
                    UNIQUE (`type`, `typeId`, `categoryId`),
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;ALTER TABLE `' . TABLEPREFIX . 'appoe_categoryRelations` AUTO_INCREMENT = 11;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }

    /**
     * @return array|bool
     */
    public function show()
    {

        $sql = 'SELECT CR.*, C.name AS name, C.parentId AS parentId FROM ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        RIGHT JOIN ' . TABLEPREFIX . 'appoe_categories AS C 
        ON(CR.categoryId = C.id)
        WHERE CR.type = :type AND CR.typeId = :typeId AND C.status = 1 ORDER BY C.name ASC';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->execute();

        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            $this->data = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $this->data;
        }
    }

    /**
     * @return array|bool
     */
    public function showAll()
    {

        $sql = 'SELECT CR.id, CR.type, CR.typeId, CR.categoryId, ART.name, ART.statut 
        FROM ' . TABLEPREFIX . 'appoe_categoryRelations AS CR 
        RIGHT JOIN ' . TABLEPREFIX . 'appoe_plugin_itemGlue_articles AS ART 
        ON(CR.typeId = ART.id) 
        RIGHT JOIN ' . TABLEPREFIX . 'appoe_categories AS AC
        ON(AC.id = CR.categoryId)
        WHERE CR.type = "ITEMGLUE" AND ART.statut > 0 AND AC.status = 1
        ORDER BY ART.statut DESC, ART.updated_at DESC';
        $stmt = $this->dbh->prepare($sql);

        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        }
    }

    /**
     *
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_categoryRelations (type, typeId, categoryId) VALUES(:type, :typeId, :categoryId)';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':categoryId', $this->categoryId);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Creating category relation -> type: ' . $this->type . ' typeId:' . $this->typeId . ' categoryId:' . $this->categoryId);
            return true;
        }
    }

    /**
     *
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_categoryRelations SET type = :type, typeId = :typeId, categoryId = :categoryId WHERE id = :id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':categoryId', $this->categoryId);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating category relation -> id: ' . $this->id . ' type: ' . $this->type . ' typeId:' . $this->typeId . ' categoryId:' . $this->categoryId);
            return true;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_categoryRelations WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Deleting category relation -> id: ' . $this->id);
            return true;
        }
    }

    /**
     * Feed class attributs
     * @param $data
     */
    public function feed($data)
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
}