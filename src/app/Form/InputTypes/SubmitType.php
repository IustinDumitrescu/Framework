<?php

namespace App\Form\InputTypes;

class SubmitType implements InputTypesInterface
{
    private const Type = 'submit';

    private string|null $viewString = null;

    private string|null $id = null;

    /**
     * @return string|null
     */
    public function getViewString(): ?string
    {
        return $this->viewString;
    }


    public function setSetting(string $id, string $name, array $options): void
    {
        $type = self::Type;

        $this->id = $id;

        $this->viewString ="
        <button type=\"{$type}\" class=\"btn btn-primary text-center \" name='{$id}[{$id}]' id='{$id}' >{$name}</button> 
        ";
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

}