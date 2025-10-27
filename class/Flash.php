<?php

namespace App;
use JetBrains\PhpStorm\NoReturn;

class Flash
{
    /**
     * @var ?string
     * Session message
     */
    private static ?string $msg = null;

    /**
     * @var string
     * Session status (danger , success , warning , info, dark, secondary, light)
     */
    private static string $status = 'danger';

    /**
     * @var string
     */
    private static string $location = WEB_ADMIN_URL;

    /**
     * @return string
     */
    public static function getLocation(): string
    {
        return self::$location;
    }

    /**
     * @param string $location
     */
    public static function setLocation(string $location): void
    {
        self::$location = $location;
    }

    /**
     * @return ?string
     */
    public static function getMsg(): ?string
    {
        return self::$msg;
    }

    /**
     * Initialise a Session message
     *
     * @param ?string $msg
     * @param ?string $status
     */
    public static function setMsg(?string $msg, ?string $status = null): void
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
    private static function setFlash(): void
    {
        $_SESSION['flash_msg'] = self::$msg;
        $_SESSION['flash_status'] = self::$status;
    }

    /**
     * Delete Flash Message
     */
    private static function deleteFlash(): void
    {
        unset($_SESSION['flash_msg']);
        unset($_SESSION['flash_status']);
    }

    /**
     * Get Flash message
     */
    public static function display(): void
    {
        if (isset($_SESSION['flash_msg'])) {
            echo $_SESSION['flash_msg'];
        }
        self::deleteFlash();
    }

    /**
     * Get & construct Flash message
     */
    public static function constructAndDisplay(): void
    {
        if (isset($_SESSION['flash_msg'])) {
            echo '<div class="alert alert-' . $_SESSION['flash_status'] . ' alertFlash" role="alert">' . $_SESSION['flash_msg'] . ' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        }
        self::deleteFlash();
    }

    /**
     * HTTP Redirection with Flash message
     *
     * @param ?string $msg
     * @param ?string $status
     */
    #[NoReturn] public static function redirect(?string $msg, ?string $status = null): void
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