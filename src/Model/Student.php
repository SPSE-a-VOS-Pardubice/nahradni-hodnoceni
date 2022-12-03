<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Student extends DatabaseEntity {
    protected int $id = 0;
    private string $jmeno;
    private string $primeni;

    public function getProperty(string $key){
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array{
        return ["id"=>"ID", "jmeno"=>"Jméno", "primeni"=>"Příjmení"];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 3) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $student = new Student($database);
        $student->setProperty("id",    intval($row[0]));
        $student->setProperty("jmeno", $row[1]);
        $student->setProperty("primeni", $row[2]);
        return $student;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("jmeno",  $this->jmeno),
            new DatabaseParameter("primeni",  $this->primeni)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO studenti (
                    jmeno,
                    primeni
                )
                VALUES (
                    :jmeno,
                    :primeni
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE studenti
                SET
                    jmeno = :jmeno,
                    primeni = :primeni
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM studenti
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id)
        ]);
    }
    
    static public function get(Database $database, string $id): Student {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM studenti
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        // TODO: Check empty result

        return Student::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM studenti
        ");

        return array_map(function (array $row) use($database) {
            return Student::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
