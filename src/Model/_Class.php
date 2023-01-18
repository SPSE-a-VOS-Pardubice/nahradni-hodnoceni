<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class _Class extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {
    protected int $id = 0;
    private int $year;
    private int $grade;
    private string $label;
    private int $class_teacher_id;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",                  "ID",               ViewablePropertyType::INTEGER),
            new ViewableProperty("year",                "Rok",              ViewablePropertyType::INTEGER),
            new ViewableProperty("grade",               "Ročník",           ViewablePropertyType::INTEGER),
            new ViewableProperty("label",               "Označení",         ViewablePropertyType::STRING),
            new ViewableProperty("class_teacher_id",    "Třídní učitel",    ViewablePropertyType::INTEGER,  true),
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $class_teacher_id = [];

        foreach(Teacher::getAll($database) as $teacher) {
            $class_teacher_id[$teacher->id] = $teacher->getFormatted();
        }

        return [
            "class_teacher_id" => $class_teacher_id,
        ];
    }

    public function getFormatted(): string {
        return sprintf("%s.%s", $this->grade, $this->label);
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 5) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new _Class($database);
        $object->setProperty("id",                  intval($row[0]));
        $object->setProperty("year",                intval($row[1]));
        $object->setProperty("grade",               intval($row[2]));
        $object->setProperty("label",               $row[3]);
        $object->setProperty("class_teacher_id",    intval($row[4]));
        return $object;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",                 $this->id),
            new DatabaseParameter("year",               $this->year),
            new DatabaseParameter("grade",              $this->grade),
            new DatabaseParameter("label",              $this->label),
            new DatabaseParameter("class_teacher_id",   $this->class_teacher_id),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Classes (
                    year,
                    grade,
                    label,
                    class_teacher_id
                )
                VALUES (
                    :year,
                    :grade,
                    :label,
                    :class_teacher_id
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE Classes
                SET
                    year                = :year
                    grade               = :grade
                    label               = :label
                    class_teacher_id    = :class_teacher_id 
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Classes
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }
    
    static public function get(Database $database, string $id): ?_Class {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Classes
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return _Class::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Classes
        ");

        return array_map(function (array $row) use($database) {
            return _Class::fromDatabaseRow($database, $row);
        }, $rows);
    }

    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $class = null;
        if ($id > 0) {
            $class = _Class::get($database, strval($id));

            if ($class == null) 
                throw new Exception("Error Processing Request", 1);
        } else {
            $class = new _Class($database);
        }

        $class->setProperty("year",                intval($data["year"]));
        $class->setProperty("grade",               intval($data["grade"]));
        $class->setProperty("label",               $data["label"]);


        return [$class, Teacher::get($database, $data["class_teacher_id"])];
    }

    public static function applyPostData(array $models): void {

        $class = $models[0];
        $teacher = $models[1];

        if ($teacher != null) {
            $class->setProperty("class_teacher_id",               $teacher->id);
        }

        $class->write();
    }
}