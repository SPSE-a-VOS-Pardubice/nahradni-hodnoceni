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
        // TODO nefunguje pro _Class
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
        // TODO neprojížděj ty property, které jsou intermediate
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
        // TODO 
    }


    /**
     * Získej možnosti pro danou třídu.
     * 
     * Vrací mapu kde klíč je název vlastnosti a hodnota je mapa id => formátovaná hodnota.
     * Metoda se používá pro vypsání možností u selectů.
     * 
     * ```
     * {
     *   "subjects": {
     *     69: "Matematika",
     *   },
     * }
     * ```
     * 
     * Tato metoda pracuje s getProperties a s daty z databáze.
     */
    public static function getAvailableOptions(Database $database): array {
        // TODO
        $availableOptions = [];

        foreach (static::getProperties() as $prop) {
            if ($prop->selectOptionsSource !== null) {
                // pokud je $selectOptionsSource array nastav ho 
                if (gettype($prop->selectOptionsSource) === "array") {
                    $availableOptions[$prop->name] = $prop->selectOptionsSource;
                // pokud je $selectOptionsSource string aka classa:
                } else if (gettype($prop->selectOptionsSource) === "string") {
                    // je to fulldatabase entitiy
                        // ano načti vše co dokážeš a nastav je
                        // ne // TODO jak se odkázat na tu správnou FullDatabaseEntity (nechceš hodit do selectu hodnotu z intermediate tabulky)
                }
                    
            }
        }

        return $availableOptions;
    }


    /**
     * Získej vybrané možnosti pro specifickou instanci.
     * 
     * Vrací mapu kde klíč je název vlastnosti a hodnota je mapa id => naformátovaná hodnota.
     * Metoda se používá pro vypsání **vybraných** možností u selectů.
     * 
     * Tato metoda pracuje s daty z modelu.
     */
    public function getSelectedOptions(): array {
        // TODO
        $selectedOptions = [];

        foreach ($this->properties as $prop) {
            // je to fulldatabase entitiy
                // ano načti ji 
            if ($prop->type === DatabaseEntityPropertyType::EXTERNAL_DATA) {
            }
                // ne // TODO jak se odkázat na tu správnou FullDatabaseEntity (nechceš hodit do selectu hodnotu z intermediate tabulky)
            if ($prop->type === DatabaseEntityPropertyType::INTERMEDIATE_DATA) {
                # code...
            }
        }

        return $selectedOptions;
    }
}
