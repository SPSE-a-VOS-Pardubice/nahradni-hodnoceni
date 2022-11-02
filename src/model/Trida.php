<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Trida extends DatabaseEntity {
    protected int $id = 0;
    private int $rocnik;
    private string $oznaceni;
    private int $tridni_ucitel_id;

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 4) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $trida = new trida($database);
        $trida->setProperty("id",    intval($row[0]));
        $trida->setProperty("rocnik", $row[1]);
        $trida->setProperty("oznaceni", $row[2]);
        $trida->setProperty("tridni_ucitel_id", $row[3]);
        return $trida;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",     $this->id),
            new DatabaseParameter("rocnik",  $this->rocnik),
            new DatabaseParameter("oznaceni",  $this->oznaceni),
            new DatabaseParameter("tridni_ucitel_id",  $this->tridni_ucitel_id)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO tridy (
                    rocnik,
                    oznaceni,
                    tridni_ucitel_id
                )
                VALUES (
                    :rocnik,
                    :oznaceni,
                    :tridni_ucitel_id
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE tridy
                SET
                    rocnik = :rocnik
                    oznaceni = :oznaceni
                    tridni_ucitel_id = :tridni_ucitel_id 
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
    
    static public function get(Database $database, string $id): trida {
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

        return Priznak::fromDatabaseRow($database, $row);
    }
}
