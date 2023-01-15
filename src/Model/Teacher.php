<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Teacher extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity
{
    protected int $id = 0;
    private string $name;
    private string $surname;
    private string $prefix;
    private string $suffix;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void
    {
        $this->$key = $value;
    }

    public static function getProperties(): array
    {
        return [
            new ViewableProperty("id",      "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("name",    "Jméno",    ViewablePropertyType::STRING),
            new ViewableProperty("surname", "Příjmení", ViewablePropertyType::STRING),
            new ViewableProperty("prefix",  "Prefix",   ViewablePropertyType::STRING),
            new ViewableProperty("suffix",  "Suffix",   ViewablePropertyType::STRING),
        ];
    }

    public static function getSelectOptions(Database $database): array {
        return [];
    }

    public function getFormatted(): string {
        return sprintf("%s %s %s %s",$this->prefix, $this->name, $this->surname, $this->suffix);
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 5) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Teacher($database);
        $object->setProperty("id",      intval($row[0]));
        $object->setProperty("name",    $row[1]);
        $object->setProperty("surname", $row[2]);
        $object->setProperty("prefix",  $row[3]);
        $object->setProperty("suffix",  $row[4]);
        return $object;
    }

    public function write(): void
    {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",         $this->id),
            new DatabaseParameter("name",       $this->name),
            new DatabaseParameter("surname",    $this->surname),
            new DatabaseParameter("prefix",     $this->prefix),
            new DatabaseParameter("suffix",     $this->suffix),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Teachers (
                    name,
                    surname,
                    prefix,
                    suffix
                )
                VALUES (
                    :name,
                    :surname,
                    :prefix,
                    :suffix
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE Teachers
                SET
                    name    = :name,
                    surname = :surname,
                    prefix  = :prefix,
                    suffix  = :suffix
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void
    {
        $this->database->execute("
            DELETE FROM Teachers
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }

    static public function get(Database $database, string $id): ?Teacher
    {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Teachers
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return Teacher::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array
    {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Teachers
        ");

        return array_map(function (array $row) use ($database) {
            return Teacher::fromDatabaseRow($database, $row);
        }, $rows);
    }
}