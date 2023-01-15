<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Ucitel extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity
{
    protected int $id = 0;
    private string $jmeno;
    private string $prijmeni;
    private string $prefix;
    private string $suffix;

    public function getProperty(string $key)
    {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void
    {
        $this->$key = $value;
    }

    public static function getProperties(): array
    {
        return [
            new ViewableProperty("id",          "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("jmeno",       "Jméno",    ViewablePropertyType::STRING),
            new ViewableProperty("prijmeni",    "Příjmení", ViewablePropertyType::STRING),
            new ViewableProperty("prefix",      "Prefix",   ViewablePropertyType::STRING),
            new ViewableProperty("suffix",      "Suffix",   ViewablePropertyType::STRING)
        ];
    }

    public static function getSelectOptions(Database $database): array {
        return [];
    }

    public function getFormatted(): string {
        return sprintf("%s %s %s %s",$this->prefix, $this->jmeno, $this->prijmeni, $this->suffix);
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row)
    {
        // Zkontroluj délku dané řady.
        if (count($row) !== 5) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $ucitel = new Ucitel($database);
        $ucitel->setProperty("id",          intval($row[0]));
        $ucitel->setProperty("jmeno",       $row[1]);
        $ucitel->setProperty("prijmeni",    $row[2]);
        $ucitel->setProperty("prefix",      $row[3]);
        $ucitel->setProperty("suffix",      $row[4]);
        return $ucitel;
    }

    public function write(): void
    {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",         $this->id),
            new DatabaseParameter("jmeno",      $this->jmeno),
            new DatabaseParameter("prijmeni",   $this->prijmeni),
            new DatabaseParameter("prefix",     $this->prefix),
            new DatabaseParameter("suffix",     $this->suffix)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Ucitele (
                    jmeno,
                    prijmeni,
                    prefix,
                    suffix
                )
                VALUES (
                    :jmeno,
                    :prijmeni,
                    :prefix,
                    :suffix
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE Ucitele
                SET
                    jmeno = :jmeno,
                    prijmeni = :prijmeni,
                    prefix = :prefix,
                    suffix = :suffix
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void
    {
        $this->database->execute("
            DELETE FROM Ucitele
            WHERE
                id = :id
                LIMIT 1
        ", [
                new DatabaseParameter("id", $this->id)
            ]);
    }

    static public function get(Database $database, string $id): Ucitel
    {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Ucitele
            WHERE
                id = :id
        ", [
                new DatabaseParameter("id", $id)
            ]);

        // TODO: Check empty result

        return Ucitel::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array
    {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Ucitele
        ");

        // TODO: Check empty result

        return array_map(function (array $row) use ($database) {
            return Ucitel::fromDatabaseRow($database, $row);
        }, $rows);
    }
}