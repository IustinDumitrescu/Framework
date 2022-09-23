<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 07-May-22
 * Time: 9:12 PM
 */

namespace App\Admin\Template;


use App\Admin\AdminUtils;
use App\Admin\Config\AdminContext;
use App\Controller\Admin\DashboardCrudController;
use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Kernel;
use App\Utils\Utils;

final class AdminTemplate
{
   private ?string $firstNav;

   private ?string $lateralNav;

   private ?string $actionTemplate = null;

   private ?string $template = null;

   public function createDashboardTemplate(
       array $urls,
       UserEntity $userEntity,
       AdminEntity $adminEntity,
       array $listing
   ): self
   {
       $this->createFirstNav($userEntity, $adminEntity);

       $this->createLateralNav($urls, $listing, $adminEntity);

       return $this;
   }

   public function getFirstNav(): ?string
   {
     return $this->firstNav;
   }

   public function getLateralNav(): ?string
   {
       return $this->lateralNav;
   }

   public function getActionTemplate(): ?string
   {
       return  $this->actionTemplate;
   }

   public function setActionTemplateName(?string $template): self
   {
       $this->template = $template;
       return $this;
   }

   public function getActionTemplateName(): ?string
   {
       return $this->template;
   }


   private function createFirstNav(
       UserEntity $userEntity,
       AdminEntity $adminEntity
   ): void
   {

       $rol = strtoupper($adminEntity->getRol());

       $superAdmin = $adminEntity->getSuperAdmin() ? 'SUPER_ADMIN' : '';

       $this->firstNav = "
        <nav class=\"navbar navbar-expand-lg navbar-light bg-light\">
            <div class=\"container-fluid\">
                <h3><a class=\"navbar-brand\" href='/admin'>Mysite</a></h3>
                <ul class=\"navbar-nav flex-row ml-md-auto\">
                        <li class=\"dropdown\">
                            <button class=\"btn btn-secondary dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                                 {$userEntity->getEmail()}
                            </button>
                            <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
                                <p>{$rol}</p>
                                <p>{$superAdmin}</p>
                                <a class=\"dropdown-item\" href=\"/admin/logout\">Logout</a>
                            </div>
                        </li>
                </ul>
            </div>
        </nav> ";
   }

   private function createLateralNav(array $url, array $listing, AdminEntity $adminEntity): void
   {
       $stringLateral = '
            <nav id="sidebarMenu" class="collapse d-lg-block sidebar collapse bg-light">
                      <div class="position-sticky">
                        <div class="list-group list-group-flush mx-3">';


       $i = 0;

       foreach ($listing[0] as $item) {

          if (in_array($adminEntity->getRol(), $item["permissions"], true)) {
              $stringLateral .= "
                      <a href= {$url[$i]} class=\"list-group-item list-group-item-action py-2 ripple bg-light\" aria-current=\"true\">
                        <i class='{$item['icon']}'></i><span>{$item["name"]}</span>
                      </a>";
          } else if ($adminEntity->getSuperAdmin()) {
              $stringLateral .= "
                      <a href= {$url[$i]} class=\"list-group-item list-group-item-action py-2 ripple bg-light\" aria-current=\"true\">
                        <i class='{$item['icon']}'></i><span>{$item["name"]}</span>
                     </a>";
          }

          $i++;
       }
       $stringLateral .= '   
                </div>
              </div>
            </nav>';


       $this->lateralNav = $stringLateral;
   }


   public function createActionTemplate(AdminContext $context): self
   {
        $currentAction = $context->getCurrentAction()->getName();

        $this->setActionTemplateName($context->getCurrentAction()->getTemplate());

        if ($this->template === null) {
           $this->setActionTemplateName("partials/".$currentAction.".php");
        }

        $class = new \ReflectionClass($this);

        $methods = $class->getMethods();

        $hasMethod = false;

        foreach ($methods as $method) {
            if ($method->getName() === $currentAction) {
                $hasMethod = true;
            }
        }

        return $hasMethod ? $this->$currentAction($context) : $this;
   }

   private function index(AdminContext $context): self
   {
      $this->actionTemplate = "<div class='d-flex m-2'>";

      foreach ($context->getActions() as $key =>$action) {
           if ($key !== AdminContext::AdminActionIndex && $action->getGlobal()) {
               $url = DashboardCrudController::rootUrl."?crudCon=".$context->getControllerName().
                   "&action=".$action->getName()."&signature=".AdminUtils::createSignatureUrl($context->getAdmin(),$action->getName());
               $this->actionTemplate .= "<a class='btn btn-primary' href={$url} role='button'><i class='".$action->getIcon()."'></i>".ucfirst($action->getName())."</a>";
           }
      }

      $this->actionTemplate .= "</div> <table class='table table-responsive-xl m-2 p-2'><thead>";

      foreach ($context->getFields() as $field) {
          $this->actionTemplate .= "<th>".$field->getName()."</th>";
      }

      $this->actionTemplate .= "<th></th></thead>";

      foreach ($context->getResponseFromAction() as $entity) {
          $this->actionTemplate .= "<tr>";

          foreach ($context->getFields() as $field) {
            $name = "get".Utils::dashesToCamelCase($field->getId(), true);

            if (is_bool($entity->$name()) ) {
                $item = $entity->$name() == 1 ? "Da" : "Nu";
                $this->actionTemplate .= "<td>" . $item . "</td>";
            } else if (str_contains(strtolower($field->getName()), 'imag')) {
                $this->actionTemplate .= "
                <td>
                        <img src='{$entity->$name()}' width='100px' height='100ox' alt='img_ls'>    
                </td>";
            } else {
                $this->actionTemplate .= "<td>" . substr($entity->$name(), 0, 200) . "</td>";
            }

          }

          $this->actionTemplate .= "<td><div class='d-flex'>";

          foreach ($context->getActions() as $key => $action) {
             if ($key !== AdminContext::AdminActionIndex && !$action->getGlobal()) {
                 $url = DashboardCrudController::rootUrl."?crudCon=".$context->getControllerName().
                     "&action=".$action->getName()."&entityId=".$entity->getId().
                     "&signature=".AdminUtils::createSignatureUrl($context->getAdmin(),$action->getName());

                 $this->actionTemplate .= "<a style='width: 40px; height: 35px' class='btn btn-primary mr-1' href={$url} role='button'><i class='".$action->getIcon()."'></i></a>";
             }
          }

          $this->actionTemplate .= "</div></td></tr>";
      }

      $this->actionTemplate .= "</table>";

      $countOfResults = count($context->getResponseFromAction());

      $nrOfPages = round($countOfResults/20);

      if ($nrOfPages > 1) {

          $this->actionTemplate .= "<div class='pagination d-flex m-3 justify-content-center'>";

          $page = $context->getRequest()->query->get("page");

          if (!$page) {
              $page = 1;
          }

          $z = $page > 1 ? $page - 1 : 1;

          $w = $page >= $nrOfPages ? $page : $page + 1;

          $this->actionTemplate .= "<a href='" . $context->getCurrentRequest() . "&page=$z" . "'>$z</a>";

          for ($i = 0; $i <= $nrOfPages; $i++) {

              if ($i = $page) {
                  $this->actionTemplate .= "<a class='active' href='#'>$i</a>";
              } else {
                  $this->actionTemplate .= "<a href='" . $context->getCurrentRequest() . "&page=$i" . "'>$i</a>";
              }
          }

          $this->actionTemplate .= "<a href='" . $context->getCurrentRequest() . "&page=$w" . "'>$w</a>";

          $this->actionTemplate .= "</div>";
      }

      $this->actionTemplate .= "</div>";
      return $this;

   }

   public function new(AdminContext $context): AdminTemplate
   {
       $response = $context->getResponseFromAction();

       $this->actionTemplate = '<div class="col-xl-9 col-lg-7 col-md-9 col-12 mx-auto">';

       if (!empty($response["flash"])) {

           $flash = $response["flash"];

           if ($flash["value"]) {
               $this->actionTemplate .=
                   " <div class=\"alert alert-success\" role=\"alert\">
                         {$flash["string"]}
                    </div>
                    ";
           } else {
               $this->actionTemplate .=
                   "<div class=\"alert alert-danger\" role=\"alert\">
                     {$flash["string"]}
                    </div>
                    ";
           }
       }

       $urlIndex = DashboardCrudController::rootUrl."?crudCon=".$context->getControllerName().
           "&action=index&signature=".AdminUtils::createSignatureUrl($context->getAdmin(), AdminContext::AdminActionIndex);

       $this->actionTemplate .= " 
        <div class='float-right mb-5'>
                <a class='btn btn-primary mr-1' href={$urlIndex} role='button'><i class='fa-solid fa-list'></i></i> Back to List</a>
        </div>";

       $this->actionTemplate .= "<h4>{$context->getController()->getName()} ".$context->getCurrentAction()->getLabel()." Form</h4>";

       $this->actionTemplate .= $response["formBuilder"]->createView()["form"];

       $this->actionTemplate .= '</div>';

       return $this;
   }

   public function show(AdminContext $context): AdminTemplate
   {
       $this->actionTemplate = '<div class="col-xl-9 col-lg-7 col-md-7 col-9 mx-auto p-5">';

       $this->actionTemplate .= "<h4 class='mb-4'>{$context->getController()->getName() } SHOW ITEM</h4>";

       foreach ($context->getResponseFromAction() as $key => $item) {

           if (!is_bool($item)) {
               $newItem = $item ?: '-';
           } else {
               $newItem = $item ? "DA" : "NU";
           }

           if (str_contains(strtolower($key), 'imag')) {
               $this->actionTemplate .= "
                <div class='d-flex justify-content-between rounded m-1 p-1'>
                        <p style='margin-right: 1em; font-weight: bold'> $key: </p>
                        <img src='{$item}' width='100px' height='100ox' alt='img_ls'>    
                </div>";
           } else {
               $this->actionTemplate .= "
                <div class='d-flex justify-content-between rounded m-1 p-1'>
                        <p style='margin-right: 1em; font-weight: bold'> $key: </p>
                        <div style='font-weight: bold'> {$newItem} </div>    
                </div>";
           }
       }

       $urlEdit= DashboardCrudController::rootUrl."?crudCon=".$context->getControllerName().
           "&action=edit&entityId=".$context->getRequestParams()["entityId"].
           "&signature=".AdminUtils::createSignatureUrl($context->getAdmin(), AdminContext::AdminActionEdit);

       $urlIndex = DashboardCrudController::rootUrl."?crudCon=".$context->getControllerName().
           "&action=index&signature=".AdminUtils::createSignatureUrl($context->getAdmin(), AdminContext::AdminActionIndex);


       $this->actionTemplate .= " 
        <div class='float-right'>
                <a class='btn btn-primary mr-1' href={$urlEdit} role='button'><i class='fas fa-edit m-1'></i> Edit</a>
        </div>";

       $this->actionTemplate .= " 
        <div class='float-left'>
                <a class='btn btn-primary mr-1' href={$urlIndex} role='button'><i class='fa-solid fa-list'></i></i> Back to List</a>
        </div>";

       $this->actionTemplate .= '</div>';
       return $this;
   }

   public function edit(AdminContext $context): AdminTemplate
   {
       return $this->new($context);
   }





}