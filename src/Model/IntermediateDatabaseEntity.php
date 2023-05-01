<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class IntermediateDatabaseEntity extends DatabaseEntity {

    private bool $fromDB = false;

    public function __construct(Database $database) {
        parent::__construct($database);
    }

    // update updatne pouze ty hodnoty, které nejsou souučástí prymárního klíče
    // TODO: otestovat chování upratu při změně složek primárního klíče 
    // šlo by tomu zabránit přepsáním magických settrů pro External propety
    public function write(): void {
        $parameters = [];
        
        $properties = array_filter(static::getProperties(), function (DatabaseEntityProperty $property) {
            return $property->type !== DatabaseEntityPropertyType::INTERMEDIATE_DATA;
        });

        if ($this->fromDB) {
            $properties = array_filter(static::getProperties(), function (DatabaseEntityProperty $property) {
                return $property->type !== DatabaseEntityPropertyType::EXTERNAL_DATA;
            });
        }
        
        foreach ($properties as &$property) {
            array_push($parameters, new DatabaseParameter(
                $property->name, 
                $property->serialize($this->getProperty($property->name)))
            );
        }

        // pokud je `$fromDB` false tak insertuješ jinak updatuješ
        if ($this->fromDB) {
            $valuesIndices = "";
            foreach($properties as $index => $value) {
                $valuesIndices .= $value->name . " = :" . $value->name;

                if($index != count($properties) - 1) {
                    $valuesIndices .= ", ";
                }
            }

            $keyAtributes = "";
    
            // věechy External data vem jako součást id
            foreach ($this->getProperties() as $property) {
                if ($property->type === DatabaseEntityPropertyType::EXTERNAL_DATA) {
                    $keyAtributes .= $property->name . " = :" . $property->name;
                    array_push($parameters, new DatabaseParameter(
                        $property->name, 
                        $property->serialize($this->getProperty($property->name)))
                    );
                }
            }    
            
            $this->database->execute(sprintf("
                UPDATE %s
                SET
                    %s
                WHERE
                    %s
            ", $this->getTableName(), $valuesIndices, $keyAtributes),
            $parameters);
        } else {
            $attributeNames = "";
            $valueNames = "";
            foreach($properties as $index => $value) {
                $attributeNames .= $value->name;
                $valueNames .= ":" . $value->name;

                if($index != count($properties) - 1) {
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
        }
    }


    public function remove(): void {

        $keyAtributes = "";
        $key = [];

        // věechy External data vem jako součást id
        foreach ($this->getProperties() as $property) {
            if ($property->type === DatabaseEntityPropertyType::EXTERNAL_DATA) {
                $keyAtributes .= $property->name . " = :" . $property->name;
                array_push($key, new DatabaseParameter(
                    $property->name, 
                    $property->serialize($this->getProperty($property->name)))
                );
            }
        }

        $this->database->execute(sprintf("
            DELETE FROM %s
            WHERE
                %s
            LIMIT 1
        ", $this->getTableName(), $keyAtributes), $key);
    }

    /**
     * 
     * 
     * Zkonstruuje instanci modelu podle dat z databáze.
     */
    protected static function fromDatabase(Database $database, array $row): IntermediateDatabaseEntity {
        // Zkontroluj délku dané řady.
        $properties = array_filter(static::getProperties(), function (DatabaseEntityProperty $property) {
            return $property->type !== DatabaseEntityPropertyType::INTERMEDIATE_DATA;
        });

        if (count($row) !== count($properties)) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new static($database);
        $object->fromDB = true;

        // neprojížděj ty property, které jsou intermediate
        foreach($properties as $index => $value) {
            $object->setProperty($value->name, $value->deserialize($row[$index + 1])); 
            // Atributy musí být ve stejném pořadí jak v deklaraci modelu, tak v databázi
        }
        
        return $object;
    }

    // TODO vytvořít statické metody pro manipulaci se záznamy při POSTu (updateData)
    // nestačí jen crud? 

    /**
     * @return ?string název vlastnosti, která se vztahuje k dané třídě
     * 
     * probléby metody, vrátí pouze první výskyt, nepočítá s tím, že jedna
     * mezitabulka odkazuje dvakrát do stejné tabulky
     */
    public static function getPropNameFromClass(string $class): ?string {
        $properties = array_filter(static::getProperties(), function (DatabaseEntityProperty $property) {
            return $property->type !== DatabaseEntityPropertyType::EXTERNAL_DATA;
        });

        foreach ($properties as $property) { 
            if ($property->selectOptionsSource === $class) {
                return $property->name;
            }
        }

        return null;
    }
  
    /**
     * @var array<FullDatabaseEntity> omezení, podle kterých se vyfitrují záznamy z mezitabulky
     * @return array<IntermediateDatabaseEntity> vyfiltrované záznamy z mezitabulky
     * 
     * $restrictions je pole restrikcí `Restriction[]`
     */
    public static function getRestricted(Database $database, array $restrictions): array {
        // TODO obecná metoda pro získání záznamů pomocí restrikce
        // blokováno QueryBuilderem
        //return [];

        // TODO předběžná implementace předělat s příchodem QB
        $restrictionString = "";
        $restrictionsValues = [];
        foreach ($restrictions as $restriction) {
            $restrictionString .= $restriction->propertyName . " = :" . $restriction->propertyName;
            $restrictionsValues[$restriction->propertyName] = $restriction->value;
        }

        $rows = $database->fetchMultiple(sprintf("
        SELECT
            *
        FROM %s
        WHERE %s", static::getTableName(), $restrictionString), $restrictionsValues);

        if ($rows === false) {
            return [];
        }
        
        return array_map(function (array $row) use($database) {
            return static::fromDatabase($database, $row);
        }, $rows);
    }
}