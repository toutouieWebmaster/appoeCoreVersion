<?php

namespace App;

use PDO;

class FileContent
{
    protected $id;
    protected $fileId;
    protected $title;
    protected $description = null;
    protected $lang = APP_LANG;
    protected $userId;
    protected $dbh = null;

    public function __construct()
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        $this->userId = getUserIdSession();
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
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @param mixed $fileId
     */
    public function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param mixed $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return bool
     */
    public function isUserId()
    {
        return $this->userId;
    }

    /**
     * @param bool $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_filesContent` (
  					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
  					`fileId` INT(11) UNSIGNED NOT NULL,
  					`title` VARCHAR(255) NOT NULL,
  					`description` TEXT NULL DEFAULT NULL,
  					`lang` VARCHAR(10) NOT NULL,
  					UNIQUE (`fileId`, `lang`),
  					`userId` int(11) UNSIGNED NOT NULL,
                    `created_at` date NOT NULL,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function show(): bool
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_filesContent WHERE id = :id';

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
     * @return bool
     */
    public function showByFile(): bool
    {
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_filesContent WHERE fileId = :fileId AND lang = :lang';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':fileId', $this->fileId);
        $stmt->bindParam(':lang', $this->lang);

        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);

                return true;
            }
            return false;
        }
    }

    /**
     *
     * @return bool
     */
    public function save(): bool
    {
        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_filesContent (fileId, title, description, lang, userId, created_at) 
            VALUES (:fileId, :title, :description, :lang, :userId, CURDATE())';

        $params = [
            ':fileId' => $this->fileId,
            ':title' => $this->title,
            ':description' => $this->description,
            ':lang' => $this->lang,
            ':userId' => $this->userId
        ];

        $stmt = DB::exec($sql, $params);

        if ($stmt === false) {
            return false;
        }

        $this->id = DB::lastInsertId();

        $error = $stmt->errorInfo();

        if ($error[0] !== '00000') {
            return false;
        }

        appLog('Creating file content -> fileId: ' . $this->fileId . ' title: ' . $this->title . ' description: ' . $this->description . ' lang: ' . $this->lang);
        return true;
    }


    /**
     *
     * @return bool
     */
    public function update(): bool
    {
        $sql = 'UPDATE '.TABLEPREFIX.'appoe_filesContent 
        SET fileId = :fileId, title = :title, description = :description, lang = :lang, userId = :userId 
        WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':fileId', $this->fileId);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':lang', $this->lang);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating file content -> id: ' . $this->id . ' fileId: ' . $this->fileId . ' title: ' . $this->title . ' description: ' . $this->description . ' lang: ' . $this->lang);
            return true;
        }
    }

    /**
     *
     * @return bool
     */
    public function delete(): bool
    {

        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_filesContent WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Deleting file content -> id: ' . $this->id);
            return true;
        }
    }

    /**
     * Feed class attributs
     *
     * @param $data
     */
    public function feed($data): void
    {
        foreach ($data as $attribut => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }
}