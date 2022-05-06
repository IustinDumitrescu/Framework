<?php
namespace App\Repository;

use App\Database\Database;
use App\Interfaces\EntityManagerInterface;
use ReflectionClass;


class EntityManager implements EntityManagerInterface
{

    public function getConnection(): \mysqli
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

    public function findBy(string $entityName , array $criteries)
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

        $result = $stmt->get_result()->fetch_assoc();

        if ($result !== null) {
            return $this->hydrate($result, $entityName);
        }

        return null;
    }

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
           $camelCasePropriety = 'get'.$this->dashesToCamelCase($property, true);

           if ($property !== 'id' && method_exists($object, $camelCasePropriety)) {
               $valuesForQuery[$property] = $object->$camelCasePropriety();
           }

       }

       return $this->getInsertQuery($valuesForQuery, $table);

    }

    /**
     * @param array $result
     * @param $entityName
     * @return mixed
     */
    public function hydrate(array $result, string $entityName)
    {
        $class = new $entityName();

        foreach ($result as $key => $value) {

            $camelCasePropriety = 'set'.$this->dashesToCamelCase($key, true);

            $class->$camelCasePropriety($value);
        }

        return $class;
    }

    private function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
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
            $columns .= $property.',';

            $values .= "'$value'".',';
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









}