<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use DateTime;
use Spse\NahradniHodnoceni\Helpers\DateTimeHelper;

const MARK_OPTIONS = ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"];

class Exam extends DatabaseEntity implements ViewableDatabaseEntity {
    protected int $id = 0;
    private int $student_id;
    private int $subject_id;
    private ?int $classroom_id;
    private string $original_mark;
    private ?string $final_mark;
    private ?DateTime $time;
    private ?int $chairman_id;
    private ?int $class_teacher_id;
    private ?int $examiner_id;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",                  "ID",               ViewablePropertyType::INTEGER),
            new ViewableProperty("student_id",          "Student",          ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("subject_id",          "Předmět",          ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("classroom_id",        "Učebna",           ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("original_mark",       "Původní známka",   ViewablePropertyType::STRING,   true),
            new ViewableProperty("final_mark",          "Výsledná známka",  ViewablePropertyType::STRING,   true),
            new ViewableProperty("time",                "Termín konání",    ViewablePropertyType::DATETIME),
            new ViewableProperty("chairman_id",         "Předseda",         ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("class_teacher_id",    "Třídní učitel",    ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("examiner_id",         "Zkoušející",       ViewablePropertyType::INTEGER,  true),
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $student_id = [];
        $subject_id = [];
        $classroom_id = [];
        $teachers = [];

        foreach (Student::getAll($database) as $student) {
            $student_id[$student->id] = $student->getFormatted(); 
        }
        foreach (Subject::getAll($database) as $subject) {
            $subject_id[$subject->id] = $subject->getFormatted(); 
        }
        foreach (Classroom::getAll($database) as $classroom) {
            $classroom_id[$classroom->id] = $classroom->getFormatted();
        }
        foreach (Teacher::getAll($database) as $teacher) {
            $teachers[$teacher->id] = $teacher->getFormatted();
        }

        return [
            "student_id"        => $student_id,
            "subject_id"        => $subject_id,
            "classroom_id"      => $classroom_id,
            "original_mark"     => MARK_OPTIONS,
            "final_mark"        => MARK_OPTIONS,
            "chairman_id"       => $teachers,
            "class_teacher_id"  => $teachers,
            "examiner_id"       => $teachers,
        ];
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 10) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Exam($database);
        $object->setProperty("id",                  intval($row[0]));
        $object->setProperty("student_id",          intval($row[1]));
        $object->setProperty("subject_id",          intval($row[2]));
        $object->setProperty("classroom_id",        intval($row[3]));
        $object->setProperty("original_mark",       $row[4]);
        $object->setProperty("final_mark",          $row[5]);
        $object->setProperty("time",                new DateTime($row[6]));
        $object->setProperty("chairman_id",         intval($row[7]));
        $object->setProperty("class_teacher_id",    intval($row[8]));
        $object->setProperty("examiner_id",         intval($row[9]));
        return $object;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("student_id",         $this->student_id),
            new DatabaseParameter("subject_id",         $this->subject_id),
            new DatabaseParameter("classroom_id",       $this->classroom_id),
            new DatabaseParameter("original_mark",      $this->original_mark),
            new DatabaseParameter("final_mark",         $this->final_mark),
            new DatabaseParameter("time",               $this->time === null ? null : DateTimeHelper::serialize($this->time)),
            new DatabaseParameter("chairman_id",        $this->chairman_id),
            new DatabaseParameter("class_teacher_id",   $this->class_teacher_id),
            new DatabaseParameter("examiner_id",        $this->examiner_id),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Exams (
                    student_id,
                    subject_id,
                    classroom_id,
                    original_mark,
                    final_mark,
                    time,
                    chairman_id,
                    class_teacher_id,
                    examiner_id
                )
                VALUES (
                    :student_id,
                    :subject_id,
                    :classroom_id,
                    :original_mark,
                    :final_mark,
                    :time,
                    :chairman_id,
                    :class_teacher_id,
                    :examiner_id
                )
            ", $parameters);

            $this->id = intval($this->database->lastInsertId("id"));
        } else {
            array_push($parameters, new DatabaseParameter("id", $this->id));
            $this->database->execute("
                UPDATE Exams
                SET
                    student_id          = :student_id,
                    subject_id          = :subject_id,
                    classroom_id        = :classroom_id,
                    original_mark       = :original_mark,
                    final_mark          = :final_mark,
                    time                = :time,
                    chairman_id         = :chairman_id,
                    class_teacher_id    = :class_teacher_id,
                    examiner_id         = :examiner_id
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Exams
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }
    
    static public function get(Database $database, int $id): ?Exam {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Exams
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return Exam::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Exams
        ");

        return array_map(function (array $row) use($database) {
            return Exam::fromDatabaseRow($database, $row);
        }, $rows);
    }

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new Exam($database) : Exam::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("student_id",       intval($data["student_id"]));
        $model->setProperty("subject_id",       intval($data["subject_id"]));
        $model->setProperty("classroom_id",     intval($data["classroom_id"]));
        $model->setProperty("original_mark",    $data["original_mark"]);
        $model->setProperty("final_mark",       $data["final_mark"]);
        $model->setProperty("time",             new DateTime($data["time"]));
        $model->setProperty("chairman_id",      intval($data["chairman_id"]));
        $model->setProperty("class_teacher_id", intval($data["class_teacher_id"]));
        $model->setProperty("examiner_id",      intval($data["examiner_id"]));

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
