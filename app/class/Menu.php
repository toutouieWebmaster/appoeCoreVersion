<?php

namespace App;

use PDO;

class Menu
{
    private $id;
    private $slug;
    private $name;
    private $minRoleId;
    private $statut;
    private $parentId;
    private $orderMenu = null;
    private $pluginName = null;

    private $dbh = null;

    public function __construct()
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }
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
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
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
     * @return mixed
     */
    public function getMinRoleId()
    {
        return $this->minRoleId;
    }

    /**
     * @param mixed $minRoleId
     */
    public function setMinRoleId($minRoleId)
    {
        $this->minRoleId = $minRoleId;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getOrderMenu()
    {
        return $this->orderMenu;
    }

    /**
     * @param mixed $orderMenu
     */
    public function setOrderMenu($orderMenu)
    {
        $this->orderMenu = $orderMenu;
    }

    /**
     * @return mixed
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * @param mixed $pluginName
     */
    public function setPluginName($pluginName)
    {
        $this->pluginName = $pluginName;
    }

    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `'.TABLEPREFIX.'appoe_menu` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`slug` VARCHAR(40) NOT NULL,
  					`name` VARCHAR(50) NOT NULL,
  					`min_role_id` INT(11) NOT NULL,
  					`statut` INT(11) NOT NULL,
  					`parent_id` INT(11) NOT NULL,
  					UNIQUE KEY (`slug`, parent_id),
  					`order_menu` INT(11) DEFAULT NULL,
  					`pluginName` VARCHAR(200) DEFAULT NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=30;
				    INSERT INTO `'.TABLEPREFIX.'appoe_menu` (`id`, `slug`, `name`, `min_role_id`, `statut`, `parent_id`, `order_menu`, `pluginName`, `updated_at`) VALUES
                    (11, "index", "Tableau de bord", 1, 1, 10, 1, NULL, NOW()),
                    (12, "users", "Utilisateurs", 1, 1, 10, 99999, NULL, NOW()),
                    (13, "setting", "Réglages", 11, 0, 10, 13, NULL, NOW()),
                    (14, "updateCategories", "Catégories", 11, 1, 10, 2, NULL, NOW()),
                    (15, "updateMedia", "Média", 1, 1, 10, 3, NULL, NOW()),
                    (16, "updatePermissions", "Permissions", 11, 0, 10, 16, NULL, NOW()),
                    (20, "allUsers", "Tous les utilisateurs", 1, 1, 12, 20, NULL, NOW()),
                    (21, "addUser", "Nouvel utilisateur", 2, 1, 12, 21, NULL, NOW()),
                    (22, "updateUser", "Mise à jour de l\'utilisateur", 1, 0, 12, 22, NULL, NOW()),
                    (23, "tools", "Outils", 3, 0, 10, 23, NULL, NOW());';

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        }

        return true;
    }


    public function displayMenuAll($id = '')
    {

        if (empty($id)) {
            $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_menu ORDER BY order_menu ASC, parent_id ASC';
            $stmt = $this->dbh->prepare($sql);
        } else {
            $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_menu WHERE id = :id';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':id', $id);
        }

        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {

            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $data[] = $row;
            }
            if (isset($data)) {
                return $data;
            } else {
                return false;
            }
        }
    }


    public function displayMenu($role, $id = '')
    {
        if (is_numeric($role)) {

            if (!empty($id)) {
                $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_menu WHERE min_role_id <= :role AND statut = 1 AND parent_id = :id ORDER BY order_menu ASC';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(':id', $id);

            } else {
                $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_menu WHERE min_role_id <= :role AND statut = 1 ORDER BY order_menu ASC';
                $stmt = $this->dbh->prepare($sql);
            }
            $stmt->bindParam(':role', $role);
            $stmt->execute();
            $error = $stmt->errorInfo();

            if ($error[0] == '00000') {

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[] = $row;
                }

                if (isset($data)) {
                    return $data;
                }
            }
        }
        return false;
    }


    public function displayMenuBySlug($slug)
    {

        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_menu WHERE slug = :slug';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':slug', $slug);

        $stmt->execute();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            return $stmt->fetch(PDO::FETCH_OBJ);
        }
    }

    public function insertMenu()
    {

        /*if ($this->parentId == 10) {
            $this->orderMenu = $this->ordonnerMenu();
        }*/

        $sql = 'INSERT INTO '.TABLEPREFIX.'appoe_menu (id, slug, name, min_role_id, statut, parent_id, order_menu, pluginName) 
        VALUES (:id, :slug, :name, :min_role_id, :statut, :parent_id, :order_menu, :pluginName)';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':min_role_id', $this->minRoleId);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':parent_id', $this->parentId);
        $stmt->bindParam(':order_menu', $this->orderMenu);
        $stmt->bindParam(':pluginName', $this->pluginName);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            showDebugData($error); //return false;
        } else {
            appLog('Creating menu -> id: ' . $this->id . ' slug: ' . $this->slug . ' name: ' . $this->name . ' 
            min role id: ' . $this->minRoleId . ' statut: ' . $this->statut . ' parent id: ' . $this->parentId . ' 
            order: ' . $this->orderMenu . ' plugin: ' . $this->pluginName);
            return true;
        }

    }

    public function updateMenu()
    {

        $sql = 'UPDATE '.TABLEPREFIX.'appoe_menu 
        SET name = :name, slug = :slug, min_role_id = :min_role_id, statut = :statut, parent_id = :parent_id, order_menu = :order_menu, pluginName = :pluginName 
        WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':min_role_id', $this->minRoleId);
        $stmt->bindParam(':statut', $this->statut);
        $stmt->bindParam(':parent_id', $this->parentId);
        $stmt->bindParam(':order_menu', $this->orderMenu);
        $stmt->bindParam(':pluginName', $this->pluginName);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            showDebugData($error); //return false;
        } else {
            appLog('Updating menu -> id: ' . $this->id . ' slug: ' . $this->slug . ' name: ' . $this->name . ' 
            min role id: ' . $this->minRoleId . ' statut: ' . $this->statut . ' parent id: ' . $this->parentId . ' 
            order: ' . $this->orderMenu . ' plugin: ' . $this->pluginName);
            return true;
        }

    }


    public function deleteMenu($id)
    {
        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_menu WHERE id = :id';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete menu -> id: ' . $id);
            return true;
        }
    }

    public function deletePluginMenu($pluginName)
    {

        $sql = 'DELETE FROM '.TABLEPREFIX.'appoe_menu WHERE pluginName = :pluginName';

        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':pluginName', $pluginName);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            appLog('Delete menu -> plugin name: ' . $pluginName);

            return true;
        }
    }


    public function checkUserPermission($user_session_role, $slug)
    {
        $sql = 'SELECT slug, min_role_id FROM '.TABLEPREFIX.'appoe_menu WHERE slug = :slug';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();

        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();
        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($row['min_role_id'] <= $user_session_role) {
                    return true;
                }
            }
            return false;
        }
    }

    public function ordonnerMenu()
    {
        $num = 3;
        $sql = 'SELECT order_menu FROM '.TABLEPREFIX.'appoe_menu WHERE parent_id = 10 ORDER BY order_menu ASC';
        $stmt = $this->dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row['order_menu'] >= $num) {
                    $num = $row['order_menu'] + 1;
                }
            }

            return $num;
        }
    }

    public function cleanText($filename)
    {

        $special = array(
            ' ', '\'', '"', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ',
            'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç',
            'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý'
        );

        $normal = array(
            '-', '-', '-', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n',
            'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'C',
            'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y'
        );

        $filename = str_replace($special, $normal, $filename);

        return strtolower($filename);
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