<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use DateTime;

class Zkouska extends DatabaseEntity implements ViewableDatabaseEntity {
    protected int $id = 0;
    private int $student_id;
    private int $predmet_id;
    private int $ucebna_id;
    private string $puvodni_znamka;
    private string $vysledna_znamka;
    private DateTime $termin_konani;

    public function getProperty(string $key){
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",              "ID",               ViewablePropertyType::INTEGER),
            new ViewableProperty("student_id",      "Student",          ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("predmet_id",      "Předmět",          ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("ucebna_id",       "Učebna",           ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("puvodni_znamka",  "Původní známka",   ViewablePropertyType::STRING,   true),
            new ViewableProperty("vysledna_znamka", "Výsledná známka",  ViewablePropertyType::STRING,   true),
            new ViewableProperty("termin_konani",   "Termín konání",    ViewablePropertyType::DATETIME)
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $student_id = [];
        $subject_id = [];
        $classroom_id = [];

        foreach (Student::getAll($database) as $student) {
            $student_id[$student->id] = $student->getFormatted(); 
        }
        foreach (Predmet::getAll($database) as $subject) {
            $subject_id[$subject->id] = $subject->getFormatted(); 
        }
        foreach (Ucebna::getAll($database) as $classroom) {
            $classroom_id[$classroom->id] = $classroom->getFormatted();
        }

        return [
            "student_id" => $student_id,
            "predmet_id" => $subject_id,
            "ucebna_id" => $classroom_id,
            "puvodni_znamka" => ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"],
            "vysledna_znamka" => ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"],
        ];
    }

    public function getIntermediateData(): array {
        return [];
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
        $zkouska->setProperty("vysledna_znamka", $row[5]);
        $zkouska->setProperty("termin_konani", new DateTime($row[6]));
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
                INSERT INTO Zkousky (
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
                UPDATE Zkousky
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
            DELETE FROM Zkousky
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
            FROM Zkousky
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
            FROM Zkousky
        ");

        return array_map(function (array $row) use($database) {
            return Zkouska::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
