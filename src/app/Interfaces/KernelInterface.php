<?php
/**
 * Created by PhpStorm.
 * UserEntity: Iusti
 * Date: 09-Apr-22
 * Time: 2:44 PM
 */

namespace App\Interfaces;


interface KernelInterface
{
    /**
     * @param string $id Identifier of the entry to look for.
     *
     *
     * @return mixed Entry.
     */
    public function get(string $id);

    /**
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has(string $id): bool;

}