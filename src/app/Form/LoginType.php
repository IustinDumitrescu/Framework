<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 22-Apr-22
 * Time: 5:11 PM
 */

namespace App\Form;


use App\Form\InputTypes\EmailType;
use App\Form\InputTypes\PasswordType;
use App\Form\InputTypes\SubmitType;
use App\Interfaces\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        $builder
            ->add('Username', 'email', EmailType::class, [
                "required" => "required"
            ])
            ->add('Password', 'password_login',PasswordType::class, [
                "required" => "required"
            ])
            ->add('Login','login_submit',SubmitType::class, []);

        return $builder;

    }

}