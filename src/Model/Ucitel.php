<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Ucitel extends DatabaseEntity {
    protected int $id = 0;
    private string $jmeno;
    private string $prijmeni;
    private string $prefix;
    private string $suffix;

    public function getProperty(string $key){
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 5) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $ucitel = new Ucitel($database);
        $ucitel->setProperty("id",    intval($row[0]));
        $ucitel->setProperty("jmeno", $row[1]);
        $ucitel->setProperty("prijmeni", $row[2]);
        $ucitel->setProperty("prefix", $row[3]);
        $ucitel->setProperty("suffix", $row[4]);
        return $ucitel;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("jmeno",  $this->jmeno),
            new DatabaseParameter("prijmeni", $this->prijmeni),
            new DatabaseParameter("prefix", $this->prefix),
            new DatabaseParameter("suffix", $this->suffix)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO ucitele (
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
                UPDATE ucitele
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

    public function remove(): void {
        $this->database->execute("
            DELETE FROM ucitele
            WHERE
                id = :id
                LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id)
        ]);
    }

    static public function get(Database $database, string $id): Ucitel {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM ucitele
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        // TODO: Check empty result

        return Ucitel::fromDatabaseRow($database, $row);
    }
    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM ucitele
        ");

        // TODO: Check empty result

        return array_map(function (array $row) use($database){
            Ucitel::fromDatabaseRow($database, $row);
        }, $rows);
    }
}