<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Student extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {
    protected int $id = 0;
    private string $name;
    private string $surname;
    private int $class_id;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",          "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("name",        "Jméno",    ViewablePropertyType::STRING),
            new ViewableProperty("surname",     "Příjmení", ViewablePropertyType::STRING),
            new ViewableProperty("class_id",    "Třída",    ViewablePropertyType::INTEGER,  true),
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $class_id = [];

        foreach(_Class::getAll($database) as $trida) {
            $class_id[$trida->id] = $trida->getFormatted();
        }

        return [
            "class_id" => $class_id,
        ];
    }

    public function getFormatted(): string {
        return sprintf("%s %s", $this->surname, $this->name);
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 4) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Student($database);
        $object->setProperty("id",          intval($row[0]));
        $object->setProperty("name",        $row[1]);
        $object->setProperty("surname",     $row[2]);
        $object->setProperty("class_id",    intval($row[3]));
        return $object;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("name",       $this->name),
            new DatabaseParameter("surname",    $this->surname),
            new DatabaseParameter("class_id",   $this->class_id),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Students (
                    name,
                    surname,
                    class_id
                )
                VALUES (
                    :name,
                    :surname,
                    :class_id
                )
            ", $parameters);

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
            array_push($parameters, new DatabaseParameter("id", $this->id));
            $this->database->execute("
                UPDATE Students
                SET
                    name        = :name,
                    surname     = :surname,
                    class_id    = :class_id
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Students
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }
    
    static public function get(Database $database, int $id): ?Student {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Students
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        
        if ($row === false) {
            return null;
        }
        return Student::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Students
        ");

        return array_map(function (array $row) use($database) {
            return Student::fromDatabaseRow($database, $row);
        }, $rows);
    }
    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new Student($database) : Student::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);
        

        $model->setProperty("name",        $data["name"]);
        $model->setProperty("surname",     $data["surname"]);

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
    }

    public static function getFromNameSurnameClass(Database $database, string $name, string $surname, string $class): Student {
        $explodedClass = explode(".", $class);
        //TODO: Zkontrolovat validitu textového řetězce třídy
        if(false) {
            throw new \RuntimeException("Jméno třídy " . $class . " je ve špatném formátu");
        }
        
        $grade = intval($explodedClass[0]);
        $label = $explodedClass[1];
        
        $row = $database->fetchSingle("
            SELECT 
                students.id, students.name, students.surname, students.class_id
            FROM students, classes 
            WHERE 
                students.class_id = classes.id AND 
                students.name = :name AND 
                students.surname = :surname AND 
                classes.grade = :grade AND 
                classes.label = :label
        ", [
            new DatabaseParameter("name", $name),
            new DatabaseParameter("surname", $surname),
            new DatabaseParameter("grade", $grade),
            new DatabaseParameter("label", $label)
        ]);

        if ($row === false) {
            throw new \RuntimeException("Student se jménem " . $name . " " . $surname . " ve třídě " . $class . " nebyl v databázi nalezen");
        }
        return Student::fromDatabaseRow($database, $row);
    }
}
