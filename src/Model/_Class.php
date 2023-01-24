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
        // mělo by vracet učitele
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

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
            array_push($parameters, new DatabaseParameter("id", $this->id));
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
    
    static public function get(Database $database, int $id): ?_Class {
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

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new _Class($database) : _Class::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("year",     intval($data["year"]));
        $model->setProperty("grade",    intval($data["grade"]));
        $model->setProperty("label",    $data["label"]);

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
    }
}
