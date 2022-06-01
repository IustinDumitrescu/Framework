<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 8:59 PM
 */

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Entity\AdminEntity;
use App\Http\Request;
use App\Interfaces\SessionInterface;

abstract class AbstractCrudController extends AbstractController
{
    public function initializeAdminLayout(SessionInterface $session, Request $request) :AdminEntity
    {
        if (!$session->isStarted() || !$this->isAdmin($session) || $session->get('admin') === null) {
            $this->redirectToRoute('/admin/login');
        }

        if ($request->cookie->get('a_d_u_s_r') === null) {
            $request->cookie->setCookie('u_s_r_d', $this->getUser($session)->getId(), [
                "expires" => time() + 1800,
                "path" => '/',
                "secure" => true,
                "httponly" => true
            ]);
        }

        return $session->get('admin');

    }




}