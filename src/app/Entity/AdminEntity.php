<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 6:11 PM
 */

namespace App\Entity;


use DateTime;

class AdminEntity
{
    /**
    CREATE TABLE `admin` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `user_id` int(11) NOT NULL,
        `rol` varchar(255) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT current_timestamp(),
        `updated_At` datetime DEFAULT NULL,
        `super_admin` tinyint(1) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `FOREIGN` (`user_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4

     */

    public const TableName = 'admin';

    public const RolVanzator = 'vanzator';

    public const RolNewsletter = 'newsletter';

    public const Roluri = [
        self::RolVanzator,
        self::RolNewsletter
    ];

    private ?int $id;

    private ?int $user_id;

    private ?string $rol;

    private ?bool $super_admin;

    private null|string|DateTime $created_at;

    private null|string|DateTime $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return AdminEntity
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }


    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     * @return AdminEntity
     */
    public function setUserId(int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getRol() :string
    {
        return $this->rol;
    }

    /**
     * @param mixed $rol
     * @return AdminEntity
     */
    public function setRol(string $rol): self
    {
        $this->rol = $rol;

        return $this;
    }

    public function getSuperAdmin(): ?bool
    {
         return $this->super_admin;
    }

    /**
     * @param mixed $super_admin
     * @return AdminEntity
     */
    public function setSuperAdmin(bool $super_admin): self
    {
        $this->super_admin = $super_admin;

        return $this;
    }

    public function getCreatedAt(): DateTime|string|null
    {
        return $this->created_at;
    }

    /**
     * @param string|DateTime|null $created_at
     * @return AdminEntity
     */
    public function setCreatedAt(null|string|DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): DateTime|string|null
    {
        return $this->updated_at;
    }

    /**
     *
     * @param string|DateTime|null $updated_at
     * @return AdminEntity
     */
    public function setUpdatedAt(null|string|DateTime $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

}