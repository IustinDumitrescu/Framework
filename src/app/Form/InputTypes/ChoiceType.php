<?php

namespace App\Form\InputTypes;

class ChoiceType implements InputTypesInterface
{
    private const Type = 'choice';

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

        $this->id = $id;

        foreach ($options as $key => $option) {
            if (!is_array($option)) {
                $stringOfOption .= " {$key} = '{$option}' ";
            }
        }

        $string = " 
         <div class=\"form-group\">
             <label for= '{$id}'>{$name}</label>
             <select  name='{$id}[{$id}]' id='{$id}' {$stringOfOption} class=\"form-control\">
        ";

        foreach ($options["choices"] as $key => $choice) {
            $string .= "<option value  =\"{$choice}\">$key</option>";
        }

        $string.= ' </select> </div>';

        $this->viewString = $string;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }
}