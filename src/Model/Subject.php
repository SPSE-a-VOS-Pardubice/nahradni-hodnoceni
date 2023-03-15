<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Subject extends FullDatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("name", "Název", DatabaseEntityPropertyType::String, false, false, ""),
            new DatabaseEntityProperty("abbreviation", "Zkratka", DatabaseEntityPropertyType::String, false, false, ""),
            new DatabaseEntityProperty("traits", "Příznaky", DatabaseEntityPropertyType::Intermediate_data, false, false, []) // TODO intermediate data
        ];
    }

    public static function getSelectOptions(Database $database): array {
        // TODO
        return [];
    }

    public function getFormatted(): string {
        return $this->name;
    }

    public function getIntermediateData(): array {
        // TODO
        return [];
    }

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new Subject($database) : Subject::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("name",            $data["name"]);
        $model->setProperty("abbreviation",    $data["abbreviation"]);

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
        // TODO
    }

    public static function getFromAbbreviation(Database $database, string $abbreviation): Subject {
        $row = $database->fetchSingle("
            SELECT 
                * 
            FROM Subjects 
            WHERE 
                abbreviation = :abbreviation
        ", [
            new DatabaseParameter("abbreviation", $abbreviation)
        ]);

        if ($row === false) {
            throw new \RuntimeException("Předmět se zkratkou " . $abbreviation . " nebyl v databázi nalezen");
        }
        return Subject::fromDatabaseRow($database, $row);
    }
    
    public static function getDatabaseName(): string {
        return "subjects";
    }
}
