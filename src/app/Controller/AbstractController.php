<?php

namespace App\Controller;

use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Form\FormBuilder;
use App\Interfaces\ControllerInterface;
use App\Http\Request;
use App\Interfaces\EntityManagerInterface;
use App\Interfaces\SessionInterface;
use Psr\Container\ContainerInterface;

abstract class AbstractController implements ControllerInterface
{

    /**
     * @var ContainerInterface
     */
    public $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function createForm($className, array $formHead, array $options = [])
    {
        $form = new $className();

        $formBuilderInterface = $this->container->get(FormBuilder::class);

        return $form->buildForm($formBuilderInterface, $formHead ,$options);
    }

    /**
     * @param string $template
     * @param array $data
     * @return false|string
     */
   public function render(string $template, Array $data)
   {
        ob_start();

        foreach ($data as $key => $item) {
            $$key = $item;
        }

        require_once('./../public/views/'.$template.'.php');

        return ob_get_clean();
   }

   public function initializeLayout(
       SessionInterface $session,
       Request $request,
       ?bool $mustBeLogged
   ):array
   {
       $templateVars = [];

       if (!$session->isStarted()) {
           $session->start();
       }

       if ($mustBeLogged !== null) {

           if (!$mustBeLogged && $this->isLoggedIn($session, $request)) {
               $this->redirectToRoute('/');
           }

           if ($mustBeLogged && !$this->isLoggedIn($session, $request)) {
               $this->redirectToRoute('/login');
           }

       }

       $templateVars["logged"] = $this->isLoggedIn($session, $request);

       $templateVars["user"] = $session->get('user');

       return $templateVars;

   }

   public function isLoggedIn(SessionInterface $session, Request $request): bool
   {
       $user = $session->get('user');

       $userCookie = $request->cookie->get('u_s_r_d');

       if ($user && $userCookie && $user->getId() === (int)$userCookie) {
           return true;
       }

       $session->set('user', null);

       return false;

   }

   public function redirectToRoute(string $url, array $parameters = []): void
   {
       $url = "Location:  {$url}";

       if (!empty($parameters)) {
          $url .= '?';

          foreach ($parameters as $key => $parameter) {
             $url .= "{$key}={$parameter}&";
          }

          if ($url[strlen($url)-1] === '&') {
              $url = rtrim($url,'&');
          }

       }

       header($url,true, 302);
   }

    /**
     * @param string $url
     */
    public function redirect(string $url)
   {
       $url = "Location:  {$url}";

       header($url,true, 301);
   }


   public function isXmlHttpRequest(Request $request): bool
   {
       return strtolower($request->server->get('HTTP_X_REQUESTED_WITH')) ==='xmlhttprequest';
   }

    /**
     * @param SessionInterface $session
     * @return mixed
     */
    public function getUser(SessionInterface $session)
    {
       return $session->get('user');
    }

    /**
     * @param SessionInterface $session
     * @param null $user
     * @return null
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function isAdmin(SessionInterface $session, ?UserEntity $user = null)
    {
        $entityManager = $this->container->get(EntityManagerInterface::class);

        if ($user === null) {
            $user = $this->getUser($session);
        }

        if (!is_object($entityManager)) {
            $entityManager = new $entityManager();
        }

        if ($user) {
           if($entityManager->findBy(AdminEntity::class, ["user_id" => $user->getId()])) {
               return true;
           }
            return false;
        }

        return false;
   }






}