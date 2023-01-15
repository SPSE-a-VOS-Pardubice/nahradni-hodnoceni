<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;
    
    class Ucebna extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

        protected int $id = 0;
        private string $oznaceni;

        public function getProperty(string $key){
            return $this->$key;
        }

        protected function setProperty(string $key, $value): void {
            $this->$key = $value;
        }

        public static function getProperties(): array {
            return [
                new ViewableProperty("id",          "ID",       ViewablePropertyType::INTEGER),
                new ViewableProperty("oznaceni",    "Označení", ViewablePropertyType::STRING),
                new ViewableProperty("traits",      "Priznaky", ViewablePropertyType::INTERMEDIATE_DATA,    true)
            ];
        }

        public static function getSelectOptions(Database $database): array {
            $traits = [];
            
            foreach(Priznak::getAll($database) as $trait) {
                $traits[$trait->id] = $trait->getFormatted();
            }

            return [
                "traits" => $traits,
            ];
        }

        public function getFormatted(): string {
            return $this->oznaceni;
        }

        public function getIntermediateData(): array {
        return [
            "traits" => array_map(function ($classroomTrait) {
                    return Priznak::get($this->database, $classroomTrait->trait_id);
                }, ClassroomTrait::getForClassroom($this->database, $this->id)),
            ];
        }

        public static function fromDatabaseRow(Database $database, array $row) {
            if (count($row) !== 2) {
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
                INSERT INTO Ucebny (
                    oznaceni
                )
                VALUES (
                    :oznaceni
                )", $parameters);
            }else{
                $this->database->execute("
                UPDATE Ucebny
                SET
                oznaceni = :oznaceni
                WHERE
                id = :id
                ", $parameters);
            }
        }

        public function remove(): void {
            $this->database->execute("
                DELETE FROM Ucebny
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
                FROM Ucebny
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
                FROM Ucebny
            ");
    
            return array_map(function (array $row) use($database) {
                return Ucebna::fromDatabaseRow($database, $row);
            }, $rows);
        }
    }
?>