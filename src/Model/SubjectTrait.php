<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class SubjectTrait extends IntermediateDatabaseEntity {
    
    // TODO @var array<DatabaseEntityProperty>
    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("trait_id",      null,   DatabaseEntityPropertyType::EXTERNAL_DATA,  null,   false,  null),
            new DatabaseEntityProperty("subject_id",    null,   DatabaseEntityPropertyType::EXTERNAL_DATA,  null,   false,  null)
        ];
    }

    public static function getTableName(): string {
        return "SubjectsTraits";
    }
}
