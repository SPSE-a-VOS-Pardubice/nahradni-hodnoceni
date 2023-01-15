<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;
use DateTime;

const MARK_OPTIONS = ["1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5", "N" => "N"];

class Exam extends DatabaseEntity implements ViewableDatabaseEntity {
    protected int $id = 0;
    private int $student_id;
    private int $subject_id;
    private int $classroom_id;
    private string $original_mark;
    private string $final_mark;
    private DateTime $time;

    public function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",              "ID",               ViewablePropertyType::INTEGER),
            new ViewableProperty("student_id",      "Student",          ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("subject_id",      "Předmět",          ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("classroom_id",    "Učebna",           ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("original_mark",   "Původní známka",   ViewablePropertyType::STRING,   true),
            new ViewableProperty("final_mark",      "Výsledná známka",  ViewablePropertyType::STRING,   true),
            new ViewableProperty("time",            "Termín konání",    ViewablePropertyType::DATETIME),
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $student_id = [];
        $subject_id = [];
        $classroom_id = [];

        foreach (Student::getAll($database) as $student) {
            $student_id[$student->id] = $student->getFormatted(); 
        }
        foreach (Subject::getAll($database) as $subject) {
            $subject_id[$subject->id] = $subject->getFormatted(); 
        }
        foreach (Classroom::getAll($database) as $classroom) {
            $classroom_id[$classroom->id] = $classroom->getFormatted();
        }

        return [
            "student_id"    => $student_id,
            "subject_id"    => $subject_id,
            "classroom_id"  => $classroom_id,
            "original_mark" => MARK_OPTIONS,
            "final_mark"    => MARK_OPTIONS,
        ];
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 7) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Exam($database);
        $object->setProperty("id",              intval($row[0]));
        $object->setProperty("student_id",      intval($row[1]));
        $object->setProperty("subject_id",      intval($row[2]));
        $object->setProperty("classroom_id",    intval($row[3]));
        $object->setProperty("original_mark",   $row[4]);
        $object->setProperty("final_mark",      $row[5]);
        $object->setProperty("time",            new DateTime($row[6]));
        return $object;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",             $this->id),
            new DatabaseParameter("student_id",     $this->student_id),
            new DatabaseParameter("subject_id",     $this->subject_id),
            new DatabaseParameter("classroom_id",   $this->classroom_id),
            new DatabaseParameter("original_mark",  $this->original_mark),
            new DatabaseParameter("final_mark",     $this->final_mark),
            new DatabaseParameter("time",           $this->time),
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Exams (
                    student_id,
                    subject_id,
                    classroom_id,
                    original_mark,
                    final_mark,
                    time
                )
                VALUES (
                    :student_id,
                    :subject_id,
                    :classroom_id,
                    :original_mark,
                    :final_mark,
                    :time
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE Exams
                SET
                    student_id      = :student_id
                    subject_id      = :subject_id
                    classroom_id    = :classroom_id
                    original_mark   = :original_mark
                    final_mark      = :final_mark
                    time            = :time
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
    
    static public function get(Database $database, string $id): ?Exam {
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
}
