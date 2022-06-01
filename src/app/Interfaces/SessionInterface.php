<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 11-Apr-22
 * Time: 8:35 PM
 */

namespace App\Interfaces;


interface SessionInterface
{
     public function isStarted(): bool;

     public function start(): bool;

     public function has(string $key): bool;

     public function get(string $key);

     public function set(string $key, $value);

}