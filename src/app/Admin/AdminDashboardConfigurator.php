<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 10:52 PM
 */

namespace App\Admin;


use App\Controller\Admin\DashboardCrudController;
use App\Entity\UserEntity;

class AdminDashboardConfigurator
{

    private static array $dashboard_listing = [];

    private array $urls_of_dashboard = [];

    private static ?UserEntity $user;


    private function __construct()
    {
    }

    public static function new(string $className, array $permissions = []): AdminDashboardConfigurator
    {
        self::$dashboard_listing[] = [
            "class" => new $className() ,
            "permissions" => $permissions
        ];

        return new self();
    }


    public static function configureForThisUser(UserEntity $userEntity): AdminDashboardConfigurator
    {
        self::$user = $userEntity;
        return new self();
    }


    /**
     *
     */
    private function createUrlForAdmin(): void
    {
        foreach (self::$dashboard_listing as $item) {
            $this->urls_of_dashboard[] = DashboardCrudController::rootUrl.'?entity='.get_class($item["class"]).'&action=index'.'&signature='.AdminUtils::createSignatureUrl($item);
        }
    }

    public function getConfiguration(): static
    {
        $this->createUrlForAdmin();
        return $this;
    }



}