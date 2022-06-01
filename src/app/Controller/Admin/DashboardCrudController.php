<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 8:58 PM
 */

namespace App\Controller\Admin;



use App\Admin\Fields\AdminDashboardField;
use App\Admin\Config\AdminDashboardConfigurator;
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
        $adminTemplate = $this->initializeAdminLayout($session, $request);

        $template = $this->configureDashboard($adminTemplate["user"], $adminTemplate["admin"], $request);

        if (!$template) {
            $this->redirect('/admin');
        }

        $adminTemplate["templateAdmin"] = $template;

        return $this->render('/admin/homeLayout', $adminTemplate );

    }

    /**
     * @param UserEntity $user
     * @param AdminEntity $admin
     * @param Request $request
     * @return \App\Admin\Template\AdminTemplate
     */
    private function configureDashboard(
        UserEntity $user,
        AdminEntity $admin,
        Request $request
    ): ?\App\Admin\Template\AdminTemplate
    {
        return AdminDashboardConfigurator::configureForThisUser($user, $admin, $request, $this->container)
            ->configureItems(
                AdminDashboardField::new('Admini',AdminCrudController::class)
            )->getConfiguration();

    }





}