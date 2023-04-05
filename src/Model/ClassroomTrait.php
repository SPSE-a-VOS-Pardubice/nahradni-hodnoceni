<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ClassroomTrait extends IntermediateDatabaseEntity
{

    public static function getProperties(): array
    {
        return [
            new DatabaseEntityProperty("trait_id", null, DatabaseEntityPropertyType::EXTERNAL_DATA, _Trait::class, false, null), 
            new DatabaseEntityProperty("classroom_id", null, DatabaseEntityPropertyType::EXTERNAL_DATA, Classroom::class, false, null), 
        ];
    }

    public function getFormatted(): string
    {
        return $this->nazev; // TODO cool a kde ho vezme?
    }

    static public function getForClassroom(Database $database, int $classroom_id): array
    {
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
            return ClassroomTrait::fromDatabase($database, $row);
        }, $rows);
    }

    public static function getTableName(): string
    {
        return "ClassroomsTraits";
    }
}