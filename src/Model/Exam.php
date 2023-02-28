<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use DateTime;
use Spse\NahradniHodnoceni\Helpers\DateTimeHelper;

const MARK_OPTIONS = ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"];

class Exam extends FullDatabaseEntity implements ViewableDatabaseEntity {

    public static function getProperties(): array {
        return [
            // TODO intermediate data
            new DatabaseEntityProperty("student_id", "Student", DatabaseEntityPropertyType::Intermediate_data, true, false, null),
            new DatabaseEntityProperty("subject_id", "Předmět", DatabaseEntityPropertyType::Intermediate_data, true, false, null),
            new DatabaseEntityProperty("classroom_id", "Učebna", DatabaseEntityPropertyType::Intermediate_data, true, true, null),
            new DatabaseEntityProperty("original_mark", "Původní známka", DatabaseEntityPropertyType::String, true, false, "5"),
            new DatabaseEntityProperty("final_mark", "Výsledná známka", DatabaseEntityPropertyType::String, true, true, "5"),
            new DatabaseEntityProperty("time", "Termín konání", DatabaseEntityPropertyType::DateTime, false, true, new \DateTime("2020-09-01")), // TODO
            new DatabaseEntityProperty("chairman_id", "Předseda", DatabaseEntityPropertyType::Intermediate_data, true, true, null), // TODO
            new DatabaseEntityProperty("class_teacher_id", "Přísedící", DatabaseEntityPropertyType::Intermediate_data, true, true, null), // TODO
            new DatabaseEntityProperty("examiner_id", "Zkoušející", DatabaseEntityPropertyType::Intermediate_data, true, false, null), // TODO
        ];
    }

    public static function getSelectOptions(Database $database): array {
        // TODO
        return [];
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new Exam($database) : Exam::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);
        
        $model->setProperty("student_id",       intval($data["student_id"]));
        $model->setProperty("subject_id",       intval($data["subject_id"]));
        $model->setProperty("classroom_id",     $data["classroom_id"] === "" ? null : intval($data["classroom_id"]));
        $model->setProperty("original_mark",    $data["original_mark"]);
        $model->setProperty("final_mark",       $data["final_mark"] === "" ? null : $data["final_mark"]);
        $model->setProperty("time",             $data["time"] === "" ? null : new DateTime($data["time"]));
        $model->setProperty("chairman_id",      $data["chairman_id"] === "" ? null : intval($data["chairman_id"]));
        $model->setProperty("class_teacher_id", $data["class_teacher_id"] === "" ? null : intval($data["class_teacher_id"]));
        $model->setProperty("examiner_id",      $data["examiner_id"] === "" ? null : intval($data["examiner_id"]));

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();
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
}
