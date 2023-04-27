<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class SubjectTrait extends IntermediateDatabaseEntity {
    
    /**
     *  @return array<DatabaseEntityProperty>
     */
    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("trait_id",      "Příznak",   DatabaseEntityPropertyType::EXTERNAL_DATA,  _Trait::class,   false,  null),
            new DatabaseEntityProperty("subject_id",    "Předmět",   DatabaseEntityPropertyType::EXTERNAL_DATA,  Subject::class,   false,  null)
        ];
    }

    public static function getTableName(): string {
        return "SubjectTraits";
    }
}
