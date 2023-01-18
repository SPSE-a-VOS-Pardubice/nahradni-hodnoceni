<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Subject extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

    protected int $id = 0;
    private string $name;
    private string $abbreviation;

    protected function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",              "ID",       ViewablePropertyType::INTEGER),
            new ViewableProperty("name",            "Název",    ViewablePropertyType::STRING),
            new ViewableProperty("abbreviation",    "Zkratka",  ViewablePropertyType::STRING),
            new ViewableProperty("traits",          "Příznaky", ViewablePropertyType::INTERMEDIATE_DATA,    true),
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
        return $this->name;
    }

    public function getIntermediateData(): array {
    return [
        "traits" => array_map(function ($classroomTrait) {
                return _Trait::get($this->database, $classroomTrait->trait_id);
            }, ClassroomTrait::getForClassroom($this->database, $this->id)),
        ];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 3) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new Subject($database);
        $object->setProperty("id",              intval($row[0]));
        $object->setProperty("name",            $row[1]);
        $object->setProperty("abbreviation",    $row[2]);
        return $object;
    }
    

    public function write(): void {
        $parameters = [
            new DatabaseParameter("id",             $this->id),
            new DatabaseParameter("name",           $this->name),
            new DatabaseParameter("abbreviation",   $this->abbreviation),
        ];

        if($this->id === 0) {
            $this->database->execute("
            INSERT INTO Subjects (
                name
                abbreviation
            )
            VALUES (
                :name
                :abbreviation
            )", $parameters);
        }else{
            $this->database->execute("
            UPDATE Subjects
            SET
            name    = :name
            zkratka = :abbreviation
            WHERE
                id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Subjects
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id),
        ]);
    }

    static public function get(Database $database, string $id): ?Subject {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Subjects
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id),
        ]);

        if ($row === false) {
            return null;
        }
        return Subject::fromDatabaseRow($database, $row);
        
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Subjects
        ");

        return array_map(function (array $row) use($database) {
            return Subject::fromDatabaseRow($database, $row);
        }, $rows);
    }
    public static function parsePostData(array $data, Database $database, int $id = 0): array {

        $subject = null;
        if ($id > 0) {
            $subject = Subject::get($database, strval($id));

            if ($subject == null) 
                throw new Exception("Error Processing Request", 1);
        } else {
            $subject = new Subject($database);
        }

        $subject->setProperty("name",            $data["name"]);
        $subject->setProperty("abbreviation",    $data["abbreviation"]);

        return [$subject, SubjectTrait::parsePostData($data, $database)];
    }

    public static function applyPostData(array $models): void {

        $subject = $models[0];
        $subjectsTrails = $models[1];
        
        // tenhle algorizmus by se možná dal imlementovat někde obecně ???

        // vem si z priznakUcebna data kde idUcebna = id
        $traitsInDB = SubjectTrait::getForSubject($subject->database, $subject->id);
        // deklarace místa pro traity co jsou i v posu i v db
        $willBeInDB = [];
    
        // projdi traity v postu 
        foreach ($subjectsTrails as $trait) {
            // pokud není trait ve výsledku z db přidej ho do db 
            if ($traitsInDB[$trait->trait_id] == null) {
                // šla by volat i metoda write 
                // TODO: zamyslet se: asi vy modely pro mezi tabulky nemusei mýt metodu applyPostDat
                $trait->subject_id = $subject->id;
                SubjectTrait::applyPostData([$trait]);
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
        

        $subject->write();
    }
}
