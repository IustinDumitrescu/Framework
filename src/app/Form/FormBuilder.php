<?php

namespace App\Form;

use App\Form\InputTypes\SubmitType;
use App\Form\InputTypes\UploadableFieldType;
use App\Http\Request;
use App\Interfaces\FormBuilderInterface;
use App\Session\Session;
use App\Utils\Utils;
use Exception;

class FormBuilder implements FormBuilderInterface
{
    private string $head = '';

    private array $body = [];

    private array $types = [];

    private string $method = '';

    private string $formId;

    private array $formHead = [];

    private string $submitId;
    
    private array $arrayOfInputs = [];

    private array $formInputs = [];

    private bool $hasUploadableFiles = false;

    /**
     * @var Request
     */
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * @param array $formHead
     * @return void
     */
    public function createHead(array $formHead): void
    {
        $headOptions = '';

        if (!empty($formHead["options"])) {
            foreach ($formHead["options"] as $key => $option) {
                $headOptions .= " $key = '{$option}' ";
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
        $typeClass = new $typeChosen();

        $typeClass
            ->setSetting($id, $name, $options);

        $this->arrayOfInputs[] = $typeClass->getId();

        if ($typeChosen === SubmitType::class) {
            $this->submitId = $typeClass->getId();
        }

        if ($typeChosen === UploadableFieldType::class && !$this->hasUploadableFiles) {
            $this->hasUploadableFiles = true;
            $newHead = substr($this->head, 0, -2) . "enctype='multipart/form-data'>";
            $this->head = $newHead;
        }

        $this->formInputs[$id] = $typeClass->getViewString();

        $this->body[] = $typeClass->getViewString();

        $this->types[] = $typeChosen;

        $this->arrayOfInputs[] = '_token';

        return $this;
    }


    /**
     * @throws Exception
     */
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

        $session->set(($this->formId), $token);

        $form .= "\r\n<input type='hidden' id ='{$this->formHead["id"]}_token' name = '{$this->formHead["id"]}[_token]' value = '{$token}'>\r\n </form>";

        $this->formInputs[$this->formId] = $this->head;

        $this->formInputs["_token"] = "\r\n<input type='hidden' id ='{$this->formHead["id"]}_token' name = '{$this->formHead["id"]}[_token]' value = '{$token}'>\r\n";

        $this->formInputs["form"] = $form;

        return $this->formInputs;
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
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
            return $session->get(($this->formId)) === $formData[$idOfToken];
        }
        return false;
    }


    public function isSubmitted() :bool
    {
        if ($this->submitId) {
            if ($this->method === 'GET') {
                return $this->request->query->get($this->submitId) !== null;
            }
            return $this->request->request->get($this->submitId) !== null;
        }
        return false;
    }






}