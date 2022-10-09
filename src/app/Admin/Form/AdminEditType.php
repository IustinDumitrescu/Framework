<?php

namespace App\Admin\Form;

use App\Form\AbstractType;
use App\Form\InputTypes\SubmitType;
use App\Interfaces\FormBuilderInterface;

class AdminEditType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        foreach ($options["data"] as $field) {
            if ($field->getTypeChosen() === 'choice') {
                $options = $field->getOptions();

                $choices = $options["choices"];
                $value = $options["value"];

                $newOptions = [];

                foreach ($choices as $name => $choice) {
                    if ($value === $choice) {
                        $newOptions[$name] = $value;
                    }
                }
                $differences = array_diff_assoc($choices, $newOptions );

                foreach ($differences as $key => $difference) {
                    $newOptions[$key] = $difference;
                }

                $field->setOption("choices", $newOptions);
            } else if (($field->getTypeChosen() === 'checkbox') && $field->getOptions()["value"]) {
                $field->setOption("checked", '');
            }

            $builder->add($field->getName(), $field->getId(), $field->getTypeChosen(), $field->getOptions());
        }

        $builder->add('Submit', "editAdminSubmit",SubmitType::class, []);

        return $builder;
    }
}