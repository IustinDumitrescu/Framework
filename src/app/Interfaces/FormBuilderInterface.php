<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 9:08 PM
 */

namespace App\Interfaces;


interface FormBuilderInterface
{
    public function createHead(array $formHead) ;

    public function add(string $name, string $id , string $typeChosen, array $options) :self;

    public function createView() :array ;

    public function getData() :?array;

    public function isValid() :bool;

    public function isSubmitted() :bool;
}