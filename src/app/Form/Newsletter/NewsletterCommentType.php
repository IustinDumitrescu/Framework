<?php

namespace App\Form\Newsletter;

use App\Form\AbstractType;
use App\Form\InputTypes\SubmitType;
use App\Form\InputTypes\TextType;
use App\Interfaces\FormBuilderInterface;

class NewsletterCommentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $formHead, array $options)
    {
        $builder->createHead($formHead);

        $builder
            ->add('', 'comment', TextType::class,
                [
                    "required" => true,
                    "style" => "width: 800px;"
                ]
            )
            ->add('Send', 'send', SubmitType::class, []);


        return $builder;
    }
}