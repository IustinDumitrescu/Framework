<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 14-Apr-22
 * Time: 12:22 PM
 */

namespace App\Form;


use App\Interfaces\FormBuilderInterface;

class RegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        $builder
            ->add("First Name",'first_name','text',["required" => "required"])
            ->add("Last Name", 'last_name','text', ["required" => "required"])
            ->add("Email",'email','email', ["required" => "required"])
            ->add("Password",'password_register','password', ["required" => "required"])
            ->add("Confirm Password",'confirm_password','password', ["required" => "required"])
            ->add("Age",'age',"number", [])
            ->add("Phone",'telefon','text', ["required" => "required"])
            ->add("Address", 'adresa','text',[])
            ->add('Register','register_submit','submit', []);


        return $builder;
    }
}