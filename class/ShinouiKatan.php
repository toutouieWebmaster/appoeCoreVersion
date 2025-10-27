<?php

namespace App;
class ShinouiKatan
{
    private static string $clef = "ApPoE";
    private static string $clef2 = "aPpOe";

    public static function Crypter(string $text): string
    {
        return base64_encode(self::Hide2(self::Hide(base64_encode($text))));
    }

    public static function Decrypter(string $text): false|string
    {
        return base64_decode(self::Show(self::Show2(base64_decode($text))));
    }

    private static function Hide(string $text): string
    {
        return implode(self::$clef, str_split($text));
    }

    private static function Hide2(string $text): string
    {
        return implode(self::$clef2, str_split($text));
    }

    private static function Show(array|string $text): array|string
    {
        return str_replace(self::$clef, '', $text);
    }

    private static function Show2(array|string $text): array|string
    {
        return str_replace(self::$clef2, '', $text);
    }
}