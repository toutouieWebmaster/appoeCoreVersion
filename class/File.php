<?php

namespace App;

use PDO;

class File
{
    protected $id;
    protected $userId;
    protected $type;
    protected $typeId;
    protected $name;
    protected ?string $link = null;
    protected int|string $position = 999;
    protected $options = null;

    protected string $title;
    protected string $description;
    protected $categoryId;
    protected string $categoryName;
    protected string $lang = LANG;
    protected $maxSize = 5621440;
    protected string $filePath = FILE_DIR_PATH;
    protected string $fileUrl = WEB_DIR_INCLUDE;
    protected $uploadFiles = null;
    protected ?PDO $dbh = null;

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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
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
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * @param mixed $typeId
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param null $link
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * @return null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param null $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param null $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
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
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * @param mixed $categoryName
     */
    public function setCategoryName($categoryName)
    {
        $this->categoryName = $categoryName;
    }

    /**
     * @return bool|mixed|string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param bool|mixed|string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return int
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param int $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     */
    public function setFilePath(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return array|null
     */
    public function getUploadFiles()
    {
        return $this->uploadFiles;
    }

    /**
     * @param array|null $uploadFiles
     */
    public function setUploadFiles(array $uploadFiles)
    {
        $this->uploadFiles = $uploadFiles;
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_files` (
  					`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`userId` INT(11) UNSIGNED NOT NULL,
  					`type` VARCHAR(55) NOT NULL,
  					`typeId` INT(11) UNSIGNED NOT NULL,
  					`name` VARCHAR(255) NOT NULL,
  					UNIQUE (`type`, `typeId`, `name`),
  					`link` VARCHAR(255) NULL DEFAULT NULL,
  					`position` INT(11) NOT NULL DEFAULT "999",
  					`options` TEXT NULL DEFAULT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;ALTER TABLE `' . TABLEPREFIX . 'appoe_files` AUTO_INCREMENT = 11;';

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
    public function show()
    {

        $sql = 'SELECT DISTINCT F.*,
        (SELECT cc1.title FROM ' . TABLEPREFIX . 'appoe_filesContent AS cc1 WHERE cc1.fileId = F.id AND cc1.lang = :lang) AS title,
        (SELECT cc2.description FROM ' . TABLEPREFIX . 'appoe_filesContent AS cc2 WHERE cc2.fileId = F.id AND cc2.lang = :lang) AS description,
        C.id AS categoryId, C.name AS categoryName
        FROM ' . TABLEPREFIX . 'appoe_files AS F 
        LEFT JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = F.typeId)
        WHERE F.id = :id
        GROUP BY F.id ORDER BY F.position ASC, F.updated_at DESC';

        $return = DB::exec($sql, [':id' => $this->id, ':lang' => $this->lang]);

        if ($return) {

            $row = $return->fetch(PDO::FETCH_OBJ);
            $this->feed($row);

            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function exist()
    {

        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_files WHERE type = :type AND typeId = :typeId AND name = :name';

        if ($return = DB::exec($sql, [':type' => $this->type, ':typeId' => $this->typeId, ':name' => $this->name])) {
            if ($return->rowCount() != 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Feed class attributs
     *
     * @param $data
     */
    public function feed($data)
    {
        foreach ($data as $attribut => $value) {
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $attribut)));

            if (is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return array|bool
     */
    public function showFiles()
    {
        $sql = 'SELECT DISTINCT F.*,
        (SELECT cc1.title FROM ' . TABLEPREFIX . 'appoe_filesContent AS cc1 WHERE cc1.fileId = F.id AND cc1.lang = :lang) AS title,
        (SELECT cc2.description FROM ' . TABLEPREFIX . 'appoe_filesContent AS cc2 WHERE cc2.fileId = F.id AND cc2.lang = :lang) AS description,
        C.id AS categoryId, C.name AS categoryName
        FROM ' . TABLEPREFIX . 'appoe_files AS F 
        LEFT JOIN ' . TABLEPREFIX . 'appoe_categories AS C
        ON(C.id = F.typeId)
        WHERE F.type = :type AND F.typeId = :typeId
        GROUP BY F.id ORDER BY F.position ASC, F.updated_at DESC';

        $return = DB::exec($sql, [':type' => $this->type, ':typeId' => $this->typeId, ':lang' => $this->lang]);

        if ($return) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * @return array|bool
     */
    public function showAll()
    {
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_files GROUP BY name ORDER BY name ASC';
        $stmt = $this->dbh->prepare($sql);

        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            $allFiles = $stmt->fetchAll(PDO::FETCH_OBJ);

            if ($allFiles) {

                $FileContent = new FileContent();

                foreach ($allFiles as &$file) {

                    $file->title = null;
                    $file->description = null;

                    $FileContent->setFileId($file->id);
                    $FileContent->setLang($this->lang);
                    if ($FileContent->showByFile()) {
                        $file->title = $FileContent->getTitle();
                        $file->description = $FileContent->getDescription();
                    }
                }
                return $allFiles;
            }
            return false;
        }
    }

    /**
     *
     * @return bool
     */
    public function update()
    {
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_files 
        SET userId = :userId, typeId = :typeId, link = :link, position = :position, options = :options 
        WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':link', $this->link);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':options', $this->options);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating file on db -> id: ' . $this->id . ' typeId:' . $this->typeId . ' link:' . $this->link . ' position:' . $this->position . ' options:' . $this->options);
            return true;
        }
    }

    /**
     *
     * @return bool
     */
    public function changePosition()
    {
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_files SET position = :position WHERE id = :id';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':position', $this->position);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Updating file on db -> id: ' . $this->id . ' position:' . $this->position);
            return true;
        }
    }

    /**
     *
     * @param bool $saveToDb
     *
     * @return array
     */
    public function upload($saveToDb = true)
    {
        $returnArr = array(
            'filename' => [],
            'countUpload' => '',
            'countDbSaved' => '',
            'errors' => ''
        );
        $uploadFilesCounter = 0;
        $dbSaveFilesCounter = 0;
        $files = $this->uploadFiles;
        $fileCount = !empty($files['name'][0]) ? count($files['name']) : 0;

        for ($i = 0; $i < $fileCount; $i++) {

            if (!empty($files['name'][$i])) {

                $error = $files['error'][$i];
                if ($error == 0) {

                    $formUploadToDB = true;
                    $tmp_name = $files['tmp_name'][$i];
                    $filename = $this->cleanText($files['name'][$i]);
                    $type = $files['type'][$i];
                    $size = $files['size'][$i];
                    if ($size <= $this->maxSize) {

                        if ($this->authorizedMediaFormat($type)) {

                            if (file_exists($this->filePath . $filename)) {
                                if (unlink($this->filePath . $filename)) {
                                    deleteThumb($filename, 370);
                                    purgeVarnishCache($this->fileUrl . $filename);
                                    appLog('Delete file to overwrite it -> name: ' . $filename);
                                }
                                $formUploadToDB = false;
                            }

                            if (move_uploaded_file($tmp_name, $this->filePath . $filename) === false) {
                                $returnArr['errors'] .= sprintf(trans('Le fichier %s n\'a pas pu être enregistré.'), $filename) . '<br>';
                                continue;
                            }

                            if($this->isFileCorrupted($this->filePath . $filename)){
                                unlink($this->filePath . $filename);
                                $returnArr['errors'] .= sprintf(trans('Le fichier %s semble être corrompu et a été supprimé'), $filename) . '<br>';
                                continue;
                            }

                            $uploadFilesCounter++;
                            appLog('Upload file -> name: ' . $filename);
                            $returnArr['filename'][] = $filename;

                            if ($saveToDb && $formUploadToDB) {

                                $this->name = $filename;
                                if ($this->save()) {
                                    $dbSaveFilesCounter++;
                                }
                            }

                        } else {
                            $returnArr['errors'] .= trans('Le format du fichier') . ' ' . $filename . ' ' . trans('n\'est pas reconnu.') . '<br>';
                        }
                    } else {
                        $returnArr['errors'] .= trans('Le fichier') . ' ' . $filename . ' ' . trans('dépasse le poids autorisé.') . '<br>';
                    }
                }
            }
        }
        $returnArr['countUpload'] = $uploadFilesCounter . '/' . $fileCount;
        $returnArr['countDbSaved'] = $dbSaveFilesCounter . '/' . $fileCount;
        return $returnArr;
    }

    /**
     *
     * @param bool $saveToDb
     *
     * @return array
     */
    public function uploadOneFile($saveToDb = true)
    {

        $returnArr = array(
            'filename' => '',
            'errors' => ''
        );

        $file = $this->uploadFiles;

        if (!empty($file['name'])) {

            if ($file['error'] == 0) {

                $tmp_name = $file['tmp_name'];
                $filename = $this->cleanText($file['name']);
                $type = $file['type'];
                $size = $file['size'];

                $returnArr['filename'] = $filename;

                if ($size <= $this->maxSize) {

                    if ($this->authorizedMediaFormat($type)) {

                        if (move_uploaded_file($tmp_name, $this->filePath . $filename) === false) {

                            $returnArr['errors'] = trans('Impossible de charger le fichier.');
                            return $returnArr;
                        }

                        if($this->isFileCorrupted($this->filePath . $filename)){
                            unlink($this->filePath . $filename);
                            $returnArr['errors'] .= sprintf(trans('Le fichier %s semble être corrompu et a été supprimé'), $filename) . '<br>';
                            return $returnArr;
                        }

                        appLog('Upload file -> name: ' . $filename);

                        if ($saveToDb) {

                            $this->name = $filename;

                            if (!$this->save()) {
                                $returnArr['errors'] = trans('Impossible d\'enregistrer le fichier.');

                                return $returnArr;
                            }
                        }

                    } else {
                        $returnArr['errors'] = trans('Le format du fichier n\'est pas reconnu.');
                    }
                } else {
                    $returnArr['errors'] = trans('Le fichier dépasse le poids autorisé.');
                }
            } else {
                $returnArr['errors'] = $file['error'];
            }
        }


        return $returnArr;
    }

    /**
     * @param $file
     * @return bool
     */
    public function isFileCorrupted($file){

        $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        $integratedFile = true;

        if (isImage($file)) {

            $mime = exif_imagetype($file);

            if (($ext == "JPG" or $ext == "JPEG") && $mime == IMAGETYPE_JPEG) {
                $integratedFile = @imagecreatefromjpeg($file);
            } elseif ($ext == "PNG" && $mime == IMAGETYPE_PNG) {
                $integratedFile = @imagecreatefrompng($file);
            } elseif ($ext == "GIF" && $mime == IMAGETYPE_GIF) {
                $integratedFile = @imagecreatefromgif($file);
            } elseif ($ext == "WEBP" && $mime == IMAGETYPE_WEBP && function_exists('imagecreatefromwebp')) {
                $integratedFile = @imagecreatefromwebp($file);
            } elseif ($ext == "SVG" && isSvg($file)) {
                $integratedFile = true;
            } else {
                $integratedFile = false;
            }
        }

        return !$integratedFile;
    }

    /**
     * @param $filename
     *
     * @return string
     */
    public function cleanText($filename)
    {

        $special = array(
            ' ', '&', '\'', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ',
            'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï',
            'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý',
            '#', '{', '}', '(', ')', '[', ']', '|', ';', ':', '`', '\\', '/', '^', '@', '°', '=', '+', '*', '?', '!', '§', '²', '%', 'µ', '$', '£', '¤', '¨'
        );

        $normal = array(
            '-', '-', '-', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o',
            'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I',
            'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y',
            '-', '-', '-', '-', '-', '-', '-', '-', '-', '-', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
        );

        return str_replace($special, $normal, $filename);
    }

    /**
     * @param $format
     * @return bool
     */
    public function authorizedMediaFormat($format)
    {
        if (is_string($format)) {

            $authorizedFormat = array(
                'image/jpeg', 'image/png', 'image/gif',
                'image/jpg', 'image/svg+xml', 'image/tiff',
                'image/x-icon', 'image/webp',
                'application/pdf', 'application/vnd.ms-word',
                'application/vnd.ms-powerpoint', 'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.presentation',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.text',
                'text/csv', 'application/msword', 'application/json',
                'audio/aac', 'audio/x-mpegurl', 'audio/m4a',
                'audio/x-midi', 'audio/x-ms-wma', 'audio/mpeg',
                'audio/ogg', 'audio/wav', 'audio/x-wav',
                'audio/webm', 'audio/3gpp',
                'video/x-msvideo', 'video/mpeg', 'video/ogg',
                'video/webm', 'video/3gpp', 'video/mp4'
            );

            return in_array($format, $authorizedFormat);
        }
        return false;
    }

    /**
     *
     * @return bool
     */
    public function save()
    {
        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_files (userId, type, typeId, name, updated_at) 
        VALUES(:userId, :type, :typeId, :name, NOW())';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':userId', $this->userId);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':typeId', $this->typeId);
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();

        $this->id = $this->dbh->lastInsertId();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Creating file on db -> type: ' . $this->type . ' typeId:' . $this->typeId . ' name:' . $this->name);
            return true;
        }
    }

    /**
     * Rename file
     * @param $oldName
     * @return bool
     */
    public function rename($oldName)
    {

        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_files SET name = :name WHERE name = :oldName';

        $stmt = DB::exec($sql, [':name' => $this->name, ':oldName' => $oldName]);

        if ($stmt) {
            appLog('Rename file on db -> oldName: ' . $oldName . ' newName: ' . $this->name);
            return true;
        }
        return false;
    }

    /**
     * @return bool|mixed
     */
    public function deleteFileByPath()
    {
        $path_file = $this->filePath . $this->name;

        if ($this->countFile() < 2) {
            if (file_exists($path_file)) {
                if (!unlink($path_file)) {
                    return false;
                }
            }
        } else {
            return trans('Ce fichier est rattaché à plusieurs données');
        }
        appLog('Delete file -> name: ' . $this->name);
        return true;
    }

    /**
     * @param bool $all
     * @return bool
     */
    public function countFile($all = false)
    {
        $sql = (!$all) ? 'SELECT * FROM ' . TABLEPREFIX . 'appoe_files WHERE name = :name' : 'SELECT * FROM ' . TABLEPREFIX . 'appoe_files WHERE type = "MEDIA"';
        $stmt = $this->dbh->prepare($sql);

        if (!$all) {
            $stmt->bindParam(':name', $this->name);
        }

        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->rowCount();
        }
    }

    /**
     * @return bool
     */
    public function deleteFileByName()
    {

        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_files WHERE name = :name';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':name', $this->name);
        $stmt->execute();

        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete file on db -> name: ' . $this->name);
            return true;
        }

    }

    /**
     *
     * @return bool
     */
    public function delete()
    {
        $this->deleteFileByPath();

        $sql = 'DELETE FROM ' . TABLEPREFIX . 'appoe_files WHERE id = :id;
                DELETE FROM ' . TABLEPREFIX . 'appoe_files_content WHERE fileId = :id;';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete file on db -> id: ' . $this->id);
            return true;
        }
    }
}