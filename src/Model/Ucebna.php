<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;

    class Ucebna extends DatabaseEntity {

        protected int $id = 0;
        private string $oznaceni;

        public function getProperty(string $key){
            return $this->$key;
        }

        protected function setProperty(string $key, $value): void {
            $this->$key = $value;
        }

        public static function getProperties(): array{
            return ["id"=>"ID", "oznaceni"=>"Označení"];
        }

        public static function fromDatabaseRow(Database $database, array $row) {
            if (count($row) !== 3) {
                throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
            }

            $ucebna = new Ucebna($database);
            $ucebna->setProperty("id",    intval($row[0]));
            $ucebna->setProperty("oznaceni", $row[1]);
            return $ucebna;
        }
        

        public function write(): void {
            $parameters = [
                new DatabaseParameter("id",     $this->id),
                new DatabaseParameter("oznaceni",  $this->oznaceni),
            ];

            if($this->id === 0){
                $this->database->execute("
                INSERT INTO ucebny (
                    oznaceni
                )
                VALUES (
                    :oznaceni
                )", $parameters);
            }else{
                $this->database->execute("
                UPDATE ucebny
                SET
                oznaceni = :oznaceni
                WHERE
                id = :id
                ", $parameters);
            }
        }

        public function remove(): void {
            $this->database->execute("
                DELETE FROM ucebny
                WHERE
                    id = :id
                    LIMIT 1
            ", [
                new DatabaseParameter("id", $this->id)
            ]);
        }

        static public function get(Database $database, string $id): Ucebna {
            $row = $database->fetchSingle("
                SELECT
                    *
                FROM ucebny
                WHERE
                    id = :id
            ", [
                new DatabaseParameter("id", $id)
            ]);

            return Ucebna::fromDatabaseRow($database, $row);
            
        }

        static public function getAll(Database $database): array {
            $rows = $database->fetchMultiple("
                SELECT
                    *
                FROM ucebny
            ");
    
            return array_map(function (array $row) use($database) {
                return Ucebna::fromDatabaseRow($database, $row);
            }, $rows);
        }
    }
?>