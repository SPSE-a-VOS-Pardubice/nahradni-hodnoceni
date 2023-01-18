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
            new DatabaseParameter("subject_id",       $this->subject_id),
            new DatabaseParameter("teacher_id",   $this->teacher_id),
        ];

        if($this->id === 0) {
            $this->database->execute("
            INSERT INTO TeachesSuitability (
                subject_id
                teacher_id
            )
            VALUES (
                :subject_id
                :teacher_id
            )", $parameters);
        }else{
            // TODO: TOTO JE BLBOST ALE SYTUACE BY NEMĚLA NASTAT
            $this->database->execute("
            UPDATE TeachesSuitability
            SET
                subject_id = :subject_id
                teacher_id = :teacher_id
            WHERE
                id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM TeachesSuitability
            WHERE
                subject_id = :subject_id,
                teacher_id = :teacher_id
            LIMIT 1
        ", [
            new DatabaseParameter("subject_id",       $this->subject_id),
            new DatabaseParameter("teacher_id",   $this->teacher_id),
        ]);
    }

    static public function getForTeacher(Database $database, int $teacher_id): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM TeachesSuitability
            WHERE
                teacher_id = :teacher_id
        ", [
            new DatabaseParameter("teacher_id", $teacher_id),
        ]);

        return array_map(function (array $row) use ($database) {
            $teacherSuitability = TeacherSuitability::fromDatabaseRow($database, $row);

            return [$teacherSuitability->subject_id => $teacherSuitability];
        }, $rows);
    }
    
    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $teachersSuitability = [];

        foreach ($data as $key => $value) {
            if (preg_match("^subject-[0-9]*$", $key)) {
                $teacherSuitability = new TeacherSuitability($database);
                $teacherSuitability->setProperty("subject_id",        intval($value));
                $teachersSuitability[] = $teacherSuitability;
            }
        }

        return $teachersSuitability;
    }

    public static function applyPostData(array $models): void {

        $teacher = $models[0];

        $teacher->write();
    }
}