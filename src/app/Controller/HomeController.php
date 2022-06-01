<?php

namespace App\Controller;

use App\Form\CalculatorType;
use App\Form\GetUserAgeType;
use App\Http\Request;
use App\Interfaces\SessionInterface;
use App\Service\HomeService;

class HomeController extends AbstractController
{

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param HomeService $homeService
     * @return false|string
     */
    public function index(
        SessionInterface $session,
        Request $request,
        HomeService $homeService
    )
    {
        $templateVars = $this->initializeLayout($session, $request,null);

        $formGetUserAge = $this->createForm(GetUserAgeType::class, [
            'name' => 'getUserAge',
            'method' => 'GET',
            'action' => '',
            'id' => 'getUserAgeForm',
            "options" => []
        ]);

        if ($formGetUserAge->isSubmitted() && $formGetUserAge->isValid()) {

           $data = $formGetUserAge->getData();

            $formGetUserAge = $this->createForm(GetUserAgeType::class, [
                'name' => 'getUserAge',
                'method' => 'GET',
                'action' => '',
                'id' => 'getUserAgeForm',
                "options" => []
            ],
            [
                "data" => $data
            ]);

           $userName = $homeService->getUserName($data);

           $ageOfUser = $homeService->getIfIsOver18($data);

           $templateVars["flash"] = [
               "flashString" => $ageOfUser ? "{$userName} este major" : "{$userName} este minor",
               "flashType" => $ageOfUser
           ];
        }

        $templateVars["formGetUserAge"] = $formGetUserAge->createView();

        return $this->render('home', $templateVars);
    }
    
    public function calculator(
     SessionInterface $session,
     Request $request,
     HomeService $homeService
    )
    {
        $templateVars = $this->initializeLayout($session, $request,null);

        $formCalculator = $this->createForm(CalculatorType::class,[
           'name' => 'Calculator',
           'method' => 'GET',
           'action' => 'calculator',
           'id' => 'calculatorForm',
           "options" => []
        ]);

        if ($formCalculator->isSubmitted() && $formCalculator->isValid()) {

            $dataFromForm = $formCalculator->getData();

            $formCalculator = $this->createForm(CalculatorType::class,[
                'name' => 'Calculator',
                'method' => 'GET',
                'action' => 'calculator',
                'id' => 'calculatorForm'
            ],
            [
                "data" => $dataFromForm
            ]);

            $resutlFromCalcul = $homeService->getResultFromCalcul($dataFromForm);

            $templateVars["flash"] = [
                "flashString" => $resutlFromCalcul !== null ? "Rezultatul operatiei este {$resutlFromCalcul}" : "Datele au fost introduse gresit",
                "flashType" => $resutlFromCalcul !== null
            ];

        }

        $templateVars["formCalculator"] = $formCalculator->createView();

        return $this->render('calculator', $templateVars);
        
    }
    



}

