<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 29-May-22
 * Time: 5:16 PM
 */

namespace App\Admin\Fields;


final class AdminListingField
{

    private string $name;

    private string $id;

    private string $typeChosen;

    private array $options;


    public function __construct(string $name, string $id, string $typeChosen, array $options = [])
    {
        $this->name = $name;
        $this->id = $id;
        $this->typeChosen = $typeChosen;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTypeChosen(): string
    {
        return $this->typeChosen;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOption(string $name, $value): self
    {
        $this->options[$name] = $value;
        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }

}