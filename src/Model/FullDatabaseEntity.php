<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class FullDatabaseEntity extends DatabaseEntity {
    /**
     * @var int
     */
    public $id = 0;

    public function __construct(Database $database) {
        parent::__construct($database);
    }

    public static function get(Database $database, $id) : mixed {
        $row = $database->fetchSingle(sprintf("
            SELECT
                *
            FROM %s
            WHERE
                id = :id
        ", static::getDatabaseName()), [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        
        return static::fromDatabaseRow($database, $row);
    }
    
    public static function getAll(Database $database): mixed {
        // TODO nefunguje pro _Class
        $rows = $database->fetchMultiple(sprintf("
        SELECT
            *
        FROM %s
        ", static::getDatabaseName()));
        
        if ($rows === false) {
            return null;
        }
        
        return array_map(function (array $row) use($database) {
            return static::fromDatabaseRow($database, $row);
        }, $rows);
    }

    abstract public static function getSelectOptions(Database $database): array;
    
    public function write(): void {
        $parameters = [];
        foreach ($this->getProperties() as &$property) {
            array_push($parameters, new DatabaseParameter(
                $property->name, 
                $property->serialize($this->getProperty($property->name)))
                // TODO: Různé datové typy?
            );
        }

        if($this->id === 0) {
            $attributeNames = "";
            $valueNames = "";
            foreach($this->getProperties() as $index => $value) {
                $attributeNames .= $value->name;
                $valueNames .= ":" . $value->name;

                if($index != count($this->getProperties()) - 1) {
                    $attributeNames .= ", ";
                    $valueNames .= ",";
                }
            }
            
            $this->database->execute(sprintf("
                INSERT INTO %s (
                    %s
                )
                VALUES (
                    %s
                )
            ", $this->getDatabaseName(), $attributeNames, $valueNames),
            $parameters);

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
            array_push($parameters, new DatabaseParameter("id", $this->id));
            
            $valuesIndices = "";
            foreach($this->getProperties() as $index => $value) {
                $valuesIndices .= $value->name . " = :" . $value->name;

                if($index != count($this->getProperties()) - 1) {
                    $valuesIndices .= ", ";
                }
            }
            
            $this->database->execute(sprintf("
                UPDATE %s
                SET
                    %s
                WHERE
                    id = :id
            ", $this->getDatabaseName(), $valuesIndices),
            $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute(sprintf("
            DELETE FROM %s
            WHERE
                id = :id
            LIMIT 1
        ", $this->getDatabaseName()), [
            new DatabaseParameter("id", $this->id),
        ]);
    }

    protected static function fromDatabaseRow(Database $database, array $row): mixed {
        // Zkontroluj délku dané řady.
        // TODO neprojížděj ty property, které jsou intermediate
        if (count($row) !== count(static::getProperties()) + 1) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new static($database);
        $object->id = $row[0];
        // TODO neprojížděj ty property, které jsou intermediate
        foreach(static::getProperties() as $index => $value) {
            $object->setProperty($value->name, $value->deserialize($row[$index + 1])); // Atributy musí být ve stejném pořadí jak v deklaraci modelu, tak v databázi
        }
        
        return $object;
    }

}
