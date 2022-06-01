<?php

namespace App\Repository;


use App\Entity\UserEntity;

class UserRepository extends EntityManager
{
    public function createUser(array $filteredData): void
    {
        $user = new UserEntity();

        $user->setFirstName($filteredData["first_name"])
             ->setLastName($filteredData["last_name"])
             ->setAge($filteredData["age"])
             ->setEmail($filteredData["email"])
             ->setAddress($filteredData["adresa"])
             ->setTelefon($filteredData["telefon"])
             ->setIpRegister($filteredData["ip_register"])
             ->setHashPass($filteredData["hash_pass"])
             ->setCreatedAt((new \DateTime('now'))->format('Y-m-d'));

         $query = $this->persist($user);

         $this->flush($query);
    }

}