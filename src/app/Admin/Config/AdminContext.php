<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 28-May-22
 * Time: 7:01 PM
 */

namespace App\Admin\Config;


use App\Admin\Action\AdminAction;
use App\Admin\AdminUtils;
use App\Admin\Template\AdminTemplate;
use App\Controller\Admin\AbstractCrudController;
use App\Controller\Admin\AdminCrudController;
use App\Controller\Admin\DashboardCrudController;
use App\Controller\Admin\NewsletterCategoryCrudController;
use App\Entity\AdminEntity;
use App\Http\Request;
use Psr\Container\ContainerInterface;

class AdminContext
{
    public const AdminActionIndex = 'index';

    public const AdminActionNew = 'new';

    public const AdminActionEdit = 'edit';

    public const AdminActionShow = 'show';

    public const AdminActionDelete = 'delete';

    private static ?Request $request;

    private static ?ContainerInterface $container;

    private static ?AdminEntity $admin;

    private static ?array $urlsOfDashboard;

    private static $dashBoardListing;

    private static AdminTemplate $adminTemplate;

    private array $signatures = [];

    private ?string $controllerName = null;

    private object $currentController;

    private ?string $currentRequest;

    private array $requestParams = [];

    private array $adminActions = [];

    private ?AdminAction $currentAction;

    private ?string $currentSignature;

    private array $fields = [];

    private array $responseFromAction = [];

    private function __construct()
    {
    }

    public static function create(
        Request $request,
        ContainerInterface $container,
        AdminEntity $admin,
        array $urlsOfDashboard,
        array $dashBoardListing,
        AdminTemplate $adminTemplate
    ): AdminContext
    {
        self::$request = $request;
        self::$container = $container;
        self::$admin = $admin;
        self::$urlsOfDashboard = $urlsOfDashboard;
        self::$dashBoardListing = $dashBoardListing;
        self::$adminTemplate = $adminTemplate;

        return new self();
    }

    public function getAction(): bool|AdminTemplate
    {
        $requestAction = $this->handleRequest();

        if (!$requestAction) {
           return false;
        }

        if ($this->controllerName) {
            $actionsController = $this->configureActionsController();

            if ($actionsController) {
                $isPermitted = false;
                foreach (self::$dashBoardListing[0] as $list) {
                    if($list["class"] === $this->controllerName
                        && (in_array(self::$admin->getRol(), $list["permissions"], true)
                            || self::$admin->getSuperAdmin())) {
                        $isPermitted = true;
                    }
                }
                if (!$isPermitted) {
                    return false;
                }
                if($this->currentAction->getName() !== self::AdminActionDelete) {
                    $this->fields = $this->currentController->configureFields($this);
                }

                $action = $this->currentAction->getName();

                $this->responseFromAction = $this->currentController->$action($this);

                return self::$adminTemplate->createActionTemplate($this);
            }
            return false;
        }
        return self::$adminTemplate;
    }

    private function handleRequest(): bool
    {
         $this->currentRequest = trim(self::getRequest()->getRequestedUri());

         parse_str($this->currentRequest, $params);

         $this->requestParams = $params;

         if (isset($params["/admin?crudCon"], $params["action"], $params["signature"])) {

             foreach (self::$dashBoardListing[0] as $list) {

                 if ($params["/admin?crudCon"] === $list["class"]) {

                     $permissions = $list["permissions"];

                     $rol = $this->getAdmin()->getRol();

                     $superAdmin = $this->getAdmin()->getSuperAdmin() ? "SUPER-ADMIN" : "not";

                     if (!in_array($rol, $permissions, true) && !in_array($superAdmin, $permissions, true)) {
                         return false;
                     }
                 }
             }

             if (!isset(self::$dashBoardListing[0]) || empty(self::$dashBoardListing[0])) {
                 return false;
             }

             foreach (self::$dashBoardListing[0] as $item) {
                 if (trim($item["class"]) === trim($params["/admin?crudCon"])) {
                     $this->controllerName = trim($params["/admin?crudCon"]);
                 }
             }

             if ( ($params["action"] === self::AdminActionEdit || $params["action"] === self::AdminActionShow)
                 && (empty($params["entityId"]))) {
                 return  false;
             }

             if (empty($this->controllerName)) {
                 return false;
             }

             return true;
         }

        return $this->currentRequest === DashboardCrudController::rootUrl;
    }



    private function createSignatureForAction(AdminEntity $adminEntity, string $action): void
    {
        $this->signatures[$action] = AdminUtils::createSignatureUrl(
            $adminEntity,
            $action
        );
    }

    private function configureActionsController(): bool
    {
        $controller = self::getContainer()->get($this->controllerName);

        $this->currentController = $controller;

        $actions = $controller->configureActions();

        foreach ($actions as $action) {
            if ($this->requestParams["action"] === $action->getName()) {
                $this->currentAction = $action;
            }

            $this->adminActions[$action->getName()] = $action;

            $this->createSignatureForAction(self::$admin, $action->getName());

            if ($this->requestParams["signature"] === $this->signatures[$action->getName()]) {
                $this->currentSignature = $this->signatures[$action->getName()];
            }
        }

        if (empty($this->currentAction)) {
            return false;
        }

        if (empty($this->currentSignature)) {
            return false;
        }


        return true;
    }

    public function getRequestParams(): array
    {
        return $this->requestParams;
    }

    public static function getRequest(): ?Request
    {
        return self::$request;
    }

    public static function getContainer(): ?ContainerInterface
    {
        return self::$container;
    }

    public function getAdmin(): ?AdminEntity
    {
        return self::$admin;
    }

    public function getActions(): array
    {
        return $this->adminActions;
    }

    public function getCurrentAction(): ?AdminAction
    {
        return $this->currentAction;
    }

    public function getCurrentRequest(): ?string
    {
        return $this->currentRequest;
    }

    public function getFields():?array
    {
        return $this->fields;
    }

    public function getController(): object
    {
        return $this->currentController;
    }

    public function getControllerName(): ?string
    {
        return $this->controllerName;
    }

    public function getResponseFromAction(): array
    {
        return $this->responseFromAction;
    }
    public function createSignatureUrl(AdminEntity $admin,string $action ): string
    {
        return AdminUtils::createSignatureUrl($admin, $action);
    }




}