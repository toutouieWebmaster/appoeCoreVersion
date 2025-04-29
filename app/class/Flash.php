<?php

namespace App;
use JetBrains\PhpStorm\NoReturn;

class Flash
{
    /**
     * @var null
     * Session message
     */
    private static $msg = null;

    /**
     * @var null
     * Session status (danger , success , warning , info, dark, secondary, light)
     */
    private static $status = 'danger';

    /**
     * @var string
     */
    private static $location = WEB_ADMIN_URL;

    /**
     * @return string
     */
    public static function getLocation()
    {
        return self::$location;
    }

    /**
     * @param string $location
     */
    public static function setLocation($location)
    {
        self::$location = $location;
    }

    /**
     * @return mixed
     */
    public static function getMsg()
    {
        return self::$msg;
    }

    /**
     * Initialise a Session message
     *
     * @param $msg
     * @param null $status
     */
    public static function setMsg($msg, $status = null)
    {
        self::$msg = $msg;
        if (!is_null($status)) {
            self::$status = $status;
        }
        self::setFlash();
    }

    /**
     * Set Flash Message
     */
    private static function setFlash()
    {
        $_SESSION['flash_msg'] = self::$msg;
        $_SESSION['flash_status'] = self::$status;
    }

    /**
     * Delete Flash Message
     */
    private static function deleteFlash()
    {
        unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_status']);
    }

    /**
     * Get Flash message
     */
    public static function display()
    {
        if (isset($_SESSION['flash_msg'])) {
            echo $_SESSION['flash_msg'];
        }
        self::deleteFlash();
    }

    /**
     * Get & construct Flash message
     */
    public static function constructAndDisplay()
    {
        if (isset($_SESSION['flash_msg'])) {
            echo '<div class="alert alert-' . $_SESSION['flash_status'] . ' alertFlash" role="alert">' . $_SESSION['flash_msg'] . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
        self::deleteFlash();
    }

    /**
     * HTTP Redirection with Flash message
     *
     * @param $msg
     * @param null $status
     */
    #[NoReturn] public static function redirect($msg, $status = null)
    {
        self::$msg = $msg;
        if (!is_null($status)) {
            self::$status = $status;
        }


        self::setFlash();
        header('location:' . self::$location);
        exit();
    }
}