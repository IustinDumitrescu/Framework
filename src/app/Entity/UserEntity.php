<?php

namespace App\Entity;


class UserEntity
{
    public const TableName = 'user';

    private $id;

    private $first_name;

    private $last_name;

    private $age;

    private $email;

    private $telefon;

    private $address;

    private $ip_register;

    private $password;

    private $hash_pass;

    private $createdAt;


    /**
     * For UserClass task
     *
     *
     * UserEntity constructor.
     * @param null|string $first_name
     * @param null|string $last_name
     * @param int|null $age
     */
    public function __construct(?string $first_name = null, ?string $last_name = null, ?int $age = null)
    {
        $this->first_name = $first_name;

        $this->last_name = $last_name;

        $this->age = $age;
    }


    public function getAge() : ?int
    {
        return $this->age;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return UserEntity
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * @param mixed $first_name
     * @return UserEntity
     */
    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->last_name;
    }

    /**
     * @param mixed $last_name
     * @return UserEntity
     */
    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @param mixed $age
     * @return UserEntity
     */
    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     * @return UserEntity
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     * @return UserEntity
     */
    public function setEmail($email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTelefon()
    {
        return $this->telefon;
    }

    /**
     * @param mixed $telefon
     * @return UserEntity
     */
    public function setTelefon(string $telefon): self
    {
        $this->telefon = $telefon;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIpRegister()
    {
        return $this->ip_register;
    }

    /**
     * @param mixed $ip_register
     * @return UserEntity
     */
    public function setIpRegister(string $ip_register): self
    {
        $this->ip_register = $ip_register;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     * @return UserEntity
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHashPass()
    {
        return $this->hash_pass;
    }

    /**
     * @param mixed $hash_pass
     * @return UserEntity
     */
    public function setHashPass($hash_pass): self
    {
        $this->hash_pass = $hash_pass;

        return $this;
    }

    public function getUserFullName(): string
    {
        return $this->first_name.' '.$this->last_name;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     * @return UserEntity
     */
    public function setAddress($address): self
    {
        $this->address = $address;

        return $this;
    }


}

