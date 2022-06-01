<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 11-Apr-22
 * Time: 7:36 PM
 */

namespace App\Utils;


final class Utils
{

    public static function createCsrfToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function createNameSpaceFromDirectory(string $directory): string
    {
        $removeExtension = rtrim($directory,'.php');

        $stringSliced = strstr($removeExtension, 'app');

        $replaceAllSlashes = str_replace('/',"\\", $stringSliced);

        $newNameSpacePathOfClass = ltrim($replaceAllSlashes, 'a');

        return '\\A'.$newNameSpacePathOfClass;
    }

    public static function get_string_diff($old, $new)
    {
        $from_start = strspn($old ^ $new, "\0");

        $from_end = strspn(strrev($old) ^ strrev($new), "\0");

        $old_end = strlen($old) - $from_end;

        return substr($old, $from_start, $old_end - $from_start);
    }

    /**
     * @return mixed|null
     */
    public static function getIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }

}