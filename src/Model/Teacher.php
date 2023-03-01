<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Teacher extends FullDatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

    /**
     * @var array<DatabaseEntityProperty>
     */
    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("name", "Jméno", DatabaseEntityPropertyType::String, false, false, ""),
            new DatabaseEntityProperty("surname", "Příjmení", DatabaseEntityPropertyType::String, false, false, ""),
            new DatabaseEntityProperty("prefix", "Prefix", DatabaseEntityPropertyType::String, false, false, ""),
            new DatabaseEntityProperty("suffix", "Suffix", DatabaseEntityPropertyType::String, false, false, ""),
            new DatabaseEntityProperty("subjects", "Vyučované předměty", DatabaseEntityPropertyType::Intermediate_data, true, false, []) // TODO intermediated data
        ];
    }

    public static function getSelectOptions(Database $database): array {
        // TODO
        return [];
    }

    public function getFormatted(): string {
        return sprintf("%s %s %s %s",$this->prefix, $this->name, $this->surname, $this->suffix);
    }

    public function getIntermediateData(): array {
        // TODO
        return [];
    }
    
    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new Teacher($database) : Teacher::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("name",    $data["name"]);
        $model->setProperty("surname", $data["surname"]);
        $model->setProperty("prefix",  $data["prefix"]);
        $model->setProperty("suffix",  $data["suffix"]);

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
        // TODO
    }

    public static function getFromSurname(Database $database, string $surname): Teacher {
        $row = $database->fetchSingle("
            SELECT 
                * 
            FROM Teachers 
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