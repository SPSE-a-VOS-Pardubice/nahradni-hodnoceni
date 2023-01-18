<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;
    
    class Classroom extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

        protected int $id = 0;
        private string $label;

        public function getProperty(string $key){
            return $this->$key;
        }

        protected function setProperty(string $key, $value): void {
            $this->$key = $value;
        }

        public static function getProperties(): array {
            return [
                new ViewableProperty("id",      "ID",       ViewablePropertyType::INTEGER),
                new ViewableProperty("label",   "Označení", ViewablePropertyType::STRING),
                new ViewableProperty("traits",  "Priznaky", ViewablePropertyType::INTERMEDIATE_DATA,    true),
            ];
        }

        public static function getSelectOptions(Database $database): array {
            $traits = [];
            
            foreach(_Trait::getAll($database) as $trait) {
                $traits[$trait->id] = $trait->getFormatted();
            }

            return [
                "traits" => $traits,
            ];
        }

        public function getFormatted(): string {
            return $this->label;
        }

        public function getIntermediateData(): array {
        return [
            "traits" => array_map(function ($classroomTrait) {
                    return _Trait::get($this->database, $classroomTrait->trait_id);
                }, ClassroomTrait::getForClassroom($this->database, $this->id)),
            ];
        }

        public static function fromDatabaseRow(Database $database, array $row) {
            if (count($row) !== 2) {
                throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
            }

            $object = new Classroom($database);
            $object->setProperty("id",      intval($row[0]));
            $object->setProperty("label",   $row[1]);
            return $object;
        }
        

        public function write(): void {
            $parameters = [
                new DatabaseParameter("id",     $this->id),
                new DatabaseParameter("label",  $this->label),
            ];

            if($this->id === 0){
                $this->database->execute("
                INSERT INTO Classrooms (
                    label
                )
                VALUES (
                    :label
                )", $parameters);
            }else{
                $this->database->execute("
                UPDATE Classrooms
                SET
                    label = :label
                WHERE
                    id = :id
                ", $parameters);
            }
        }

        public function remove(): void {
            $this->database->execute("
                DELETE FROM Classrooms
                WHERE
                    id = :id
                LIMIT 1
            ", [
                new DatabaseParameter("id", $this->id),
            ]);
        }

        static public function get(Database $database, string $id): ?Classroom {
            $row = $database->fetchSingle("
                SELECT
                    *
                FROM Classrooms
                WHERE
                    id = :id
            ", [
                new DatabaseParameter("id", $id),
            ]);

            if ($row === false) {
                return null;
            }
            return Classroom::fromDatabaseRow($database, $row);
            
        }

        static public function getAll(Database $database): array {
            $rows = $database->fetchMultiple("
                SELECT
                    *
                FROM Classrooms
            ");
    
            return array_map(function (array $row) use($database) {
                return Classroom::fromDatabaseRow($database, $row);
            }, $rows);
        }

        public static function parsePostData(array $data, Database $database, int $id = 0): array {

            $classroom = null;
            if ($id > 0) {
                $classroom = Classroom::get($database, strval($id));

                if ($classroom == null) 
                    throw new Exception("Error Processing Request", 1);
            } else {
                $classroom = new Classroom($database);
            }

            $classroom->setProperty("label",   $data["label"]);

            return [$classroom, ClassroomTrait::parsePostData($data, $database)];
        }

        public static function applyPostData(array $models): void {

            $classroom = $models[0];
            $classroomsTrails = $models[1];
            
            // tenhle algorizmus by se možná dal imlementovat někde obecně ???

            // vem si z priznakUcebna data kde idUcebna = id
            $traitsInDB = ClassroomTrait::getForClassroom($classroom->database, $classroom->id);
            // deklarace místa pro traity co jsou i v posu i v db
            $willBeInDB = [];
        
            // projdi traity v postu 
            foreach ($classroomsTrails as $trait) {
                // pokud není trait ve výsledku z db přidej ho do db 
                if ($traitsInDB[$trait->trait_id] == null) {
                    // šla by volat i metoda write 
                    // TODO: zamyslet se: asi vy modely pro mezi tabulky nemusei mýt metodu applyPostDat
                    $trait->classroom_id = $classroom->id;
                    ClassroomTrait::applyPostData([$trait]);
                }

                // dej do traity co jsou i v posu i v db
                $willBeInDB[$trait->trait_id] = $trait;
            }

            // projdi data z mezitabulky
            foreach ($traitsInDB as $traitInDB) {
                // pokud není v   traity co jsou i v postu i v db
                if ($willBeInDB[$traitInDB->trait_id] == null) {
                   //odeber ho z db
                   $traitInDB->remove();
                }
            }
            

            $classroom->write();
        }
    }

?>