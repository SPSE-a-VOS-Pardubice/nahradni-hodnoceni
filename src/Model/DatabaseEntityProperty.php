<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class DatabaseEntityProperty {
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var DatabaseEntityPropertyType
     */
    public $type;

    /**
     * @var bool
     */
    public $isSelect;

    /**
     * @var bool
     */
    public $isNullable;

    /**
     * @var mixed
     */
    public $defaultValue;

    public function __construct($name, $displayName, $type, $isSelect, $isNullable, $defaultValue) {
        $this->name = $name;
        $this->displayName = $displayName;
        $this->type = $type;
        $this->isSelect = $isSelect;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
    }

    public function serialize($value): mixed {
        if($this->type == DatabaseEntityPropertyType::DateTime) {
            return $value->format("Y-m-d H:i:s");
        } else {
            return $value;
        }
    }

    public function deserialize($value): mixed {
        if($this->type == DatabaseEntityPropertyType::DateTime) {
            return new \DateTime($value);
        } else {
            return $value;
        }
    } 
}