<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Student extends FullDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("name", "Jméno", DatabaseEntityPropertyType::STRING, null, false, ""),
            new DatabaseEntityProperty("surname", "Příjmení", DatabaseEntityPropertyType::STRING, null, false, ""),
            new DatabaseEntityProperty("class_id", "Třída", DatabaseEntityPropertyType::EXTERNAL_DATA, _Class::class, false, null) 
        ];
    }

    public static function getSelectOptions(Database $database): array {
        // TODO 
        return [];
    }

    public function getFormatted(): string
    {
        return sprintf("%s %s", $this->surname, $this->name);
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
    }

    public static function getFromNameSurnameClass(Database $database, string $name, string $surname, string $class): Student {
        $explodedClass = explode(".", $class);
        //TODO: Zkontrolovat validitu textového řetězce třídy
        if (false) {
            throw new \RuntimeException("Jméno třídy " . $class . " je ve špatném formátu");
        }

        $grade = intval($explodedClass[0]);
        $label = $explodedClass[1];

        // TODO: Crashne když nenalezne žádného studenta
        $row = $database->fetchSingle("
            SELECT 
                Students.id, Students.name, Students.surname, Students.class_id
            FROM Students, Classes 
            WHERE 
                Students.class_id = Classes.id AND 
                Students.name = :name AND 
                Students.surname = :surname AND 
                Classes.grade = :grade AND 
                Classes.label = :label
        ", [
                new DatabaseParameter("name", $name),
                new DatabaseParameter("surname", $surname),
                new DatabaseParameter("grade", $grade),
                new DatabaseParameter("label", $label)
            ]);

        if ($row === false) {
            throw new \RuntimeException("Student se jménem " . $name . " " . $surname . " ve třídě " . $class . " nebyl v databázi nalezen");
        }
        return Student::fromDatabase($database, $row);
    }

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new Student($database) : Student::get($database, $id);
        if ($model === null)
            throw new \RuntimeException("Error Processing Request", 1);


        $model->setProperty("name", $data["name"]);
        $model->setProperty("surname", $data["surname"]);

        // Otestuje zda je třída se zadaným id v db
        if (_Class::get($database, intVal($data["class_id"])) != null)
            $model->setProperty("class_id", intVal($data["class_id"]));


        return new ParsedPostData($model, []); // TODO
    }

    public function getPropertyValues(): array {
        $res = [];
        for ($i = 0; $i < count($this->getProperties()); $i++) {
            $res[] = $this->getProperty($this->getProperties()[$i]->name);
        }

        return $res;
    }

    public function setPropertyValues($properties): void {
        for ($i = 0; $i < count($properties); $i++) {
            $this->setProperty($this->getProperties()[$i]->name, $properties[$i]);
        }
    }

    public static function getTableName(): string {
        return "Students";
    }
}