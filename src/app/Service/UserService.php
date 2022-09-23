<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 14-Apr-22
 * Time: 12:01 PM
 */

namespace App\Service;

use App\Entity\UserEntity;
use App\Repository\UserRepository;
use App\Utils\Utils;

final class UserService
{


    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    public function createUser(array $dataInput): ?string
    {
        $newData = [];

        $newData["first_name"] = filter_var($dataInput["first_name"],FILTER_SANITIZE_STRING);

        $newData["last_name"] = filter_var($dataInput["last_name"],FILTER_SANITIZE_STRING);

        $newData["password"] = filter_var($dataInput["password_register"],FILTER_SANITIZE_STRING);

        $newData["telefon"] = filter_var($dataInput["telefon"], FILTER_SANITIZE_STRING);

        $confirmPassword = filter_var($dataInput["confirm_password"], FILTER_SANITIZE_STRING);

        $newData["email"] = filter_var($dataInput["email"],FILTER_SANITIZE_STRING);

        $newData["adresa"] = filter_var($dataInput["adresa"],FILTER_SANITIZE_STRING);

        $newData["age"] = filter_var($dataInput["age"],FILTER_VALIDATE_INT) ? $dataInput["age"] : null;

        $newData["ip_register"] = Utils::getIp();

        if (!preg_match('/^(\+4|)?(07[0-8]{1}[0-9]{1}|02[0-9]{2}|03[0-9]{2}){1}?(\s|\.|\-)?([0-9]{3}(\s|\.|\-|)){2}$/', $dataInput["telefon"])) {
          return 'Telefonul nu este valid';
        }

        if ($newData["password"] !== $confirmPassword) {
            return 'Parola si Confirma Parola nu coincid';
        }

        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $newData["password"])) {
            return 'Parola trebuie sa aibe cel putin 8 caractere, o litera mare, o litera mica, un numar si un caracter special';
        }

        $newData["hash_pass"] = password_hash(trim($newData["password"]), PASSWORD_DEFAULT);

        if (!filter_var($newData["email"], FILTER_VALIDATE_EMAIL)) {
            return 'Introduceti un Email valid';
        }

        $user = $this->userRepository->findBy(UserEntity::class,["email" => $newData["email"]]);

        if (!empty($user)) {
            return 'Adresa de email este utilizata de un alt utilizator';
        }

        $this->userRepository->createUser($newData);

        return "Success, contul cu username-ul: {$newData["email"]} a fost creat !";

    }

}