<?php

namespace App\Form;

use App\Http\Request;
use App\Interfaces\FormBuilderInterface;
use App\Session\Session;
use App\Utils\Utils;

class FormBuilder implements FormBuilderInterface
{
    private $head = '';

    private $body = [];

    private $types = [];

    private $method = '';

    private $formId;

    private $formHead = [];

    private $submitId;
    
    private $arrayOfInputs = [];

    private $formInputs = [];

    private const Types = [
        "checkbox",
        "button",
        "color",
        "date",
        "datetime-local",
        "email",
        "file",
        "hidden",
        "image",
        "radio",
        "search",
        "submit",
        "text",
        "time",
        "number",
        "submit",
        "choice",
        "password"
    ];
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    public function createHead(
        array $formHead = [
            "name" => 'form',
            "method" => 'POST',
            "action" => '',
            "id" => 'formId',
            "options" => []
        ]
    )
    {

        $headOptions = '';

        if (!empty($formHead["options"])) {

            foreach ($formHead["options"] as $key => $option) {
                $headOptions .= " {$key} = '{$option}' ";
            }

        }

        $this->formHead = $formHead;

        $this->method = $formHead["method"];
        
        $this->arrayOfInputs[] = $formHead["id"]; 


        $this->formId = $formHead["id"];

        $this->head = "<form name = '{$formHead["name"]}' method = '{$formHead["method"]}' id = '{$formHead["id"]}' action = '{$formHead["action"]}' {$headOptions}>";
    }


    public function add(string $name, string $id , string $typeChosen, array $options): self
    {
        foreach (self::Types as $type) {

            if ($typeChosen === $type ) {

                
                $this->arrayOfInputs[] = $id;
                
                if ($typeChosen === 'submit' && in_array($typeChosen, $this->types, true)) {
                    return $this;
                }

                if ($typeChosen === 'submit') {
                    $this->submitId = $id;
                }

                $stringOfOption = '';

                foreach ($options as $key => $option) {
                    if (!is_array($option)) {
                        $stringOfOption .= " {$key} = '{$option}' ";
                    }
                }

                if ($typeChosen === 'submit') {
                    $input = "
                    <button type=\"submit\" class=\"btn btn-primary text-center \" name='{$id}[{$id}]' id='{$id}' >{$name}</button> 
                    ";
                } else if ($typeChosen === 'choice') {
                    $input = " 
                     <div class=\"form-group\">
                         <label for= '{$id}'>{$name}</label>
                         <select  name='{$id}[{$id}]' id='{$id}' {$stringOfOption} class=\"form-control\">
                    ";

                    foreach ($options["choices"] as $key => $choice) {
                        $input .= "<option value  =\"{$choice}\">$key</option>";
                    }

                    $input.= ' </select> </div>';

                } else {
                    $input = "
                <div class='form-group'>
                    <label for = '{$id}' class = 'form-label'>{$name}</label>
                    <input type = '{$typeChosen}' id = '{$id}' name='{$id}[{$id}]' {$stringOfOption} class='form-control'>
                </div>";
                }

                $this->formInputs[$id] = $input;

                $this->body[] = $input;

                $this->types[] = $typeChosen;
            }

        }

        $this->arrayOfInputs[] = '_token';

        return $this;
    }


    public function createView(): array
    {
        $form = $this->head;

        foreach ($this->body as $item) {
            $form .= $item;
        }

        $token = Utils::createCsrfToken();

        $session = new Session();

        if (!$session->isStarted()) {
            throw new \RuntimeException('Session was not started');
        }

        $session->set((string)($this->formId), $token);

        $form .= "\r\n<input type='hidden' id ='{$this->formHead["id"]}_token' name = '{$this->formHead["id"]}[_token]' value = '{$token}'>\r\n </form>";

        $this->formInputs[$this->formId] = $this->head;

        $this->formInputs["_token"] = "\r\n<input type='hidden' id ='{$this->formHead["id"]}_token' name = '{$this->formHead["id"]}[_token]' value = '{$token}'>\r\n";

        $this->formInputs["form"] = $form;

        return $this->formInputs;

    }

    /**
     * @return array|mixed|null
     */
    public function getData() :?array
    {
        if (isset($this->method)) {
            
            if ($this->method === 'POST') {
                return $this->request->request->getContent();
            }

            if ($this->method === 'GET') {
                return $this->request->query->all();
            }

        }
        return null;
    }

    public function isValid(): bool
    {
        $idOfToken = '_token';

        $session = new Session();
        
        $formData = $this->getData();
        
        foreach ($formData as $inputId => $value) {
            if (!in_array($inputId, $this->arrayOfInputs, true)) {
                return false;
            } 
        }

        if (isset($formData[$idOfToken])) {
            return  $session->get((string)($this->formId)) === $formData[$idOfToken];
        }

        return false;
    }


    public function isSubmitted() :bool
    {
        if ($this->submitId !== null) {

            if ($this->method === 'GET') {
                return $this->request->query->get($this->submitId) !== null;
            }

            return $this->request->request->get($this->submitId) !== null;
        }

        return false;
    }






}