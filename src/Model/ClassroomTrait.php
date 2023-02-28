<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ClassroomTrait extends IntermediateDatabaseEntity implements FormattableDatabaseEntity {

    public static function getProperties(): array {
        return [
            // TODO implementace intermediate modelu "getProperties()"
            new DatabaseEntityProperty("trait_id", "trait_id", DatabaseEntityPropertyType::Intermediate_data, true, false, null),
            new DatabaseEntityProperty("classroom_id", "classroom_id", DatabaseEntityPropertyType::Intermediate_data, true, false, null),
        ];
    }

    public function getFormatted(): string {
        return $this->nazev; // TODO cool a kde ho vezme?
    }

    static public function getForClassroom(Database $database, int $classroom_id): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM ClassroomsTraits
            WHERE
                classroom_id = :classroom_id
        ", [
            new DatabaseParameter("classroom_id", $classroom_id),
        ]);

        return array_map(function (array $row) use ($database) {
            return ClassroomTrait::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
