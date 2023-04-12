<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class DatabaseEntity {
    protected Database $database;

    // array<string, mixed>
    protected array $properties;

    public function __construct(Database $database) {
        $this->database = $database;
        
        foreach ($this->getProperties() as &$property) {
            $this->setProperty($property->name, null);
        }
        
        // TODO initialize with default values
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

    // TODO @return array<DatabaseEntityProperty>
    public static abstract function getProperties(): array;

    /**
     * Internal method for handling all database `set` calls.
     */
    protected function setProperty(string $key, mixed $value): void {
        
        // TODO check type
        
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
     * Získej vybrané možnosti pro specifickou instanci.
     * 
     * Vrací mapu kde klíč je název vlastnosti a hodnota je pole id.
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
}
