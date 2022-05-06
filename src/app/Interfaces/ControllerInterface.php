<?php

namespace App\Interfaces;


use App\Entity\UserEntity;

interface ControllerInterface
{
    /**
     * @param string $template
     * @param array $data
     * @return mixed
     */
    public function render(string $template, array $data);


    /**
     * @param string $url
     * @param array $options
     */
    public function redirectToRoute(string $url, array  $options = []) :void;


    /**
     * @param string $url
     */
    public function redirect(string $url);


    /**
     * @param SessionInterface $session
     * @param UserEntity|null $user
     * @return mixed
     */
    public function isAdmin(SessionInterface $session, ?UserEntity $user = null);

}