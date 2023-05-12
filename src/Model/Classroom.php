<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;
    
    class Classroom extends FullDatabaseEntity {

        public static function getProperties(): array {
            return [
                new DatabaseEntityProperty("label", "Označení", DatabaseEntityPropertyType::STRING, null, false, ""),
                new DatabaseEntityProperty("traits", "Příznaky", DatabaseEntityPropertyType::INTERMEDIATE_DATA, ClassroomTrait::class, true, 0) 
            ];
        }

        public function getFormatted(): string {
            return $this->label;
        }
        
        public static function getTableName(): string {
            return "Classrooms";
        }
    }

?>