<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;

    class Predmet extends DatabaseEntity {

        protected int $id = 0;
        private string $nazev;
        private string $zkratka;

        protected function setProperty(string $key, $value): void {
            $this->$key = $value;
        }

        public static function fromDatabaseRow(Database $database, array $row, ) {
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
            $parameters [
                new DatabaseParameter("id",     $this->id),
                new DatabaseParameter("nazev",  $this->nazev),
                new DatabaseParameter("zkratka", $this->zkratka)
            ];

            if($this->id === 0){
                $this->database->execute("
                INSERT INTO predmety (
                    nazev
                    zkratka
                )
                VALUES (
                    :nazev
                    :zkratka
                )", $parameters);
            }else{
                $this->database->execute("
                UPDATE predmety
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
                DELETE FROM predmety
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
                FROM predmety
                WHERE
                    id = :id
            ", [
                new DatabaseParameter("id", $id)
            ]);

        }
    }

?>
