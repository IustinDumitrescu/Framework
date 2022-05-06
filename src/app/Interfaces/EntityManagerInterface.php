<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 10-Apr-22
 * Time: 6:19 PM
 */

namespace App\Interfaces;


interface EntityManagerInterface
{
    public function getConnection();

    public function find(string $entityName, int $id);

    public function findBy(string $entityName , array $criteria);

    public function hydrate(array $result, string $entityName);

    public function persist($object): string;

    public function flush(string $sql): void;

}