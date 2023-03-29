<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class DatabaseEntityProperty {

    public string $name;

    public string $displayName;

    public DatabaseEntityPropertyType $type;

    /**
     * Tato vlastnost poskytuje možnosti pro <select> prvky.
     * - null - 
     */
    public null | array | string $selectOptionsSource;

    public bool $isNullable;

    public mixed $defaultValue;

    public function __construct($name, $displayName, $type, $selectedOption, $isNullable, $defaultValue) {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->selectedOption = $selectedOption;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
    }

    public function serialize($value): mixed {
        if($value === null) {
            return null; // TODO: Test
        }
        
        switch($this->type) {
            case DatabaseEntityPropertyType::DateTime:
                return $value->format("Y-m-d H:i:s");
            default:
                return $value;
        }
    }

    public function deserialize($value): mixed {
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