<?php

namespace App\Repository;

use http\Exception\RuntimeException;

final class QueryBuilder extends EntityManager
{

    public const Select = 'select';

    public const Update = 'update';

    public const Delete = 'delete';

    private const Operations = [
        'select',
        'update',
        'delete'
    ];

    private string $entityName;

    private string $alias;

    private array $inputsOfSelect;

    private string $initialCondition;

    private array $conditions = [];

    private string $operationName;

    private string $query;

    private ?string $leftJoin = null;

    private array $propertiesOfUpdate = [];

    private array $propertiesValuesOfUpdate = [];

    private ?string $orderBy = null;

    public function setOperation(string $operationName): QueryBuilder
    {
        $this->operationName = $operationName;
        return $this;
    }

    public function select(array $inputsOfSelect): QueryBuilder
    {
        $this->inputsOfSelect = $inputsOfSelect;
        return $this;
    }

    public function from(string $from, string $alias = 'k'): QueryBuilder
    {
        $this->entityName = $from;
        $this->alias = $alias;
        return $this;
    }

    public function LeftJoin(string $tableName, string $conditionType, string $condition): self
    {
        $this->leftJoin = "LEFT JOIN $tableName $conditionType $condition";
        return $this;
    }

    public function where(string $condition): QueryBuilder
    {
        $this->initialCondition = $condition;
        return $this;
    }

    public function andWhere(string $otherCondition): QueryBuilder
    {
        $this->conditions[] = $otherCondition;
        return  $this;
    }

    public function getQuery(): QueryBuilder
    {
        if (!in_array($this->operationName,self::Operations)) {
            throw new RuntimeException('Operatia nu exista');
        }

        $this->query = match ($this->operationName) {
            'select' => $this->getSelectQuery(),
            'update' => $this->getUpdateQuery(),
            'delete' => $this->getDeleteQuery(),
        };

        return $this;
    }

    public function setDql(string $query): self
    {
        $this->query = $query;
        return $this;
    }


    private function getSelectQuery(): string
    {
        $query = "SELECT ";

        if (!empty($this->inputsOfSelect)) {
            foreach ($this->inputsOfSelect as $item) {
                if (str_contains($item, 'count')) {
                    $query .= "count(".$this->entityName::TableName. ".id) as count";
                } else {
                    $query .= $this->entityName::TableName . "." . $item . ",";
                }
            }
        } else {
            $query .= '*';
        }
        $countOfQuery = strlen($query);

        if ($query[$countOfQuery-1] === ",") {
           $query = rtrim($query,",");
        }

        $query.= " FROM ".$this->entityName::TableName ;

        if ($this->leftJoin !== null) {
            $query .= $this->leftJoin;
        }

        if (!empty($this->initialCondition)) {
            $query .= " WHERE ".$this->initialCondition;
        }

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $query .= " AND ".$condition;
            }
        }

        if ($this->orderBy) {
            $query .= $this->orderBy;
        }

        return $query;
    }

    public function getDql(): string
    {
        return $this->query;
    }

    public function getResult(): array
    {
        $con = $this->getConnection();

        $stmt = $con->prepare($this->query);

        $stmt->execute();

        $result = $stmt->get_result();

        $results = [];

        foreach ($result as $item) {
            $results[] = $this->hydrate($item,$this->entityName);
        }

        return $results;
    }

    public function getNormalResult(): array
    {
        $con = $this->getConnection();

        $stmt = $con->prepare($this->query);

        $stmt->execute();

        $result = $stmt->get_result();

        $results = [];

        foreach ($result as $item) {
            $results[] = $item;
        }
        return  $results;
    }

    public function update(string $class): self
    {
        $this->entityName = $class;
        return $this;
    }

    public function set(string $name, $value): self
    {
        $this->propertiesOfUpdate[] = $name;
        $this->propertiesValuesOfUpdate[] = $value;
        return $this;
    }

    private function getUpdateQuery(): string
    {
        $table = $this->entityName::TableName;

        $query = "Update ". $table. " SET ";

        $countOfProp = count($this->propertiesOfUpdate);

        for ($i = 0; $i < $countOfProp; $i++) {

            if (is_string($this->propertiesValuesOfUpdate[$i])) {
                $propertyValue = "'{$this->propertiesValuesOfUpdate[$i]}'";
            } elseif (is_bool($this->propertiesValuesOfUpdate[$i])) {
                $propertyValue = $this->propertiesValuesOfUpdate[$i] ? 1 : 0;
            } else {
                $propertyValue = $this->propertiesValuesOfUpdate[$i];
            }

            $query .= $this->propertiesOfUpdate[$i]. " = " .$propertyValue.",";
        }

        if ($query[strlen($query)-1] === ',') {
            $query = rtrim($query,',');
        }

        if (!empty($this->initialCondition)) {
            $query .= " WHERE ".$this->initialCondition;
        }

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $query .= " AND ".$condition;
            }
        }

        return $query;
    }

    public function execute(): void
    {
        $con = $this->getConnection();

        $stmt = $con->prepare($this->query);

        $stmt->execute();
    }


    private function getDeleteQuery(): string
    {
        $query = "DELETE ";

        $table = $this->entityName::TableName;

        $query .= "FROM ".$table ;

        if (!empty($this->initialCondition)) {
            $query .= " WHERE ".$this->initialCondition;
        }

        if (!empty($this->conditions)) {
            foreach ($this->conditions as $condition) {
                $query .= " AND ".$condition;
            }
        }
        return $query;
    }

    public function orderBy(string $field, string $value): self
    {
        $this->orderBy = " ORDER BY $field $value ";
        return $this;
    }



}