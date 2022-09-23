<?php

namespace App\Form\InputTypes;

class TextareaType implements InputTypesInterface
{
    private const Type = 'textarea';

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

        $this->viewString = "
        <div class='form-group'>
            <label for = '{$id}' class = 'form-label'>{$name}</label><br>
            <textarea id = '{$id}' name='{$id}[{$id}]' {$stringOfOption} class='form-control'></textarea>
        </div>";

        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }
}