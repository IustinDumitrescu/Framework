<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 14-Apr-22
 * Time: 11:53 AM
 */

namespace App\Controller;


use App\Form\LoginType;
use App\Form\RegisterType;
use App\Http\Request;
use App\Interfaces\SessionInterface;
use App\Service\UserService;

class UserController extends AbstractController
{
    public function register(
        SessionInterface $session,
        Request $request,
        UserService $userService
    )
    {
        $templateVars = $this->initializeLayout($session, $request,false);

        $formRegister = $this->createForm(RegisterType::class, [
            "name" => 'Register Form',
            "method" => 'POST',
            "action" => '',
            "id" => 'form_register'
        ]);

        if ($formRegister->isSubmitted() && $formRegister->isValid()) {
            $dataSubmitted = $formRegister->getData();

            $getOperationSuccessString = $userService->createUser($dataSubmitted);

            $templateVars["flash"] = [
                "flashString" => $getOperationSuccessString,
                "flashType" => strpos($getOperationSuccessString, 'Success') !== false
            ];

        }

        $templateVars["formRegister"] = $formRegister->createView();

        return $this->render('register',$templateVars);
    }

    public function login(
        SessionInterface $session,
        Request $request
    )
    {
        $templateVars = $this->initializeLayout($session, $request,false);


        $flashString = filter_var($request->query->get('flashString'),FILTER_SANITIZE_STRING);

        $formLogin = $this->createForm(LoginType::class, [
           "name" => "Login Form",
           "method" => "POST",
           "action" => '',
           "id" => "form_login"
        ]);

        if (!empty($flashString)) {

            if ($flashString === 'nuexista') {
                $flashStrings = 'User-ul nu exista';
                $flashValue = false;
            }

            if ($flashString === 'parolagresita') {
                $flashStrings = 'Parola este gresita';
                $flashValue = false;
            }

            $templateVars["flash"] = [
                "flashString" => $flashStrings,
                "flashType" => $flashValue
            ];
        }

        $templateVars["formLogin"] = $formLogin->createView();

        return $this->render('login', $templateVars);
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @return string
     */
    public function logout(
        SessionInterface $session,
        Request $request
    ): string
    {
        $this->initializeLayout($session, $request, true );

        $userCookie = $request->cookie->get('u_s_r_d');

        if ($userCookie) {
            unset($userCookie);

            $session->set('user', null);
        }

        $this->redirectToRoute('/');
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @return false|string
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function adminLogin (
        SessionInterface $session,
        Request $request
    )
    {
        $templateVars = $this->initializeLayout($session, $request, null);

        if ($this->isAdmin($session) && $session->get('admin') !== null && $request->cookie->get('a_d_u_s_r') !== null) {
            $this->redirect('/admin');
        }

        $flashString = filter_var($request->query->get('flashString'), FILTER_SANITIZE_STRING);

        $formLoginAdmin = $this->createForm(LoginType::class, [
            "name" => "login_admin_form",
            "method" => "POST",
            "action" => '',
            "id" => "form_login_admin"
        ]);


        if (!empty($flashString)) {

            if ($flashString === 'nuexista') {
                $flashStrings = 'User-ul nu exista';
                $flashValue = false;
            }

            if ($flashString === 'parolagresita') {
                $flashStrings = 'Parola este gresita';
                $flashValue = false;
            }

            $templateVars["flash"] = [
                "flashString" => $flashStrings,
                "flashType" => $flashValue
            ];

        }

        $templateVars["formLoginAdmin"] = $formLoginAdmin->createView();

        return $this->render('/admin/adminLogin', $templateVars);
    }



}