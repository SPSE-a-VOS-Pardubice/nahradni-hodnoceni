<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class SubjectTrait extends IntermediateDatabaseEntity implements FormattableDatabaseEntity {
    /**
     * @var array<DatabaseEntityProperty>
     */
    public static function getProperties(): array {
        // TODO
        return [];
    }
    public function getFormatted(): string {
        //return $this->nazev;
        // TODO
        return "";
    }

    static public function getForSubject(Database $database, int $subject_id): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM SubjectsTraits
            WHERE
                subject_id = :subject_id
        ", [
            new DatabaseParameter("subject_id", $subject_id),
        ]);

        return array_map(function (array $row) use ($database) {
            return SubjectTrait::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
