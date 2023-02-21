<?php

namespace App;

use PDO;

class Page
{
    private $slug;
    private $name;
    private $min_role_id;
    private $statut;
    private $parent_id;
    private $exist;

    private $dbh = null;

    public function __construct($slug)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        $this->setSlug($slug);
        $this->getPageInfo();
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
        return $this->min_role_id;
    }

    /**
     * @param mixed $min_role_id
     */
    public function setMinRoleId($min_role_id)
    {
        $this->min_role_id = $min_role_id;
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
        return $this->parent_id;
    }

    /**
     * @param mixed $parent_id
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;
    }

    /**
     * @return mixed
     */
    public function isExist()
    {
        return $this->exist;
    }

    /**
     * @param mixed $exist
     */
    public function setExist($exist)
    {
        $this->exist = $exist;
    }

    /**
     * Get page informations
     * @return array|bool
     */
    public function getPageInfo()
    {
        $sql = 'SELECT * FROM '.TABLEPREFIX.'appoe_menu WHERE slug = :slug';
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':slug', $this->slug);
        $stmt->execute();
        $count = $stmt->rowCount();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        } else {
            if ($count == 1) {
                $this->exist = true;
                $row = $stmt->fetch(PDO::FETCH_OBJ);
                $this->feed($row);
                return true;
            } else {
                $this->exist = false;
                return false;
            }
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