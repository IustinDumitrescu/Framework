<?php
/**
 * Created by PhpStorm.
 * UserEntity: Iusti
 * Date: 09-Apr-22
 * Time: 12:52 PM
 */

namespace App;


use App\Firewall\LoginFirewall;
use App\Http\Request;

final class Kernel
{

    private $files = [];


    public function getAppDir(): string
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public static function getRootDirectory(): string
    {
        return  $_SERVER["DOCUMENT_ROOT"].'/../';
    }

    /**
     * @return array
     */
    public function getAppFiles(): array
    {
        $scandir = scandir($this->getAppDir());

         $this->checkIfDir($scandir, $this->getAppDir());

         return $this->files;
    }

    /**
     * @param $directories
     * @param $path
     */
    public function checkIfDir($directories , $path): void
    {
        if (is_array($directories)) {
            foreach ($directories as $directory) {
                if (strlen($directory) > 2) {
                    if (is_file($path. '/'.$directory)) {
                        $this->files[] = $path."/".$directory;
                    } else if (scandir($path.'/'. $directory)) {
                        $newDirectory = scandir($path . '/' . $directory);
                        $this->checkIfDir($newDirectory, $path .'/'.$directory);
                    }
                }
            }
        } else if (is_file($directories.'/'.$path)) {
            $this->files[] = $path."/".$directories;
        }
    }


    /**
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function run()
    {
        $container = new Container();

        $request = new Request();

        $referer = $request->server->get('HTTP_REFERER');

        $method = $request->server->get('REQUEST_METHOD');

        $loginFormSubmitted = $request->request->get('login_submit');

        $rootUri = 'http://'.$request->server->get('HTTP_HOST').'/';

        $firewall = new LoginFirewall($container);

        $flashString = filter_var($request->query->get('flashString'),FILTER_SANITIZE_STRING);

        if ($method === 'POST' &&  $loginFormSubmitted !== null
            && ($referer === $rootUri.'login' ||$referer === $rootUri."login?flashString={$flashString}" )
        ) {
            $firewall->checkLogin($request);
        }

        if ($method === 'POST' &&  $loginFormSubmitted !== null
            && ($referer === $rootUri.'admin/login' || $referer === $rootUri."admin/login?flashString={$flashString}" )
        ) {
            $firewall->checkAdminLogin($request);
        }


        return (new Router($container, $request))->submit();
    }





}