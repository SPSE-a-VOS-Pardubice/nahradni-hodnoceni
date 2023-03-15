<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class TeacherSuitability extends IntermediateDatabaseEntity implements FormattableDatabaseEntity {
    /**
     * @var array<DatabaseEntityProperty>
     */
    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("subject_id", null, DatabaseEntityPropertyType::Intermediate_data, true, false, null),// TODO intermediated data
            new DatabaseEntityProperty("teacher_id", null, DatabaseEntityPropertyType::Intermediate_data, true, false, null)// TODO intermediated data
        ];
    }

	/**
	 * @return string
	 */
    public function getFormatted(): string {
        return $this->nazev; // TODO cool a kde ho vezme?
    }

    static public function getForTeacher(Database $database, int $teacher_id): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM TeachersSuitability
            WHERE
                teacher_id = :teacher_id
        ", [
            new DatabaseParameter("teacher_id", $teacher_id),
        ]);

        return array_map(function (array $row) use ($database) {
            return TeacherSuitability::fromDatabaseRow($database, $row);
        }, $rows);
    }

    public static function getDatabaseName(): string {
        return "teacherssuitability";
    }
}