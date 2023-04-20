<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class FullDatabaseEntity extends DatabaseEntity {
    
    public int $id = 0;

    public function __construct(Database $database) {
        parent::__construct($database);
    }

    abstract public function getFormatted(): string;

    public static function get(Database $database, $id) : ?FullDatabaseEntity {
        $row = $database->fetchSingle(sprintf("
            SELECT
                *
            FROM %s
            WHERE
                id = :id
        ", static::getTableName()), [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        
        return static::fromDatabase($database, $row);
    }
    
    // TODO @returns ?array<FullDatabaseEntity>
    public static function getAll(Database $database): array {
        $rows = $database->fetchMultiple(sprintf("
        SELECT
            *
        FROM %s
        ", static::getTableName()));
        
        if ($rows === false) {
            return null;
        }
        
        return array_map(function (array $row) use($database) {
            return static::fromDatabase($database, $row);
        }, $rows);
    }
    

    /**
     * Propíše data z instance do databáze.
     * 
     * Jedná se pouze o jednoduché typy.
     */
    public function write(): void {
        $parameters = [];
        // TODO z parametrů je třeba vyloučit INTERMEDIATE DATA
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
            ", $this->getTableName(), $attributeNames, $valueNames),
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
            ", $this->getTableName(), $valuesIndices),
            $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute(sprintf("
            DELETE FROM %s
            WHERE
                id = :id
            LIMIT 1
        ", $this->getTableName()), [
            new DatabaseParameter("id", $this->id),
        ]);
    }


    /**
     * 
     * 
     * Zkonstruuje instanci modelu podle dat z databáze.
     */
    protected static function fromDatabase(Database $database, array $row): FullDatabaseEntity {
        // Zkontroluj délku dané řady (+id).
        $properties = array_filter(static::getProperties(), function (DatabaseEntityProperty $property) {
            return $property->type !== DatabaseEntityPropertyType::INTERMEDIATE_DATA;
        });

        if (count($row) !== count($properties) + 1) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new static($database);
        $object->id = $row[0];

        // neprojížděj ty property, které jsou intermediate
        foreach($properties as $index => $value) {
            $object->setProperty($value->name, $value->deserialize($row[$index + 1])); // Atributy musí být ve stejném pořadí jak v deklaraci modelu, tak v databázi
        }
        
        return $object;
    }

    
    /**
     * Aktualizace dat.
     * 
     * Metoda je volána při postu dat a stará se o aktualizaci svého modelu i záznamů mezitabulek v DB.
     */
    public static function updateData(Database $database, array $row): void {
        // TODO zamyslet se nad přesunutím do DatabaseEntity
    }

    /**
     * Získej všechny instance IntermediateDatabaseEntity, které uchovávají reference (ve formě id) na instanci, na které je volána tato metoda 
     * 
     * Účelem této metody je získání n ku n dat z db 
     * 
     * Vrací asociativní mapu název proměné a pole instancí 
     * 
     * př. 
     * 
     * ```
     * teacher.getIntermediateData() => {
     *      "subjects": [
     *          "availableOptions": [ // zavoláno na třídě TeacherSuitability
     *              "subject_id": [
     *                  0: "ČJ"
     *                  1: "MA"
     *              ] 
     *              "suitability": [
     *                  "vhodny",
     *                  "nahovno"  
     *              ]
     *          ],
     *          "data": [ 
     *              instance TeacherSuitability {subject_id:1,teacher_id:1,suitability:"vhodny"}
     *              instance TeacherSuitability {subject_id:2,teacher_id:1,suitability:"vhodny"}
     *              instance TeacherSuitability {subject_id:3,teacher_id:1,suitability:"vhodny"}
     *      ]
     *  }
     * ```
     * 
     * Tato metoda pracuje s daty z modelu.
     */
    // TODO
    public function getIntermediateData(): array {
        $intermediateData = [];

        foreach ($this->getProperties() as $prop) {
            $intProps = [];
            if ($prop->type === DatabaseEntityPropertyType::INTERMEDIATE_DATA) {

                $intProps["availableOptions"] = $prop->selectOptionsSource::getAvailableOptions($this->database);

                // TODO nefunguje metoda getRestricted
                // TODO doplnit druhý parametr
                // TODO je třeba promyslet jak z mezitabulky získat jen záznamy vážícíse k instanci, na které byla metoda zavolána
                $intProps["data"] = $prop->selectOptionsSource::getRestricted($this->database, [$this]);
                
                $intermediateData[$prop->name] = $intProps;
            }
        }

        return $intermediateData;
    }

}
