<?php

namespace App\Admin\Form;

use App\Form\AbstractType;
use App\Form\InputTypes\SubmitType;
use App\Interfaces\FormBuilderInterface;

class AdminNewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options): FormBuilderInterface
    {
        $builder->createHead($formHead);

        foreach ($options["data"] as $field) {
            $builder->add($field->getName(),$field->getId(),$field->getTypeChosen(), $field->getOptions());
        }

        $builder->add('Submit', "newAdminSubmit",SubmitType::class, []);

        return $builder;
    }

}