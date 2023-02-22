<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class TeacherSuitability extends IntermediateDatabaseEntity implements FormattableDatabaseEntity {
    /**
     * @var array<DatabaseEntityProperty>
     */
    public static function getProperties(): array {
        // TODO
        return [];
    }

	/**
	 * @return string
	 */
    public function getFormatted(): string {
        //return $this->nazev;
        // TODO
        return "";
    }

    static public function getForTeacher(Database $database, int $teacher_id): array {
        // TODO
        return [];
    }
}