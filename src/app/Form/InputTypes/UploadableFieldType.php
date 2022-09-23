<?php

namespace App\Form\InputTypes;

class UploadableFieldType implements InputTypesInterface
{
    private const Type = 'file';

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
            <div class='custom-file'>
                <input type = '{$type}' id = '{$id}' name='{$id}[{$id}]' {$stringOfOption} class='custom-file-input'>
                <label for='{$id}' class='custom-file-label'></label>
            </div>
        </div>   
        ";
    }

    public function getId(): ?string
    {
        return $this->id;
    }

}