<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 22-Apr-22
 * Time: 5:22 PM
 */

namespace App\Firewall;

use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Http\Request;
use App\Interfaces\ControllerInterface;
use App\Interfaces\SessionInterface;
use App\Repository\AdminRepository;
use App\Repository\UserRepository;
use Psr\Container\ContainerInterface;

class LoginFirewall
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    /**
     * @param Request $request
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function checkLogin(Request $request)
    {
        $contentOfLoginData = $request->request->getContent();

        $session = $this->container->get(SessionInterface::class);

        $controller = $this->container->get(ControllerInterface::class);

        $tokenOfLogin = $session->get('form_login');

        if (!empty($contentOfLoginData) && $contentOfLoginData["_token"] === $tokenOfLogin) {

            $userRepository = $this->container->get(UserRepository::class);

            $sanitizeEmail = filter_var( $contentOfLoginData["email"], FILTER_SANITIZE_EMAIL);

            $userArray = $userRepository->findBy(UserEntity::class, ["email" => $sanitizeEmail]);

            $user = $userArray[0] ?? null;

            if (!empty($user)) {

               $passwordInput = trim(filter_var($contentOfLoginData["password_login"],FILTER_SANITIZE_FULL_SPECIAL_CHARS));

               if (password_verify($passwordInput, $user->getHashPass())) {
                   $session->set('user', $user);

                   $request->cookie->setCookie('u_s_r_d', $user->getId(), [
                       "expires" => time() + 3600,
                       "path" => '/',
                       "secure" => true,
                       "httponly" => true
                   ]);

                  return $controller->redirect('/');
               }
               return $controller->redirectToRoute('/login', [
                        "flashString" => "parolagresita",
                   ]
               );
            }

            return $controller->redirectToRoute('/login', [
                    "flashString" => "nuexista",
                ]
            );

        }

    }

    public function checkAdminLogin(Request $request)
    {
        $contentOfLoginData = $request->request->getContent();

        $session = $this->container->get(SessionInterface::class);

        $controller = $this->container->get(ControllerInterface::class);

        $tokenOfLogin = $session->get('form_login_admin');

        if (!empty($contentOfLoginData) && $contentOfLoginData["_token"] === $tokenOfLogin) {

            $userRepository = $this->container->get(UserRepository::class);

            $sanitizeEmail = filter_var($contentOfLoginData["email"], FILTER_SANITIZE_EMAIL);

            $arrayOfUser = $userRepository->findBy(UserEntity::class, ["email" => $sanitizeEmail]);

            $user = $arrayOfUser[0] ?? null;

            if (!empty($user) && $controller->isAdmin($session, $user)) {

                $passwordInput = trim(filter_var($contentOfLoginData["password_login"],FILTER_SANITIZE_STRING));

                if (password_verify($passwordInput, $user->getHashPass())) {

                    $session->set('user', $user);


                    $request->cookie->setCookie('u_s_r_d', $user->getId(), [
                        "expires" => time() + 1800,
                        "path" => '/',
                        "secure" => true,
                        "httponly" => true
                    ]);


                    $adminRepository = $this->container->get(AdminRepository::class);

                    $adminArray = $adminRepository->findBy(AdminEntity::class, ['user_id' => $user->getId()]);

                    $admin = $adminArray[0] ?? null;

                    $session->set('admin', $admin);

                    $request->cookie->setCookie('a_d_u_s_r', $admin->getId(), [
                        "expires" => time() + 1800,
                        "path" => '/',
                        "secure" => true,
                        "httponly" => true
                    ]);


                    return $controller->redirect('/admin');
                }

                return $controller->redirectToRoute('/admin/login', [
                        "flashString" => "parolagresita",
                    ]
                );

            }

            return $controller->redirectToRoute('/admin/login', [
                    "flashString" => "nuexista",
                ]
            );

        }
    }




}