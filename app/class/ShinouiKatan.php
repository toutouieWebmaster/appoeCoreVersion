<?php

namespace App;
class ShinouiKatan
{
    private static $clef = "ApPoE";
    private static $clef2 = "aPpOe";

    public static function Crypter($text)
    {
        return base64_encode(self::Hide2(self::Hide(base64_encode($text))));
    }

    public static function Decrypter($text)
    {
        return base64_decode(self::Show(self::Show2(base64_decode($text))));
    }

    private static function Hide($text)
    {
        return implode(self::$clef, str_split($text));
    }

    private static function Hide2($text)
    {
        return implode(self::$clef2, str_split($text));
    }

    private static function Show($text)
    {
        return str_replace(self::$clef, '', $text);
    }

    private static function Show2($text)
    {
        return str_replace(self::$clef2, '', $text);
    }
}