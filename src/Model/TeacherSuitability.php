<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class TeacherSuitability extends DatabaseEntity implements FormattableDatabaseEntity {
    private int $subject_id;
    private int $teacher_id;

    
    protected function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public function getFormatted(): string {
        return $this->nazev;
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 2) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new ClassroomTrait($database);
        $object->setProperty("subject_id",        intval($row[0]));
        $object->setProperty("teacher_id",    intval($row[1]));
        return $object;
    }
    
    public function write(): void {
        $parameters = [
            new DatabaseParameter("subject_id", $this->subject_id),
            new DatabaseParameter("teacher_id", $this->teacher_id),
        ];

        $this->database->execute("
            INSERT INTO TeachersSuitability (
                subject_id,
                teacher_id
            )
            VALUES (
                :subject_id,
                :teacher_id
        )", $parameters);
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM TeachersSuitability
            WHERE
                subject_id = :subject_id,
                teacher_id = :teacher_id
            LIMIT 1
        ", [
            new DatabaseParameter("subject_id", $this->subject_id),
            new DatabaseParameter("teacher_id", $this->teacher_id),
        ]);
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
}