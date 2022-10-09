<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 8:58 PM
 */

namespace App\Controller\Admin;


use App\Admin\AdminDashboardConfigurator;
use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Http\Request;
use App\Interfaces\SessionInterface;


class DashboardCrudController extends AbstractCrudController
{

     public const rootUrl = '/admin';


    /**
     * @param SessionInterface $session
     * @param Request $request
     * @return string
     */
    public function admin(
        SessionInterface $session,
        Request $request
    ): string
    {
        $admin = $this->initializeAdminLayout($session, $request);

        $user = $this->getUser($session);

        dd($this->configureDashboard($user));

    }


    private function configureDashboard($user)
    {
       return AdminDashboardConfigurator::configureForThisUser($user)
            ->new(UserEntity::class, 'SUPER_ADMIN')
            ->new(AdminEntity::class)
            ->getConfiguration();

    }


}