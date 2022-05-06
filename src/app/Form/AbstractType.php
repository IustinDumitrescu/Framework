<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 9:46 PM
 */

namespace App\Form;


use App\Interfaces\FormBuilderInterface;

abstract class AbstractType
{
    abstract public function buildForm(FormBuilderInterface $builder , array $formHead, array  $options);
}