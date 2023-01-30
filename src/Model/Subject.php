<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Subject extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {
    /**
     * @var int
     */
    protected $id = 0;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $abbreviation;

    protected function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",              "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("name",            "Název",    ViewablePropertyType::STRING),
            new ViewableProperty("abbreviation",    "Zkratka",  ViewablePropertyType::STRING),
            new ViewableProperty("traits",          "Příznaky", ViewablePropertyType::INTERMEDIATE_DATA,    true),
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $traits = [];
        
        foreach(_Trait::getAll($database) as $trait) {
            $traits[$trait->id] = $trait->getFormatted();
        }

        return [
            "traits" => $traits,
        ];
    }

    public function getFormatted(): string {
        return $this->name;
    }

    public function getIntermediateData(): array {
        return [
            "traits" => array_map(function ($subjectTrait) {
                return _Trait::get($this->database, $subjectTrait->trait_id);
            }, SubjectTrait::getForSubject($this->database, $this->id)),
        ];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 3) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Subject($database);
        $object->setProperty("id",              intval($row[0]));
        $object->setProperty("name",            $row[1]);
        $object->setProperty("abbreviation",    $row[2]);
        return $object;
    }
    

    public function write(): void {
        $parameters = [
            new DatabaseParameter("name",           $this->name),
            new DatabaseParameter("abbreviation",   $this->abbreviation)
        ];

        if($this->id === 0) {
            $this->database->execute("
            INSERT INTO Subjects (
                name,
                abbreviation
            )
            VALUES (
                :name,
                :abbreviation
            )", $parameters);

            $this->id = intval($this->database->lastInsertId("id"));
        }else{
            array_push($parameters, new DatabaseParameter("id", $this->id));
            $this->database->execute("
            UPDATE Subjects
            SET
                name    = :name,
                abbreviation = :abbreviation
            WHERE
                id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Subjects
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }

    static public function get(Database $database, int $id): ?Subject {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Subjects
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return Subject::fromDatabaseRow($database, $row);
        
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Subjects
        ");

        return array_map(function (array $row) use($database) {
            return Subject::fromDatabaseRow($database, $row);
        }, $rows);
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
}
