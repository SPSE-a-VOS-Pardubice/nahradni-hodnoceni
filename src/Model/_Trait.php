<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class _Trait extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {
    protected int $id = 0;
    private string $name;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",      "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("name",    "Název",    ViewablePropertyType::STRING),
        ];
    }

    public static function getSelectOptions(Database $database): array{
        return [];
    }

    public function getFormatted(): string {
        return $this->name;
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 2) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new _Trait($database);
        $object->setProperty("id",      intval($row[0]));
        $object->setProperty("name",    $row[1]);
        return $object;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("name", $this->name),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Traits (
                    name
                )
                VALUES (
                    :name
                )
            ", $parameters);

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
            array_push($parameters, new DatabaseParameter("id", $this->id));
            $this->database->execute("
                UPDATE Traits
                SET
                    name = :name
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Traits
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }

    static public function get(Database $database, int $id): ?_Trait {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Traits
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return _Trait::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Traits
        ");

        return array_map(function (array $row) use($database) {
            return _Trait::fromDatabaseRow($database, $row);
        }, $rows);
    }
    
    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new _Trait($database) : _Trait::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("name", $data["name"]);

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
    }
}
