<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;

    class Predmet extends DatabaseEntity implements EditableDatabaseEntity {

        protected int $id = 0;
        private string $nazev;
        private string $zkratka;

        protected function getProperty(string $key){
            return $this->$key;
        }

        protected function setProperty(string $key, $value): void {
            $this->$key = $value;
        }

        public static function getProperties(): array {
            return [
                new ViewableProperty("id", "ID", gettype(0)),
                new ViewableProperty("nazev", "Název", gettype("")),
                new ViewableProperty("zkratka", "Zkratka", gettype("")),
            ];
        }

        public static function getSelectOptions(Database $database): array {
            $priznaky = [];
            $ucitel_ids = [];
            
            foreach(Priznak::getAll($database) as $tempTrait) {
                $priznaky[$tempTrait->id] = $tempTrait->nazev;
            }

            foreach(Ucitel::getAll($database) as $tempUcitel){
            $ucitel_ids[$tempUcitel->id] = sprintf("%s %s %s %s",$tempUcitel->prefix, $tempUcitel->jmeno, $tempUcitel->prijmeni, $tempUcitel->suffix);
            }

            return [
                "priznaky" => $priznaky,
                "ucitele" => $ucitel_ids
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

            return Predmet::fromDatabaseRow($database, $row);
            
        }

        static public function getAll(Database $database): array {
            $rows = $database->fetchMultiple("
                SELECT
                    *
                FROM predmety
            ");

            return array_map(function (array $row) use($database) {
                return Predmet::fromDatabaseRow($database, $row);
            }, $rows);
        }
    }
?>