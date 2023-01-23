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
                new DatabaseParameter("label", $this->label),
            ];

            if ($this->id === 0) {
                $this->database->execute("
                INSERT INTO Classrooms (
                    label
                )
                VALUES (
                    :label
                )", $parameters);

                $this->id = intval($this->database->lastInsertId("id"));
            } else {
                array_push($parameters, new DatabaseParameter("id", $this->id));
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

        static public function get(Database $database, int $id): ?Classroom {
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

        public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
            $model = $id === 0 ? new Classroom($database) : Classroom::get($database, $id);
            if ($model === null) 
                throw new \RuntimeException("Error Processing Request", 1);

            $model->setProperty("label", $data["label"]);

            return new ParsedPostData($model, []); // TODO
        }

        public static function applyPostData(ParsedPostData $parsedData): void {
            $model = $parsedData->model;
            $model->write();

            // TODO
        }
    }

?>