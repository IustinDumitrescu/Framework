<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 11:03 PM
 */

namespace App\Admin;


use App\Entity\AdminEntity;

final class AdminUtils
{
    public static function createSignatureUrl(AdminEntity $admin, string $action): string
    {
        return md5($admin->getId().$action);
    }


}