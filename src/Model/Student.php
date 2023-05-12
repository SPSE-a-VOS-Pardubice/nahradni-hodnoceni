<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Student extends FullDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("name", "Jméno", DatabaseEntityPropertyType::STRING, null, false, ""),
            new DatabaseEntityProperty("surname", "Příjmení", DatabaseEntityPropertyType::STRING, null, false, ""),
            new DatabaseEntityProperty("class_id", "Třída", DatabaseEntityPropertyType::EXTERNAL_DATA, _Class::class, false, 0) 
        ];
    }

    public function getFormatted(): string {
        return sprintf("%s %s", $this->surname, $this->name);
    }

    public function getPropertyValues(): array {
        $res = [];
        for ($i = 0; $i < count($this->getProperties()); $i++) {
            $res[] = $this->getProperty($this->getProperties()[$i]->name);
        }

        return $res;
    }

    public function setPropertyValues($properties): void {
        for ($i = 0; $i < count($properties); $i++) {
            $this->setProperty($this->getProperties()[$i]->name, $properties[$i]);
        }
    }

    public static function getTableName(): string {
        return "Students";
    }
}