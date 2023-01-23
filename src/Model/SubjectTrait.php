<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class SubjectTrait extends DatabaseEntity implements FormattableDatabaseEntity {
    private int $trait_id;
    private int $subject_id;

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
        $object->setProperty("trait_id",    intval($row[0]));
        $object->setProperty("subject_id",  intval($row[1]));
        return $object;
    }
    

    public function write(): void {
        $parameters = [
            new DatabaseParameter("trait_id",   $this->trait_id),
            new DatabaseParameter("subject_id", $this->subject_id),
        ];

        $this->database->execute("
            INSERT INTO SubjectsTraits (
                trait_id
                subject_id
            )
            VALUES (
                :trait_id
                :subject_id
        )", $parameters);
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM SubjectsTraits
            WHERE
                trait_id    = :trait_id,
                subject_id  = :subject_id
            LIMIT 1
        ", [
            new DatabaseParameter("trait_id",   $this->trait_id),
            new DatabaseParameter("subject_id", $this->subject_id),
        ]);
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
            $classroomTrait = ClassroomTrait::fromDatabaseRow($database, $row);

            return [$classroomTrait->trait_id => $classroomTrait];
        }, $rows);
    }
    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $classroomTraits = [];

        foreach ($data as $key => $value) {
            if (preg_match("/^trait-[0-9]*$/", $key)) {
                $classroomTrait = new ClassroomTrait($database);
                $classroomTrait->setProperty("trait_id",        intval($value));
                $classroomTraits[] = $classroomTrait;
            }
        }

        return $classroomTraits;
    }

    public static function applyPostData(array $models): void {

        $trait = $models[0];

        $trait->write();
    }
}
