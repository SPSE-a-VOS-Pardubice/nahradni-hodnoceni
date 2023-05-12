<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class DatabaseEntity {
    protected Database $database;

    // array<string, mixed>
    protected array $properties = [];

    public function __construct(Database $database) {
        $this->database = $database;
        
        foreach ($this->getProperties() as &$property) {
            if ($property->type !== DatabaseEntityPropertyType::INTERMEDIATE_DATA)
                $this->setProperty($property->name, $property->defaultValue);
        }
        
    }

    /**
     * Handle external `set` call of a database property.
     */
    public function __set(string $name, $value): void {
        $this->setProperty($name, $value);
    }

    public function __get(string $name): mixed {
        return $this->getProperty($name);
    }

    /**
     * @return array<DatabaseEntityProperty>
     */
    public static abstract function getProperties(): array;

    /**
     * Internal method for handling all database `set` calls.
     */
    protected function setProperty(string $key, mixed $value): void {
        $properties = static::getProperties();
        foreach ($properties as $property) {
            if($property->name === $key) {
                $property->checkType($value);
                break;
            }
        }
        
        $this->properties[$key] = $value;
    }

    protected function getProperty(string $key): mixed {
        return $this->properties[$key];
    }

    /**
     * Write this object to the database. Either update the row or create new if it does not already exist (when `id` equals zero or is empty in case of string).
     */
    abstract public function write(): void;

    /**
     * Remove the object from the database. Works only with non-default `id` values.
     */
    abstract public function remove(): void;

    /**
     * Vrací název tabulky odpovídající modelu.
     */
    abstract public static function getTableName(): string;

    abstract protected static function fromDatabase(Database $database, array $row): DatabaseEntity;
    
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
     *     70: "Operační systémy"
     *   },
     * }
     * ```
     * 
     * Tato metoda pracuje s getProperties a s daty z databáze.
     */
    public static function getAvailableOptions(Database $database): array {
        $availableOptions = [];

        foreach (static::getProperties() as $prop) {
            if ($prop->selectOptionsSource !== null) {
                // pokud je $selectOptionsSource array nastav ho 
                if (gettype($prop->selectOptionsSource) === "array") {
                    $availableOptions[$prop->name] = $prop->selectOptionsSource;
                // pokud je $selectOptionsSource string aka classa:
                // a pokud se jedná o EXTERNAL DATA tak...
                } else if (gettype($prop->selectOptionsSource) === "string" && $prop->type === DatabaseEntityPropertyType::EXTERNAL_DATA) {
                    // načti vše co dokážeš 
                    $asocMap = [];

                    foreach ($prop->selectOptionsSource::getAll($database) as $oneRecord) {
                        $asocMap[$oneRecord->id] = $oneRecord->getFormatted();
                    }

                    $availableOptions[$prop->name] = $asocMap;
                }
            }
        }

        return $availableOptions;
    }

    public static function getRestricted(Database $database, array $restrictions): ?static {
        $results = static::getRestrictedAll($database, $restrictions, 1);
        if (count($results) === 0)
            return null;
        return $results[0];
    }

    /**
     * @var array<FullDatabaseEntity> omezení, podle kterých se vyfitrují záznamy z mezitabulky
     * @return array<IntermediateDatabaseEntity> vyfiltrované záznamy z mezitabulky
     * 
     * $restrictions je pole restrikcí `Restriction[]`
     */
    public static function getRestrictedAll(Database $database, array $restrictions, int $limit = 0): array {
        // TODO obecná metoda pro získání záznamů pomocí restrikce
        // blokováno QueryBuilderem
        //return [];

        // TODO předběžná implementace předělat s příchodem QB
        $restrictionString = "";
        $restrictionsValues = [];
        foreach ($restrictions as $restriction) {
            $restrictionString .= $restriction->propertyName . " = :" . $restriction->propertyName;
            $restrictionsValues[] = new DatabaseParameter($restriction->propertyName, $restriction->value);
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
