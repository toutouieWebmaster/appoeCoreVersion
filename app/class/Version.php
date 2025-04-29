<?php

namespace App;
class Version
{
    private static $file;
    private static $date;
    private static $version;
    private static $renew = false;
    private static $data;

    /**
     * App & plugins versions $majour.$minor.$simple (X.X.X)
     */
    private static $major;
    private static $minor;
    private static $simple;

    /**
     * @return mixed
     */
    public static function getFile()
    {
        return self::$file;
    }

    /**
     * @param mixed $file
     */
    public static function setFile($file)
    {
        self::$file = $file;
    }

    /**
     * @return mixed
     */
    public static function getDate()
    {
        return self::$date;
    }

    /**
     *
     */
    public static function initializeDate()
    {
        self::$date = date('Y-m-d H:i');
    }

    /**
     * @return mixed
     */
    public static function getVersion()
    {
        return self::$version;
    }

    /**
     * @param $version
     */
    public static function setVersion($version)
    {
        self::$version = $version;
    }

    /**
     * @return mixed
     */
    public static function getRenew()
    {
        return self::$renew;
    }

    /**
     * @param $renew
     */
    public static function setRenew($renew)
    {
        self::$renew = $renew;
    }

    /**
     * @return mixed
     */
    public static function getData()
    {
        return self::$data;
    }

    /**
     *
     */
    public static function initializeData()
    {
        self::$data = array('date' => self::$date, 'version' => self::$version, 'renew' => self::$renew);
    }

    /**
     *
     */
    public static function show()
    {
        if (!empty(self::$file)) {
            $json_file = file_get_contents(self::$file);

            if ($json_file) {
                $json = json_decode($json_file);

                if ($json_file && $json) {

                    self::$date = $json->date;
                    self::$version = $json->version;
                    self::$renew = $json->renew;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public static function save()
    {
        $json_file = fopen(self::$file, 'w');

        if ($json_file) {
            self::initializeData();

            fwrite($json_file, json_encode(self::$data));
            fclose($json_file);
            return true;
        }

        return false;
    }

    /**
     * @param String $X (maj, min, sim)
     */
    public static function updateVersion($X = 'sim')
    {
        list(self::$major, self::$minor, self::$simple) = explode('.', self::$version);

        if ($X === 'maj') {

            self::$major < 999 ? self::$major += 1 : self::$major = 1;

            self::$simple = 1;
            self::$minor = 1;

        } elseif ($X === 'min') {

            if (self::$minor < 999) {
                self::$minor += 1;

            } else {
                self::$minor = 1;
                self::$major += 1;
            }

            self::$simple = 1;

        } elseif ($X === 'sim') {

            if (self::$simple < 999) {

                self::$simple += 1;

            } else {

                if (self::$minor < 999) {
                    self::$minor += 1;

                } else {
                    self::$major < 999 ? self::$major += 1 : self::$major = 1;
                    self::$minor = 1;
                }

                self::$simple = 1;
            }
        }

        self::$version = self::$major . '.' . self::$minor . '.' . self::$simple;
        self::$renew = true;
    }
}