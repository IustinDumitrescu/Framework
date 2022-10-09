<?php

namespace App\Form\InputTypes;

interface InputTypesInterface
{
    public function getId(): ?string;

    public function getViewString(): ?string;

    public function setSetting(string $id, string $name, array $options): void;

}