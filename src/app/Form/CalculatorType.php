<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 12-Apr-22
 * Time: 6:46 PM
 */

namespace App\Form;


use App\Form\InputTypes\ChoiceType;
use App\Form\InputTypes\NumberType;
use App\Form\InputTypes\SubmitType;
use App\Interfaces\FormBuilderInterface;
use App\Service\HomeService;

class CalculatorType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        $builder
            ->add('First Number','first_number',NumberType::class, [
                "required" => "required",
                "step" => "0.01",
                "value" => isset($options["data"]) ? $options["data"]["first_number"] : ''
            ])
            ->add('Second Number', 'second_number',NumberType::class, [
                "required" => "required",
                "step" => "0.01",
                "value" => isset($options["data"]) ? $options["data"]["second_number"] : ''
            ])
            ->add('Operation','operation',ChoiceType::class,[
                "required" => "required",
                "choices" => HomeService::CalculatorChoices
            ])
            ->add('Calculate','calculate',SubmitType::class,[
                "required"
            ]);

        return $builder;

    }
}