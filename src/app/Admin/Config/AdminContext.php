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
use App\Controller\Admin\DashboardCrudController;
use App\Entity\AdminEntity;
use App\Http\Request;
use Psr\Container\ContainerInterface;

class AdminContext
{
    public const AdminActionIndex = 'index';

    public const AdminActionNew = 'new';

    public const AdminActionEdit = 'edit';

    public const AdminActionShow = 'show';

    private static ?Request $request;

    private static ?ContainerInterface $container;

    private static ?AdminEntity $admin;

    private static ?array $urlsOfDashboard;

    private static $dashBoardListing;

    private static $adminTemplate;

    private array $signatures = [];

    private  ?string $currentControllerName;

    private $currentController;

    private ?string $currentRequest;

    private ?array $requestParams = [];

    private ?array $adminActions = [];

    private ?array $currentAction;

    private $currentSignature;

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

    public function getAction()
    {
        $requestAction = $this->handleRequest();

        if (!$requestAction) {
           return false;
        }

        if ($this->currentControllerName) {
            $actionsController = $this->configureActionsController();

            if ($actionsController) {

                $fields = $this->currentController->configureFields($this);

                dd($fields);
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

             if (!isset(self::$dashBoardListing[0]) || empty(self::$dashBoardListing[0])) {
                 return false;
             }

             foreach (self::$dashBoardListing[0] as $item) {
                 if (trim($item["class"]) === trim($params["/admin?crudCon"])) {
                     $this->currentControllerName = trim($params["/admin?crudCon"]);
                 }
             }

             if (empty($this->currentControllerName)) {
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
        $controller = self::getContainer()->get($this->currentControllerName);

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




}