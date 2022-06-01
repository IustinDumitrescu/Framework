<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 9:45 PM
 */

namespace App\Form;


use App\Interfaces\FormBuilderInterface;

class GetUserAgeType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
         $builder->createHead($formHead);

         $builder
             ->add('First Name', 'first_name', 'text', [
                 'required' => "required",
                 "value" => isset($options["data"]) ? $options["data"]["first_name"] : ''
             ])
             ->add('Last Name','last_name','text', [
                 'required' => "required",
                 "value" => isset($options["data"]) ? $options["data"]["last_name"] : ''
             ])
             ->add('Age','age','number',[
                 'required' => "required",
                 "value" => isset($options["data"]) ? $options["data"]["age"] : ''
             ])
             ->add('Submit','submit_get_user_age','submit',[]);


         return $builder;

    }
}