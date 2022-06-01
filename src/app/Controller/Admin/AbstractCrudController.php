<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 8:59 PM
 */

namespace App\Controller\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\Config\AdminContext;
use App\Admin\Config\AdminDashboardConfigurator;
use App\Controller\AbstractController;
use App\Http\Request;
use App\Interfaces\SessionInterface;

abstract class AbstractCrudController extends AbstractController
{

    public function initializeAdminLayout(SessionInterface $session, Request $request) :array
    {
        $adminTemplate = [];

        if (!$session->isStarted()
            || !$this->isAdmin($session)
            || $session->get('admin') === null
            || $request->cookie->get('a_d_u_s_r') === null) {
            $this->redirectToRoute('/admin/login');
        }

        $adminTemplate["user"] = $this->getUser($session);

        $adminTemplate["admin"] = $session->get('admin');

        return $adminTemplate;
    }

    public function configureActions(): array
    {
        return [
             new AdminAction(
                 AdminContext::AdminActionIndex,
                 '',
                 '',
                 true,
                 AdminDashboardConfigurator::PageIndex
             ),
             new AdminAction(
                 AdminContext::AdminActionShow,
                 'Show',
                 'fa-solid fa-eye',
                 false,
                 AdminDashboardConfigurator::PageShow
             ),
             new AdminAction(
                 AdminContext::AdminActionEdit,
                 'Edit',
                 'fas fa-edit',
                 false,
                 AdminDashboardConfigurator::PageEdit
             ),
             new AdminAction(
                 AdminContext::AdminActionNew,
                 'Adauga',
                 'fa-solid fa-plus',
                 true,
                 AdminDashboardConfigurator::PageNew
             )
        ];
    }

    public function index(AdminContext $adminContext)
    {

    }






}