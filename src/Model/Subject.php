<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use Spse\NahradniHodnoceni\Model\SubjectTrait;

class Subject extends FullDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("name",          "Název",    DatabaseEntityPropertyType::STRING,             null,                   false,  ""),
            new DatabaseEntityProperty("abbreviation",  "Zkratka",  DatabaseEntityPropertyType::STRING,             null,                   false,  ""),
            new DatabaseEntityProperty("traits",        "Příznaky", DatabaseEntityPropertyType::INTERMEDIATE_DATA,  SubjectTrait::class,    false, null)
        ];
    }
    
    public static function getTableName(): string {
        return "Subjects";
    }

    public function getFormatted(): string {
        return $this->name;
    }
}
