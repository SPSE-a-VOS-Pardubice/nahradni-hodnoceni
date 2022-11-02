<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Zkouska extends DatabaseEntity {
    protected int $id = 0;
    private int $student_id;
    private int $predmet_id;
    private int $ucebna_id;
    private string $puvodni_znamka;
    private string $vyslednaZnamka;
    private time $cas_konani;
    private date $den_konani;

    protected function setProrerty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 8) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $trida = new trida($database);
        $trida->setProperty("id",    intval($row[0]));
        $trida->setProperty("student_id", $row[1]);
        $trida->setProperty("predmet_id", $row[2]);
        $trida->setProperty("ucebna_id", $row[3]);
        $trida->setProperty("puvodni_znamka", $row[4]);
        $trida->setProperty("vyslednaZnamka", $row[5]);
        $trida->setProperty("cas_konani", $row[6]);
        $trida->setProperty("den_konani", $row[7]);
        return $trida;
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
            new DatabaseParameter("cas_konani",  $this->cas_konani),
            new DatabaseParameter("den_konani",  $this->den_konani)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO tridy (
                    student_id,
                    predmet_id,
                    ucebna_id,
                    puvodni_znamka,
                    vyslednaZnamka,
                    cas_konani,
                    den_konani
                )
                VALUES (
                    :student_id,
                    :predmet_id,
                    :ucebna_id,
                    :puvodni_znamka,
                    :vyslednaZnamka,
                    :cas_konani,
                    :den_konani
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE tridy
                SET
                student_id = :student_id
                predmet_id = :predmet_id
                ucebna_id = :ucebna_id
                puvodni_znamka = :puvodni_znamka
                vyslednaZnamk = :vyslednaZnamk
                cas_konani = :cas_konani
                den_konani = :den_konani
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM tridy
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
            FROM tridy
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        // TODO: Check empty result

        return Zkouska::fromDatabaseRow($database, $row);
    }
}
