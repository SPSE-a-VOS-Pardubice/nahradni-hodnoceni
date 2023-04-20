<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class TeacherSuitability extends IntermediateDatabaseEntity {
    /**
     * @var array<DatabaseEntityProperty>
     */
    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("subject_id", "Předmět", DatabaseEntityPropertyType::EXTERNAL_DATA, Subject::class, false, null),
            new DatabaseEntityProperty("teacher_id", "Učitel", DatabaseEntityPropertyType::EXTERNAL_DATA, Teacher::class, false, null),
            new DatabaseEntityProperty("suitability", "Vhodnost", DatabaseEntityPropertyType::STRING, ["vhodny", "nevhodny"], false, null)
        ];
    }

    public static function getTableName(): string {
        return "TeacherSuitabilities";
    }
}