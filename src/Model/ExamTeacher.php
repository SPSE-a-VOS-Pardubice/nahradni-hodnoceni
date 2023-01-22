<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ExamTeacher extends DatabaseEntity implements FormattableDatabaseEntity {
    private int $exam_id;
    private int $teacher_id;
    private string $Role;

    protected function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("exam_id", "exam_id", ViewablePropertyType::INTEGER),
            new ViewableProperty("teacher_id", "teacher_id", ViewablePropertyType::INTEGER,  true),
            new ViewableProperty("Role", "Role", ViewablePropertyType::STRING,  true)
        ];
    }

    public function getFormatted(): string {
        // TODO: asi role učitel se hádám získá jinak
        return $this->Role;
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 3) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new ExamTeacher($database);
        $object->setProperty("exam_id",         intval($row[0]));
        $object->setProperty("teacher_id",      intval($row[1]));
        $object->setProperty("Role",            $row[2]);
        return $object;
    }

    public function write(): void {
        $parameters = [
            new DatabaseParameter("exam_id",       $this->exam_id),
            new DatabaseParameter("teacher_id",   $this->teacher_id),
            new DatabaseParameter("Role",   $this->Role),
        ];

        if(true) { // co ten clovek prede mnou bere za drogy lmao
            $this->database->execute("
            INSERT INTO ExamsTeachers (
                exam_id,
                teacher_id,
                Role
            )
            VALUES (
                :exam_id,
                :teacher_id,
                :Role
            )", $parameters);
        }else{
            // TODO: TOTO JE BLBOST ALE SYTUACE BY NEMĚLA NASTAT
            $this->database->execute("
            UPDATE ExamsTeachers
            SET
                exam_id = :exam_id
                teacher_id = :teacher_id
                Role = :Role
            WHERE
                id = :id
            ", $parameters);
        }
    }
    public function remove(): void {
        $this->database->execute("
            DELETE FROM ExamsTeachers
            WHERE
                exam_id = :exam_id
                teacher_id = :teacher_id
            LIMIT 1
        ", [
            new DatabaseParameter("exam_id",       $this->trait_id),
            new DatabaseParameter("teacher_id",   $this->classroom_id),
        ]);
    }

    static public function getForExamp(Database $database, int $exam_id): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM ExamsTeachers
            WHERE
                exam_id = :exam_id
        ", [
            new DatabaseParameter("exam_id", $exam_id),
        ]);

        return array_map(function (array $row) use ($database) {
            $exampTeacher = ExamTeacher::fromDatabaseRow($database, $row);

            return [$exampTeacher->teacher_id => $exampTeacher];
        }, $rows);
    }

    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $examTeachers = [];

        foreach ($data as $key => $value) {
            if (preg_match("/^teacher-[0-9]*$/", $key)) {
                $examTeacher = new ClassroomTrait($database);
                $examTeacher->setProperty("trait_id",   intval($value));
                $examTeacher->setProperty("Role",       $data[preg_replace("teacher", "role", $key)]);
                $examTeachers[] = $examTeacher;
            }
        }

        return $examTeachers;
    }

    public static function applyPostData(array $models): void {

        $trait = $models[0];

        $trait->write();
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