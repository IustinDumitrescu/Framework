<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 10:52 PM
 */

namespace App\Admin\Config;


use App\Admin\AdminUtils;
use App\Admin\Fields\AdminDashboardField;
use App\Admin\Template\AdminTemplate;
use App\Container;
use App\Controller\Admin\DashboardCrudController;
use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Http\Request;
use Psr\Container\ContainerInterface;

class AdminDashboardConfigurator
{
    public const PageIndex = 'index';

    public const PageNew = 'new';

    public const PageShow = 'show';

    public const PageEdit = 'edit';

    public const PageDelete = 'delete';

    private static array $dashboard_listing = [];

    private array $urls_of_dashboard = [];

    private static ?UserEntity $user;

    private static ?AdminEntity $admin;

    private static Request $request;

    private static Container $container;


    private function __construct()
    {
    }


    /**
     * @param AdminDashboardField $adminDashboardField
     * @return AdminDashboardConfigurator
     */
    public static function configureItems(AdminDashboardField $adminDashboardField): AdminDashboardConfigurator
    {
        self::$dashboard_listing[] = $adminDashboardField->getDashboardItem();
        return new self();
    }

    /**
     * @param UserEntity $userEntity
     * @param AdminEntity $adminEntity
     * @param Request $request
     * @param ContainerInterface $container
     * @return AdminDashboardConfigurator
     */
    public static function configureForThisUser(
        UserEntity $userEntity,
        AdminEntity $adminEntity,
        Request $request,
        ContainerInterface $container
    ): AdminDashboardConfigurator
    {
        self::$user = $userEntity;
        self::$admin = $adminEntity;
        self::$request = $request;
        self::$container = $container;
        return new self();
    }


    private function createUrlForAdmin(): void
    {
        foreach (self::$dashboard_listing[0] as $item) {
            $this->urls_of_dashboard[] =
               DashboardCrudController::rootUrl.'?crudCon='.$item["class"].'&action='.AdminContext::AdminActionIndex.'&signature='.AdminUtils::createSignatureUrl(self::$admin, AdminContext::AdminActionIndex);
        }
    }

    public function getConfiguration(): ?AdminTemplate
    {
        $this->createUrlForAdmin();

        $adminActionTemplate = AdminContext::create(
          self::$request,
          self::$container,
          self::$admin,
          $this->urls_of_dashboard,
          self::$dashboard_listing,
          (new AdminTemplate())
        )->getAction();

        return !$adminActionTemplate ? null : $this->createTemplate($adminActionTemplate);
    }

    /**
     * @param AdminTemplate $adminTemplate
     * @return AdminTemplate
     */
    private function createTemplate(AdminTemplate $adminTemplate): AdminTemplate
    {
        return $adminTemplate->createDashboardTemplate(
            $this->urls_of_dashboard,
            self::$user,
            self::$admin,
            self::$dashboard_listing
        );
    }




}