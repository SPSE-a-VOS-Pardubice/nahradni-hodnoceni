<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Student extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

    public static function getProperties(): array {
        // TODO
        return [];
    }

    public static function getSelectOptions(Database $database): array {
        // TODO 
        return [];
    }

    public function getFormatted(): string {
        // TODO
        return "";
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
        if(false) {
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
        return Student::fromDatabaseRow($database, $row);
    }

    public function getPropertyValues(): array {
        $res = [];
        for($i = 0; $i < count($this->getProperties()); $i++) {
            $res[] = $this->getProperty($this->getProperties()[$i]->name); 
        }

        return $res;
    }
    
    public function setPropertyValues($properties): void {
        for($i = 0; $i < count($properties); $i++) {
            $this->setProperty($this->getProperties()[$i]->name, $properties[$i]);   
        }
    }
}
