<?php

namespace App\Service;


use App\Entity\UserEntity;

final class HomeService
{

     public const CalculatorChoices = [
        "Add" => '+',
        "Subtract" => '-',
        "Multiply" => '*',
        "Divide" => "/"
    ];

    /**
     * For UserClass task
     *
     * @param array $dataSubmitted
     * @return string
     */

    public function getUserName(array $dataSubmitted): string
    {
        return $this->sanitizeAndCreateUser($dataSubmitted)->getUserFullName();
    }

    /**
     * For UserClass task
     *
     * @param array $dataSubmitted
     * @return bool
     */

    public function getIfIsOver18(array $dataSubmitted): bool
    {
        $user = $this->sanitizeAndCreateUser($dataSubmitted);

        return $user->getAge() >= 18 ;
    }

    /**
     * For UserClass task
     *
     *
     * @param array $dataSubmitted
     * @return UserEntity
     */

    public function sanitizeAndCreateUser(array $dataSubmitted): UserEntity
    {
        $firstName = filter_var($dataSubmitted["first_name"],FILTER_SANITIZE_STRING);

        $lastName = filter_var($dataSubmitted["last_name"],FILTER_SANITIZE_STRING );

        $age = filter_var($dataSubmitted["age"], FILTER_VALIDATE_INT) ? $dataSubmitted["age"] : 0;

        return new UserEntity($firstName, $lastName, $age);
    }


    /**
     * For Calculator task
     *
     *
     *
     * @param array $dataSubmitted
     * @return float|null
     */
    public function getResultFromCalcul(array $dataSubmitted) :?float
    {
        if (!(float)$dataSubmitted["first_number"]) {
            return null;
        }

        if (!(float)$dataSubmitted["second_number"]) {
            return null;
        }

        if (!in_array($dataSubmitted["operation"], self::CalculatorChoices,true)) {
            return null;
        }

        $firstNr = (float)$dataSubmitted["first_number"];

        $secondNr = (float)$dataSubmitted["second_number"];

        return match ($dataSubmitted["operation"]) {
            "+" => round($firstNr + $secondNr, 2),
            "-" => round($firstNr - $secondNr, 2),
            "*" => round($firstNr * $secondNr, 2),
            "/" => round($firstNr / $secondNr, 2),
            default => null,
        };

    }

}
