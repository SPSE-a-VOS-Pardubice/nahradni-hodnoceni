<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ClassroomTrait extends IntermediateDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("trait_id", "Ročník", DatabaseEntityPropertyType::EXTERNAL_DATA, _Trait::class, false, null), 
            new DatabaseEntityProperty("classroom_id", "Učebna", DatabaseEntityPropertyType::EXTERNAL_DATA, Classroom::class, false, null)
        ];
    }

    public static function getTableName(): string {
        return "ClassroomsTraits";
    }
}