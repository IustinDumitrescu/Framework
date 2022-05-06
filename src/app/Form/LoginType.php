<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 22-Apr-22
 * Time: 5:11 PM
 */

namespace App\Form;


use App\Interfaces\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        $builder
            ->add('Username', 'email', 'email', ["required" => "required"])
            ->add('Password', 'password_login','password', ["required" => "required"])
            ->add('Login','login_submit','submit', []);

        return $builder;

    }

}