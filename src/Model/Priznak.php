<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Priznak extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {
    protected int $id = 0;
    private string $nazev;

    public function getProperty(string $key){
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",      "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("nazev",   "Název",    ViewablePropertyType::STRING)
        ];
    }

    public static function getSelectOptions(Database $database): array{
        return [];
    }

    public function getFormatted(): string {
        return $this->nazev;
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
        $priznak = new Priznak($database);
        $priznak->setProperty("id",    intval($row[0]));
        $priznak->setProperty("nazev", $row[1]);
        return $priznak;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("nazev",  $this->nazev)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Priznaky (
                    nazev
                )
                VALUES (
                    :nazev
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE Priznaky
                SET
                    nazev = :nazev
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Priznaky
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $this->id)
        ]);
    }

    static public function get(Database $database, int $id): Priznak {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Priznaky
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        // TODO: Check empty result

        return Priznak::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Priznaky
        ");

        return array_map(function (array $row) use($database) {
            return Priznak::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
