<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class DatabaseEntityProperty {

    public string $name;

    public string $displayName;

    public DatabaseEntityPropertyType $type;

    /**
     * Tato vlastnost poskytuje **možnosti** pro <select> prvky.
     * - `null` - nereprezentuje <select>
     * - `array` - instance reprezentuje hodnotu, uživatel si může vybrat z předdefinovaných možností v selectu
     * - `string` (ve skutečnosti `__CLASS__`) - instance reprezentuje referenci na jinou tabulku, uživatel si může vybrat jakýkoliv záznam z externí tabulky
     */
    public null | array | string $selectOptionsSource;

    public bool $isNullable;

    public mixed $defaultValue;

    public function __construct(string $name, ?string $displayName, DatabaseEntityPropertyType $type, null | array | string $selectedOption, bool $isNullable, mixed $defaultValue) {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->selectedOption = $selectedOption;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
    }

    /**
     * Serializuje danou hodnotu pro SQL dotaz (`\PDO`).
     */
    public function serialize($value): ?string {
        if($value === null) {
            return null; // TODO: test
        }
        
        switch($this->type) {
            case DatabaseEntityPropertyType::DateTime:
                return $value->format("Y-m-d H:i:s");
            default:
                return $value;
        }
    }

    /**
     * Deserializuje hodnotu z výstupu z SQL databáze (`\PDO`).
     */
    public function deserialize(string $value): mixed {
        if($value === null) {
            return null;
        }
        
        switch($this->type) {
            case DatabaseEntityPropertyType::DateTime:
                return new \DateTime($value);
            case DatabaseEntityPropertyType::Integer:
                return intval($value);
            default:
                return $value;
        }
    } 
}
