<?php

namespace App\Form\InputTypes;

class PasswordType implements InputTypesInterface
{
    private const Type = 'password';

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
        $stringOfOption = '';

        foreach ($options as $key => $option) {
            if (!is_array($option)) {
                $stringOfOption .= " {$key} = '{$option}' ";
            }
        }

        $type = self::Type;

        $this->id = $id;

        $this->viewString = "
        <div class='form-group'>
            <label for = '{$id}' class = 'form-label'>{$name}</label>
            <input type = '{$type}' id = '{$id}' name='{$id}[{$id}]' {$stringOfOption} class='form-control'>
        </div>";
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}