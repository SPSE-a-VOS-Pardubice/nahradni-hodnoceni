<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Teacher extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity
{
    protected int $id = 0;
    private string $name;
    private string $surname;
    private string $prefix;
    private string $suffix;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void
    {
        $this->$key = $value;
    }

    public static function getProperties(): array
    {
        return [
            new ViewableProperty("id",      "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("name",    "Jméno",    ViewablePropertyType::STRING),
            new ViewableProperty("surname", "Příjmení", ViewablePropertyType::STRING),
            new ViewableProperty("prefix",  "Prefix",   ViewablePropertyType::STRING),
            new ViewableProperty("suffix",  "Suffix",   ViewablePropertyType::STRING),
            new ViewableProperty("subjects", "Vyučované předměty", ViewablePropertyType::INTERMEDIATE_DATA, true)
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $subjects = [];

        foreach (Subject::getAll($database) as $subject) {
            $subjects[$subject->id] = $subject->getFormatted();
        }

        return [
            "subjects" => $subjects,
        ];
    }

    public function getFormatted(): string {
        return sprintf("%s %s %s %s",$this->prefix, $this->name, $this->surname, $this->suffix);
    }

    public function getIntermediateData(): array {
        return [
            "subjects" => array_map(function ($teacherSuitability) {
                    return Subject::get($this->database, $teacherSuitability->subject_id);
                }, TeacherSuitability::getForTeacher($this->database, $this->id)),
            ];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 5) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Teacher($database);
        $object->setProperty("id",      intval($row[0]));
        $object->setProperty("name",    $row[1]);
        $object->setProperty("surname", $row[2]);
        $object->setProperty("prefix",  $row[3]);
        $object->setProperty("suffix",  $row[4]);
        return $object;
    }

    public function write(): void
    {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("name",       $this->name),
            new DatabaseParameter("surname",    $this->surname),
            new DatabaseParameter("prefix",     $this->prefix),
            new DatabaseParameter("suffix",     $this->suffix)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Teachers (
                    name,
                    surname,
                    prefix,
                    suffix
                )
                VALUES (
                    :name,
                    :surname,
                    :prefix,
                    :suffix
                )
            ", $parameters);

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
            array_push($parameters, new DatabaseParameter("id", $this->id));
            $this->database->execute("
                UPDATE Teachers
                SET
                    name    = :name,
                    surname = :surname,
                    prefix  = :prefix,
                    suffix  = :suffix
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void
    {
        $this->database->execute("
            DELETE FROM Teachers
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }

    static public function get(Database $database, string $id): ?Teacher
    {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Teachers
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return Teacher::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array
    {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Teachers
        ");

        return array_map(function (array $row) use ($database) {
            return Teacher::fromDatabaseRow($database, $row);
        }, $rows);
    }
    
    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $teacher = null;
        if ($id > 0) {
            $teacher = Teacher::get($database, strval($id));

            if ($teacher == null) 
                throw new \RuntimeException("Error Processing Request", 1);
        } else {
            $teacher = new Teacher($database);
        }

        $teacher->setProperty("name",    $data["name"]);
        $teacher->setProperty("surname", $data["surname"]);
        $teacher->setProperty("prefix",  $data["prefix"]);
        $teacher->setProperty("suffix",  $data["suffix"]);

        return [$teacher, TeacherSuitability::parsePostData($data, $database)];
    }

    public static function applyPostData(array $models): void {

        $teacher = $models[0];
        $teacherSuitability = $models[1];

        $suitabilityInDB = TeacherSuitability::getForTeacher($teacher->database, $teacher->id);
        $willBeInDB = [];

        foreach ($teacherSuitability as $suitability) {
            if ($suitabilityInDB[$suitability->subject_id] == null) {
                // TODO co když teprve vytvářím? id je 0
                $suitability->teacher_id = $teacher->id;
                TeacherSuitability::applyPostData([$suitability]);
            }

            $willBeInDB[$teacher->teacher_id] = $teacher;
        }

        foreach ($willBeInDB as $suitabilityInDB) {
            if ($willBeInDB[$suitabilityInDB->subject_id] == null) {
                $suitabilityInDB->remove();
            }
        }

        $teacher->write();
    }

    public static function getFromSurname(Database $database, string $surname): Teacher {
        $row = $database->fetchSingle("
            SELECT 
                * 
            FROM teachers 
            WHERE 
                surname = :surname
        ", [
            new DatabaseParameter("surname", $surname)
        ]);

        if ($row === false) {
            throw new \RuntimeException("Učitel s příjmení " . $surname . " nebyl v databázi nalezen");
        }
        return Teacher::fromDatabaseRow($database, $row);
    }
}