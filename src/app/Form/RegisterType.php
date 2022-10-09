<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 14-Apr-22
 * Time: 12:22 PM
 */

namespace App\Form;


use App\Form\InputTypes\EmailType;
use App\Form\InputTypes\NumberType;
use App\Form\InputTypes\PasswordType;
use App\Form\InputTypes\SubmitType;
use App\Form\InputTypes\TextType;
use App\Form\InputTypes\UploadableFieldType;
use App\Interfaces\FormBuilderInterface;

class RegisterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        $builder
            ->add("First Name",'first_name',TextType::class,[
                "required" => "required"
            ])
            ->add("Last Name", 'last_name',TextType::class, [
                "required" => "required"
            ])
            ->add("Email",'email',EmailType::class, [
                "required" => "required"
            ])
            ->add("Password",'password_register',PasswordType::class, [
                "required" => "required"
            ])
            ->add("Confirm Password",'confirm_password',PasswordType::class, [
                "required" => "required"
            ])
            ->add("Age",'age',NumberType::class, [])
            ->add("Phone",'telefon',TextType::class,
                [
                    "required" => "required"
                ]
            )
            ->add('Adauga imagine', 'imagPrin',UploadableFieldType::class,
                [
                    "required" => true,
                    "accept" => "image/*"
                ]
            )
            ->add("Address", 'adresa',TextType::class,[])
            ->add('Register','register_submit',SubmitType::class, []);


        return $builder;
    }
}