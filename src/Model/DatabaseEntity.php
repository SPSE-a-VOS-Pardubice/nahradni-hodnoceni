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
        
        static::getProperties()[$key]->checkType($value);
        
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
}
