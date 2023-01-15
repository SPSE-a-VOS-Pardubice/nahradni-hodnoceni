<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Predmet extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

    protected int $id = 0;
    private string $nazev;
    private string $zkratka;

    protected function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",          "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("nazev",       "Název",    ViewablePropertyType::STRING),
            new ViewableProperty("zkratka",     "Zkratka",  ViewablePropertyType::STRING),
            new ViewableProperty("traits",      "Příznaky", ViewablePropertyType::INTERMEDIATE_DATA,    true)
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $traits = [];
        $teachers = [];
        
        foreach(Priznak::getAll($database) as $trait) {
            $traits[$trait->id] = $trait->getFormatted();
        }

        foreach(Ucitel::getAll($database) as $teacher){
            $teachers[$teacher->id] = $teacher->getFormatted();
        }

        return [
            "priznaky" => $traits,
            "ucitele" => $teachers
        ];
    }

    public function getFormatted(): string {
        return $this->nazev;
    }

    public function getIntermediateData(): array {
    return [
        "traits" => array_map(function ($classroomTrait) {
                return Priznak::get($this->database, $classroomTrait->trait_id);
            }, ClassroomTrait::getForClassroom($this->database, $this->id)),
        ];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 3) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $predmet = new Predmet($database);
        $predmet->setProperty("id",    intval($row[0]));
        $predmet->setProperty("nazev", $row[1]);
        $predmet->setProperty("zkratka", $row [2]);
        return $predmet;
    }
    

    public function write(): void {
        $parameters = [
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("nazev",  $this->nazev),
            new DatabaseParameter("zkratka", $this->zkratka)
        ];

        if($this->id === 0){
            $this->database->execute("
            INSERT INTO Predmety (
                nazev
                zkratka
            )
            VALUES (
                :nazev
                :zkratka
            )", $parameters);
        }else{
            $this->database->execute("
            UPDATE Predmety
            SET
            nazev = :nazev
            zkratka = :zkraka
            WHERE
            id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Predmety
            WHERE
                id = :id
                LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id)
        ]);
    }

    static public function get(Database $database, string $id): Predmet {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Predmety
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        return Predmet::fromDatabaseRow($database, $row);
        
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Predmety
        ");

        return array_map(function (array $row) use($database) {
            return Predmet::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
