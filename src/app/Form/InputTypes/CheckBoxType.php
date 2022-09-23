<?php

namespace App\Form\InputTypes;

class CheckBoxType implements InputTypesInterface
{
    private const Type = 'checkbox';

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

        $this->id = "{$id}_checkbox";

        foreach ($options as $key => $option) {
            if (!is_array($option)) {
                $stringOfOption .= " {$key} = '{$option}' ";
            }
        }

        $initialValue = str_contains($stringOfOption, 'checked')
            ? "value = true"
            : "value = false";

        $this->viewString = "
        <div class='form-group d-flex'>
             <label class='switch' for='{$id}_checkbox'>
                <input type='checkbox' name='{$id}_checkbox[{$id}_checkbox]' onchange='changeValue(this)' {$stringOfOption} id='{$id}_checkbox'>
                <span class='slider round'></span>
             </label>
             <span class='ml-2'>{$name}</span>
        </div>
        <div class='form-group'>
            <input type='hidden' name='{$id}[{$id}]' id='{$id}' $initialValue >
        </div>
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