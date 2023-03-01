<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class FullDatabaseEntity extends DatabaseEntity {
    /**
     * @var int
     */
    public $id;

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
        ", self::getDatabaseName()), [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        
        return self::fromDatabaseRow($database, $row);
    }
    
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
            ", $this->getDatabaseName(), $attributeNames, $valueNames), [
                $parameters,
            ]);

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
            ", $this->getDatabaseName(), $valuesIndices), [
                $parameters,
            ]);
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
        if (count($row) !== count(self::getProperties())) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new static($database);
        foreach(self::getProperties() as $index => $value) {
            $object->setProperty($value->name, $value->deserialize($row[$index])); // Atributy musí být ve stejném pořadí jak v deklaraci modelu, tak v databázi
        }
        
        return $object;
    }
}
