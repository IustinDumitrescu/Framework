<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 07-May-22
 * Time: 10:04 PM
 */

namespace App\Admin\Fields;


final class AdminDashboardField
{
    private static $dashboard_item;


    private function __construct()
    {
    }


    public static function new(string $name,string $className, array $permissions = [], string $icon = null )
    {
        self::$dashboard_item[] = [
            "name" => $name,
            "class" => $className,
            "permissions" => $permissions,
            "icon" => $icon
        ];

        return new self();
    }

    /**
     * @return mixed
     */
    public function getDashboardItem()
    {
        return self::$dashboard_item;
    }

}