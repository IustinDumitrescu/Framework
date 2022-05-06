<?php

namespace App;



use App\Http\Request;
use App\Utils\Utils;

final class Router
{

    private $uri = [];

    private $method = [];

    private $controller = [];

    private $slug = [];

    private $newSlug = [];

    /**
     * @var Container
     */
    private $container;
    /**
     * @var Request
     */
    private $request;

    /**
     * Router constructor.
     * @param Container $container
     * @param Request $request
     */
    public function __construct(Container $container, Request $request)
    {
        $this->container = $container;
        $this->request = $request;
    }

    public function createRoutes(): void
    {
        $routes = yaml_parse_file(Kernel::getRootDirectory().'config/routing/routing.yaml');

        foreach ($routes["Routes"] as $route) {
            $this->add($route["path"], $route["controller"], $route["method"], $route["slug"]);
        }

    }


    /**
     * @param $uri
     * @param null $controller
     * @param null $method
     * @param array $slug
     * @return Router
     */
    public function add($uri, $controller = null, $method = null, $slug = []): self
    {
        $this->uri[] = $uri;

        if($method !== null) {
            $this->method[] = $method;
        }

        if ($controller !== null) {
            $this->controller[] = $controller;
        }


        $this->slug[] = $slug;

        return $this;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function submit()
    {
       $this->createRoutes();

       $getUriParam =$this->request->server->get('REQUEST_URI') ?? '/';

       foreach($this->uri as $key => $value) {

            if (!empty($this->slug[$key])) {

               $arrayOfSlugDifference = explode( '/',Utils::get_string_diff($getUriParam, $this->uri[$key]));

               foreach ($this->slug[$key] as $keys => $difference) {

                   $value = str_replace( '{'.$difference.'}', $arrayOfSlugDifference[$keys], $value);

                   $this->newSlug[$difference] = $arrayOfSlugDifference[$keys];
               }

            }

            $posOfVariable = strpos($getUriParam, '?');

            $positionOf = strpos($getUriParam, $value);

            if($posOfVariable !== false && $positionOf !== false
                && preg_match_all("#$value(\?|\&)([^=]+)\=([^&]+)#i",$getUriParam)) {

                if(isset($this->method[$key], $this->controller[$key])) {

                    $controller = $this->container->get($this->controller[$key]);

                    $method = $this->method[$key];

                    $slug = $this->newSlug;

                    return call_user_func_array([$controller, $method], $this->container->getMethodsArgs(
                            $this->controller[$key],
                            $method,
                            $slug)
                    );

                }
            } else if(isset($this->method[$key], $this->controller[$key]) && preg_match("#^$value$#",$getUriParam)) {
                $controller = $this->container->get($this->controller[$key]);

                $method = $this->method[$key];

                $slug = $this->newSlug;

                return call_user_func_array([$controller, $method], $this->container->getMethodsArgs(
                            $this->controller[$key],
                            $method,
                            $slug)
                );
            }
       }
    }




}