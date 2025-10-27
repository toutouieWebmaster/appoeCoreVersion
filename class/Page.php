<?php

namespace App;

use PDO;

class Page
{
    private string $slug;
    private string $name;
    private $min_role_id;
    private $statut;
    private $parent_id;
    private $exist;

    private $dbh = null;

    public function __construct(string $slug)
    {
        if (is_null($this->dbh)) {
            $this->dbh = DB::connect();
        }

        $this->setSlug($slug);
        $this->getPageInfo();
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
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
     * @return bool
     */
    public function getPageInfo(): bool
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
}