<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 11:03 PM
 */

namespace App\Admin;


class AdminUtils
{
    public static function createSignatureUrl($entity): string
    {
        return md5(get_class($entity["class"]));
    }


}