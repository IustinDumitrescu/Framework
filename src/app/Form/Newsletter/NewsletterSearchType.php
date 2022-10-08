<?php

namespace App\Form\Newsletter;

use App\Form\AbstractType;
use App\Form\InputTypes\SubmitType;
use App\Form\InputTypes\TextType;
use App\Interfaces\FormBuilderInterface;

class NewsletterSearchType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options): FormBuilderInterface
    {
        $builder->createHead($formHead);

        $builder
            ->add('Cauta','query', TextType::class,
                [
                    "style" => "max-width: 800px; min-width: 400px;"
                ]
            )
            ->add('go', 'go', SubmitType::class, []);

        return $builder;
    }
}