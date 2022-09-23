<?php

namespace App\Form\InputTypes;

class TextType implements InputTypesInterface
{
    private const Type = 'text';

    private string|null $id = null;

    private string|null $viewString = null;

    /**
     * @return string|null
     */
    public function getViewString(): ?string
    {
        return $this->viewString;
    }


    public function setSetting(string $id, string $name, array $options): void
    {
        $stringOfOption = '';

        $this->id = $id;

        foreach ($options as $key => $option) {
            if (!is_array($option)) {
                $stringOfOption .= " {$key} = '{$option}' ";
            }
        }

        $type = self::Type;

        $this->viewString = "
         <div class='form-group'>
            <label for = '{$id}' class = 'form-label'>{$name}</label>
            <input type = '{$type}' id = '{$id}' name='{$id}[{$id}]' {$stringOfOption} class='form-control'>
        </div>";
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}