<?php
/**
 * Created by PhpStorm.
 * UserEntity: Iusti
 * Date: 09-Apr-22
 * Time: 12:33 PM
 */

namespace App\Exception\Container;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends  \Exception implements NotFoundExceptionInterface
{

}