<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use DateTime;

class Zkouska extends EditableDatabaseEntity {
    protected int $id = 0;
    private int $student_id;
    private int $predmet_id;
    private int $ucebna_id;
    private string $puvodni_znamka;
    private string $vyslednaZnamka;
    private DateTime $termin_konani;

    public function getProperty(string $key){
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array{
        return [
            ["propertyName" => "id", "name" => "ID", "type" => gettype(0)],
            ["propertyName" => "student_id", "name" => "ID studenta", "type" => gettype(0)],
            ["propertyName" => "predmet_id", "name" => "ID předmětu", "type" => gettype([])],
            ["propertyName" => "ucebna_id", "name" => "ID učebny", "type" => gettype(0)],
            ["propertyName" => "puvodni_znamka", "name" => "Původní známka", "type" => gettype("")], 
            ["propertyName" => "vysledna_znamka", "name" => "Výsledná známka", "type" => gettype("")], 
            ["propertyName" => "termin_konani", "name" => "Termín konání", "type" => gettype("")], 
            ["propertyName" => "zkratka", "name" => "Zkratka", "type" => gettype("")]
        ];
    }

    public static function getSelectOptions(Database $database): array {
        return [
            "predmet_id" => array_map(function ($predmet) {
                return [$predmet->id, $predmet->nazev];
            }, Predmet::getAll($database)) ,
            "vysledna_znamka" => ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"],
        ];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 7) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $zkouska = new Zkouska($database);
        $zkouska->setProperty("id",    intval($row[0]));
        $zkouska->setProperty("student_id", $row[1]);
        $zkouska->setProperty("predmet_id", $row[2]);
        $zkouska->setProperty("ucebna_id", $row[3]);
        $zkouska->setProperty("puvodni_znamka", $row[4]);
        $zkouska->setProperty("vyslednaZnamka", $row[5]);
        $zkouska->setProperty("termin_konani", $row[6]);
        return $zkouska;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("student_id",  $this->student_id),
            new DatabaseParameter("predmet_id",  $this->predmet_id),
            new DatabaseParameter("ucebna_id",  $this->ucebna_id),
            new DatabaseParameter("puvodni_znamka",  $this->puvodni_znamka),
            new DatabaseParameter("vyslednaZnamka",  $this->vyslednaZnamka),
            new DatabaseParameter("termin_konani",  $this->termin_konani),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO zkousky (
                    student_id,
                    predmet_id,
                    ucebna_id,
                    puvodni_znamka,
                    vyslednaZnamka,
                    termin_konani
                )
                VALUES (
                    :student_id,
                    :predmet_id,
                    :ucebna_id,
                    :puvodni_znamka,
                    :vyslednaZnamka,
                    :termin_konani
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE zkousky
                SET
                student_id = :student_id
                predmet_id = :predmet_id
                ucebna_id = :ucebna_id
                puvodni_znamka = :puvodni_znamka
                vyslednaZnamk = :vyslednaZnamk
                termin_konani = :termin_konani
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM zkousky
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id)
        ]);
    }
    
    static public function get(Database $database, string $id): Zkouska {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM zkousky
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        // TODO: Check empty result

        return Zkouska::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM zkousky
        ");

        return array_map(function (array $row) use($database) {
            return Zkouska::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
