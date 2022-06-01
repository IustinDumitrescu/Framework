<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 07-May-22
 * Time: 9:12 PM
 */

namespace App\Admin\Template;


use App\Entity\AdminEntity;
use App\Entity\UserEntity;

final class AdminTemplate
{
   private $firstNav;

   private $lateralNav;

   public function createDashboardTemplate(array $urls, UserEntity $userEntity, AdminEntity $adminEntity, array $listing)
   {
       $this->createFirstNav($userEntity, $adminEntity);

       $this->createLateralNav($urls, $listing, $adminEntity);

       return $this;
   }

    /**
     * @return mixed
     */
    public function getFirstNav()
   {
       return $this->firstNav;
   }

   public function getLateralNav()
   {
       return $this->lateralNav;
   }


   private function createFirstNav(UserEntity $userEntity, AdminEntity $adminEntity)
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

   private function createLateralNav(array $url, array $listing, AdminEntity $adminEntity)
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
                        <i class=\"fas fa-tachometer-alt fa-fw me-3\"></i><span>{$item["name"]}</span>
                      </a>";
          } else if ($adminEntity->getSuperAdmin()) {
              $stringLateral .= "
                      <a href= {$url[$i]} class=\"list-group-item list-group-item-action py-2 ripple bg-light\" aria-current=\"true\">
                        <i class=\"fas fa-tachometer-alt fa-fw me-3\"></i><span>{$item["name"]}</span>
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





}