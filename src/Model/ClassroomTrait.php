<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class ClassroomTrait extends DatabaseEntity implements FormattableDatabaseEntity {
    private int $trait_id;
    private int $classroom_id;

    protected function getProperty(string $key) {
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public function getFormatted(): string {
        return $this->nazev;
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 2) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $object = new ClassroomTrait($database);
        $object->setProperty("trait_id",        intval($row[0]));
        $object->setProperty("classroom_id",    intval($row[1]));
        return $object;
    }
    

    public function write(): void {
        $parameters = [
            new DatabaseParameter("trait_id",       $this->trait_id),
            new DatabaseParameter("classroom_id",   $this->classroom_id),
        ];

        $this->database->execute("
            INSERT INTO ClassroomsTraits (
                trait_id
                classroom_id
            )
            VALUES (
                :trait_id
                :classroom_id
        )", $parameters);
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM ClassroomsTraits
            WHERE
                trait_id = :trait_id,
                classroom_id = :classroom_id
            LIMIT 1
        ", [
            new DatabaseParameter("trait_id",       $this->trait_id),
            new DatabaseParameter("classroom_id",   $this->classroom_id),
        ]);
    }

    static public function getForClassroom(Database $database, int $classroom_id): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM ClassroomsTraits
            WHERE
                classroom_id = :classroom_id
        ", [
            new DatabaseParameter("classroom_id", $classroom_id),
        ]);

        return array_map(function (array $row) use ($database) {
            return ClassroomTrait::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
