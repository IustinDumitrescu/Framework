<?php
namespace App\Repository;

use App\Database\Database;
use App\Interfaces\EntityManagerInterface;
use App\Utils\Utils;
use ReflectionClass;
use ReflectionException;


class EntityManager implements EntityManagerInterface
{

    protected function getConnection(): \mysqli
    {
        return (new Database())->getConnection();
    }

    public function find(string $entityName, int $id)
    {
        $table = $entityName::TableName;

        $con = $this->getConnection();

        $sql = "SELECT * from {$table} where id = ? LIMIT 1";

        $stmt = $con->prepare($sql);

        $stmt->bind_param('i', $id);

        $stmt->execute();

        $result = $stmt->get_result()->fetch_assoc();

        return $this->hydrate($result , $entityName);

    }

    public function findAll(string $entityName): array
    {
        $table = $entityName::TableName;

        $con = $this->getConnection();

        $sql = "SELECT * from {$table}";

        $stmt = $con->prepare($sql);

        $stmt->execute();

        $results = [];

        $result = $stmt->get_result();

        foreach ($result as $item) {
            $results[] = $this->hydrate($item, $entityName);
        }

        return $results;
    }

    public function findBy(string $entityName , array $criteries): array
    {
        $table = $entityName::TableName;

        $con = $this->getConnection();

        $sql = "SELECT * FROM {$table} WHERE ";

        $i = 0;

        foreach ($criteries as $key => $criteria) {
            if ($i === 0) {
                $sql .= "{$key} = '{$criteria}'";
            } else {
                $sql .= "AND {$key} = '{$criteria}'";
            }
            $i++;
        }

        $sql .= ' LIMIT 1';

        $stmt = $con->prepare($sql);

        $stmt->execute();

        $result = $stmt->get_result();

        $results = [];

        foreach ($result as $item) {
            $results[] = $this->hydrate($item, $entityName);
        }

        return $results;
    }

    public function findMultipleBy(string $entityName , array $criteries, array $orderBy = []) : array
    {
        $table = $entityName::TableName;

        $con = $this->getConnection();

        $sql = "SELECT * FROM {$table} WHERE ";

        $i = 0;

        foreach ($criteries as $key => $criteria) {
            if ($i === 0) {
                $sql .= "{$key} = '{$criteria}'";
            } else {
                $sql .= "AND {$key} = '{$criteria}'";
            }
            $i++;
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $value) {
                $sql .= " ORDER BY $field $value ";
            }
        }

        $stmt = $con->prepare($sql);

        $stmt->execute();

        $result = $stmt->get_result();

        $results = [];

        foreach ($result as $item) {
            $results[] = $this->hydrate($item, $entityName);
        }

        return $results;
    }

    /**
     * @throws ReflectionException
     */
    public function persist($object): string
    {
       $class = get_class($object);

       $table = $class::TableName;

       $reflection = new ReflectionClass($class);

       $propertiesReflect = $reflection->getProperties();

       $infoSchema = $this->getColumnsOfInformationSchema($table);

       $properties = [];

       foreach ($propertiesReflect as $reflectionProperty) {

           $nameOfProperty = $reflectionProperty->getName();

           if (in_array($nameOfProperty, $infoSchema,true)) {
               $properties[] = $reflectionProperty->getName();
           }

       }

       $valuesForQuery = [];

       foreach ($properties as $property) {
           $camelCasePropriety = 'get'.Utils::dashesToCamelCase($property, true);

           if ($property !== 'id' && method_exists($object, $camelCasePropriety)) {
               $valuesForQuery[$property] = $object->$camelCasePropriety();
           }
       }

       return $this->getInsertQuery($valuesForQuery, $table);

    }

    /**
     * @param array $result
     * @param string $entityName
     * @return mixed
     */
    public function hydrate(array $result, string $entityName): mixed
    {
        $class = new $entityName();

        foreach ($result as $key => $value) {

            $camelCasePropriety = 'set'.Utils::dashesToCamelCase($key, true);

            if (method_exists($class, $camelCasePropriety)) {
                $class->$camelCasePropriety($value);
            }
        }

        return $class;
    }

    private function getColumnsOfInformationSchema(string $tableName): array
    {
        $con = $this->getConnection();

        $sql = " SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = N'{$tableName}' ";

        $stmt = $con->prepare($sql);

        $stmt->execute();

        $results = $stmt->get_result();

        $arrayOfResult = [];

        foreach ($results as $result) {
            $arrayOfResult[] = $result["COLUMN_NAME"];
        }

       return $arrayOfResult;

    }

    public function getInsertQuery(array $propertiesAndValues , string  $tableName): string
    {
        $sql = "INSERT INTO {$tableName} (";

        $columns = '';

        $values = '';


        foreach ($propertiesAndValues as $property => $value) {
            $columns .= $property . ',';

            if ($value) {
                $values .= "'$value'" . ',';
            } else {
                $values .= "NULL,";
            }
        }

        $newColumns = rtrim($columns,',');

        $newValues = rtrim($values, ',');

        $sql .= $newColumns.')'.' VALUES ('.$newValues.')';

        return $sql;

    }


    public function flush(string $sql): void
    {
        $con = $this->getConnection();

        $stmt = $con->prepare($sql);

        $stmt->execute();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return new QueryBuilder();
    }









}