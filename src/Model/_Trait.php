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
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("name",   $this->name),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Traits (
                    name
                )
                VALUES (
                    :name
                )
            ", [
                new DatabaseParameter("name",   $this->name)
            ]);

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
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

    static public function get(Database $database, string $id): ?_Trait {
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
    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $trait = null;
        if ($id > 0) {
            $trait = _Trait::get($database, strval($id));

            if ($trait == null) 
                throw new \RuntimeException("Error Processing Request", 1);
        } else {
            $trait = new _Trait($database);
        }

        $trait->setProperty("name",    $data["name"]);


        return [$trait];
    }

    public static function applyPostData(array $models): void {

        $trait = $models[0];

        $trait->write();
    }
}
