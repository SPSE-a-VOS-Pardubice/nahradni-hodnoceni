<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ClassroomTrait extends DatabaseEntity implements FormattableDatabaseEntity {

    public function getFormatted(): string {
        // TODO
        return "";
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
