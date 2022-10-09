<?php

namespace App\Form\InputTypes;

class DateType implements InputTypesInterface
{
    private const Type = 'date';

    private string|null $id = null;

    private string|null $viewString = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getViewString(): ?string
    {
        return $this->viewString;
    }

    public function setSetting(string $id, string $name, array $options): void
    {
        $stringOfOption = '';

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

        $this->id = $id;
    }
}