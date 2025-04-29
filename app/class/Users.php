<?php

namespace App;

use PDO;

class Users
{
    private $id;
    private $login;
    private $password;
    private $role;
    private $email;
    private $nom;
    private $prenom = '';
    private $options = null;
    private $statut = 1;

    private $dbh = null;

    public function __construct($userId = null)
    {
        if (!is_null($userId)) {
            $this->id = $userId;
            $this->show();
        }
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = intval($id);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return strlen($this->role) < 3 ? $this->role : Shinoui::Decrypter($this->role);
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = strlen($role) > 3 ? $role : Shinoui::Crypter($role);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
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


    public function createTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . TABLEPREFIX . 'appoe_users` (
  					`id` INT(11) NOT NULL AUTO_INCREMENT,
                	PRIMARY KEY (`id`),
                	`login` VARCHAR(70) NOT NULL,
                	UNIQUE KEY (`login`),
  					`password` VARCHAR(255) NOT NULL,
  					`role` VARCHAR(255) NOT NULL,
  					`email` VARCHAR(200) NULL DEFAULT NULL,
  					`nom` VARCHAR(100) NOT NULL,
  					`prenom` VARCHAR(100) NULL DEFAULT NULL,
  					`options` TEXT NULL DEFAULT NULL,
  					`statut` TINYINT(1) NOT NULL DEFAULT 1,
                    `created_at` DATE NULL,
                	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=15792;';

        return !DB::exec($sql) ? false : true;
    }

    /**
     * Authenticate User
     * Require email & password
     * @return bool
     */
    public function authUser()
    {
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_users WHERE BINARY login = :login AND statut = TRUE';
        $params = array(':login' => $this->login);
        if ($return = DB::exec($sql, $params)) {

            if ($return->rowCount() == 1) {
                $row = $return->fetch(PDO::FETCH_OBJ);
                if (password_verify($this->password, $row->password)) {
                    if (password_needs_rehash($row->password, PASSWORD_DEFAULT)) {
                        $this->updatePassword();
                    }
                    $this->feed($row);

                    return true;
                } else {
                    return false; // Le mot de passe n'est pas correct;
                }
            } else {
                return false; // L'utilisateur n'existe pas;
            }
        }
        return false;
    }

    /**
     * Get User by Id
     *
     * @return bool
     */
    public function show()
    {
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_users WHERE id = :id';
        $params = array(':id' => $this->id);
        if ($return = DB::exec($sql, $params)) {
            $this->feed($return->fetch(PDO::FETCH_OBJ));
            return true;
        }
        return false;
    }

    /**
     * @param $minStatus
     * @return bool|array
     */
    public function showAll($minStatus = true)
    {

        $sqlStatus = $minStatus ? ' statut >= :statut ' : ' statut = :statut ';
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_users WHERE ' . $sqlStatus . ' ORDER BY statut DESC, created_at ASC';
        $params = array(':statut' => $this->statut);
        if ($return = DB::exec($sql, $params)) {
            return $return->fetchAll(PDO::FETCH_OBJ);
        }
        return false;
    }

    /**
     * Insert User into DataBase
     * @return bool
     */
    public function save()
    {
        $hash_password = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = 'INSERT INTO ' . TABLEPREFIX . 'appoe_users (login, email, password, role,  nom, prenom, options, created_at) 
                    VALUES (:login, :email, :password, :role, :nom, :prenom, :options, CURDATE())';
        $params = array(
            ':login' => $this->login,
            ':email' => $this->email,
            ':password' => $hash_password,
            ':role' => $this->role,
            ':nom' => $this->nom,
            ':prenom' => $this->prenom,
            ':options' => $this->options,
        );
        if ($return = DB::exec($sql, $params)) {
            $lastInsertId = DB::lastInsertId();
            $this->setId($lastInsertId);
            appLog('Creating user -> login: ' . $this->login . ' email: ' . $this->email . ' role: ' . $this->role . ' nom: ' . $this->nom . ' prenom: ' . $this->prenom . ' options: ' . $this->options);
            return true;
        }
        return false;
    }

    public function update()
    {
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_users 
        SET login = :login, email = :email, nom = :nom, prenom = :prenom, role = :role, statut = :statut 
        WHERE id = :id';
        $params = array(
            ':id' => $this->id,
            ':login' => $this->login,
            ':email' => $this->email,
            ':nom' => $this->nom,
            ':prenom' => $this->prenom,
            ':role' => $this->role,
            ':statut' => $this->statut
        );
        if (DB::exec($sql, $params)) {
            appLog('Updating user -> id: ' . $this->id . ' login: ' . $this->login . ' email: ' . $this->email . ' role: ' . $this->role . ' nom: ' . $this->nom . ' prenom: ' . $this->prenom . ' statut: ' . $this->statut);
            return true;
        }
        return false;
    }

    /**
     * @param bool $login
     * if $login is true, ignoring User login from results
     *
     * @return bool
     */
    public function exist($login = false)
    {
        $sql = 'SELECT login FROM ' . TABLEPREFIX . 'appoe_users WHERE BINARY login = :login';
        $params = array(':login' => $this->login);
        if ($return = DB::exec($sql, $params)) {
            $count = $return->rowCount();
            if ($count == 0) {
                return false;
            } else {
                if ($login && $count == 1) {
                    $row = $return->fetch(PDO::FETCH_OBJ);
                    if ($row->login == $this->login) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Update user Password with new hash algorithme
     * @return bool
     */
    public function updatePassword()
    {
        $hash_password = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = 'UPDATE ' . TABLEPREFIX . 'appoe_users SET password = :password WHERE BINARY login = :login';
        $params = array(':password' => $hash_password, ':login' => $this->login);
        if (DB::exec($sql, $params)) {
            appLog('Updating user password -> login: ' . $this->login);
            return true;
        }
        return false;
    }

    public function delete()
    {
        $this->statut = 0;
        if ($this->update()) {
            return true;
        } else {
            return false;
        }
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
}