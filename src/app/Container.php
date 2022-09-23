<?php
/**
 * Created by PhpStorm.
 * UserEntity: Iusti
 * Date: 09-Apr-22
 * Time: 12:25 PM
 */

namespace App;

use App\Exception\Container\ContainerException;
use App\Utils\Utils;
use Psr\Container\ContainerInterface;
use ReflectionClass;

final class Container implements ContainerInterface
{

    private array $entries = [];

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function get(string $id)
    {
        if ($this->has($id)) {

            return $this->entries[$id];
        }

       return $this->resolve($id);

    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, $concrete): void
    {
        $this->entries[$id] = new $concrete();
    }

    /**
     * @param string $id
     * @return object
     * @throws ContainerException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function resolve(string $id): object|bool|int|array|string
    {
        $reflection = new ReflectionClass($id);

        if (!$reflection->isInstantiable()) {

            if ($reflection->isInterface()) {
                return $this->dealWithItIfIsInterface($reflection);
            }

            throw new ContainerException('Class '.$id. 'is not instantiable');
        }

        $constructor = $reflection->getConstructor();

        $parameters = $constructor ? $constructor->getParameters() : '';

        if (! $constructor || empty($parameters )) {

            $this->set($id, $id);

            return new $id;
        }

        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if (! $type) {
                $dependencies[] = null;
            } else if ($type->isBuiltin()) {

                $getType = $this->dealWithItIfIsBuildIn($type->getName());

                if ($getType !== null) {

                     return $dependencies[] = $getType;
                }

                throw new ContainerException('Tipul parametrului ' . $parameter->getName() . ' nu are valoare default in container');

            } else {

                $name = $type->getName();

                $newDependency = $this->get($name);

                $dependencies[] = $newDependency;

                $this->set($name, $newDependency);

            }
        }

        return $reflection->newInstanceArgs($dependencies);
    }


    public function dealWithItIfIsBuildIn( string $name): bool|int|array|string|null
    {
        return match ($name) {
            'int' => 0,
            'string' => '',
            'bool' => false,
            'array' => [],
            default => null,
        };
    }

    /**
     * @throws \ReflectionException
     * @throws ContainerException
     */
    public function dealWithItIfIsInterface(ReflectionClass $reflection)
    {
        $kernel = new Kernel();

        $files = $kernel->getAppFiles();

        $arrayOfInterfaceImplementation = [];

        foreach ($files as $file) {

            $class = Utils::createNameSpaceFromDirectory($file);

            $reflect = new ReflectionClass($class);

            if ($reflect->implementsInterface($reflection->name) && $reflect->isInstantiable()) {
                $arrayOfInterfaceImplementation[] = $class;
            }
        }

        $classInstance = $arrayOfInterfaceImplementation[0];

        return $this->get($classInstance);

    }

    /**
     * @param string $controller
     * @param string $method
     * @param array $slugs
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function getMethodsArgs(string $controller, string $method, array $slugs) :array
    {
        $reflection = new \ReflectionClass($controller);

        $methodOfController = $reflection->getMethod($method);

        $parameters = $methodOfController->getParameters();

        $args = [];

        if (empty($parameters)) {
            return $args;
        }

        foreach ($parameters as $parameter) {

            $type = $parameter->getType();

            $typeName = $type?->getName();

            $name = $parameter->getName();

            if (array_key_exists($name, $slugs)) {

                foreach ($slugs as $key => $slug) {

                    if ($key === $name) {
                        $args[$name] = $slug;
                    }
                }
            } else if ($typeName !== null) {

                $reflect = new ReflectionClass($typeName);

                if ($reflect->isInterface()) {
                    $args[$name] = $this->dealWithItIfIsInterface($reflect);
                } else {

                    $args[$name] = $this->get($typeName);
                }

            } else {

                $args[$name] = '';
            }
        }

        return $args;
    }



}