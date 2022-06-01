<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 6:11 PM
 */

namespace App\Entity;


class AdminEntity
{

    public const TableName = 'admin';

    private ?int $id;

    private ?int $user_id;

    private ?string $rol;

    private ?bool $super_admin;

    private $created_at;

    private $updated_at;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return AdminEntity
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return AdminEntity
     */
    public function setUserId($user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRol()
    {
        return $this->rol;
    }

    /**
     * @param mixed $rol
     * @return AdminEntity
     */
    public function setRol($rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuperAdmin()
    {
        return $this->super_admin;
    }

    /**
     * @param mixed $super_admin
     * @return AdminEntity
     */
    public function setSuperAdmin($super_admin): self
    {
        $this->super_admin = $super_admin;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     * @return AdminEntity
     */
    public function setCreatedAt($created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     * @return AdminEntity
     */
    public function setUpdatedAt($updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}