<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use DateTime;
use Spse\NahradniHodnoceni\Helpers\DateTimeHelper;

const MARK_OPTIONS = ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"];

class Exam extends FullDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("student_id", "Student", DatabaseEntityPropertyType::EXTERNAL_DATA, Student::class, false, 0), 
            new DatabaseEntityProperty("subject_id", "Předmět", DatabaseEntityPropertyType::EXTERNAL_DATA, Subject::class, false, 0), 
            new DatabaseEntityProperty("classroom_id", "Učebna", DatabaseEntityPropertyType::EXTERNAL_DATA, Classroom::class, true, 0), 
            new DatabaseEntityProperty("original_mark", "Původní známka", DatabaseEntityPropertyType::STRING, MARK_OPTIONS, false, "5"),
            new DatabaseEntityProperty("final_mark", "Výsledná známka", DatabaseEntityPropertyType::STRING, MARK_OPTIONS, true, "5"),
            new DatabaseEntityProperty("time", "Termín konání", DatabaseEntityPropertyType::DATE_TIME, null, true, new \DateTime("2020-09-01 12:00:00")),
            new DatabaseEntityProperty("chairman_id", "Předseda", DatabaseEntityPropertyType::EXTERNAL_DATA, Teacher::class, true, 0), 
            new DatabaseEntityProperty("class_teacher_id", "Přísedící", DatabaseEntityPropertyType::EXTERNAL_DATA, Teacher::class, true, 0), 
            new DatabaseEntityProperty("examiner_id", "Zkoušející", DatabaseEntityPropertyType::EXTERNAL_DATA, Teacher::class, false, 0)
        ];
    }

    public function getPropertyValues(): array {
        $res = [];
        for($i = 0; $i < count($this->getProperties()); $i++) {
            $res[] = $this->getProperty($this->getProperties()[$i]->name); 
        }

        return $res;
    }
    
    public function setPropertyValues($properties): void {
        for($i = 0; $i < count($properties); $i++) {
            $this->setProperty($this->getProperties()[$i]->name, $properties[$i]);   
        }
    }

    public static function getTableName(): string {
        return "Exams";
    }
    
    public function getFormatted(): string {
        // TODO jen získat stringová data záznam 524 69 5 nic uživateli neřekne on pořěbuje Jan Novák - Český jazyk - 5
        // Data budou třeba jinak vytáhnout
        return sprintf("student%s - subject%s - %s", $this->student_id, $this->subjenc_id, $this->original_mark);
    }
}
