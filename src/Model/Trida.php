<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class Trida extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {
    protected int $id = 0;
    private int $rok;
    private int $rocnik;
    private string $oznaceni;
    private int $tridni_ucitel_id;

    public function getProperty(string $key){
        return $this->$key;
    }

    protected function setProperty(string $key, $value): void {
        $this->$key = $value;
    }

    public static function getProperties(): array {
        return [
            new ViewableProperty("id",                  "ID",               ViewablePropertyType::INTEGER),
            new ViewableProperty("rok",                 "Rok",              ViewablePropertyType::INTEGER),
            new ViewableProperty("rocnik",              "Ročník",           ViewablePropertyType::INTEGER),
            new ViewableProperty("oznaceni",            "Označení",         ViewablePropertyType::STRING),
            new ViewableProperty("tridni_ucitel_id",    "Třídní učitel",    ViewablePropertyType::INTEGER,  true)
        ];
    }

    public static function getSelectOptions(Database $database): array {
        $class_teacher_id = [];

        foreach(Ucitel::getAll($database) as $teacher) {
            $class_teacher_id[$teacher->id] = $teacher->getFormatted();
        }

        return [
            "tridni_ucitel_id" => $class_teacher_id,
        ];
    }

    public function getFormatted(): string {
        return sprintf("%s.%s", $this->rocnik, $this->oznaceni);
    }

    public function getIntermediateData(): array {
        return [];
    }

    public static function fromDatabaseRow(Database $database, array $row) {
        // Zkontroluj délku dané řady.
        if (count($row) !== 5) {
            throw new \InvalidArgumentException("Délka řady z databáze neodpovídá.");
        }

        // Vybuduj novou instanci a vrať ji.
        $trida = new trida($database);
        $trida->setProperty("id",               intval($row[0]));
        $trida->setProperty("rok",              intval($row[1]));
        $trida->setProperty("rocnik",           intval($row[2]));
        $trida->setProperty("oznaceni",         $row[3]);
        $trida->setProperty("tridni_ucitel_id", $row[4]);
        return $trida;
    }

    public function write(): void {
        // Připrav parametry pro dotaz.
        $parameters = [
            new DatabaseParameter("id",                 $this->id),
            new DatabaseParameter("rok",                $this->rok),
            new DatabaseParameter("rocnik",             $this->rocnik),
            new DatabaseParameter("oznaceni",           $this->oznaceni),
            new DatabaseParameter("tridni_ucitel_id",   $this->tridni_ucitel_id)
        ];

        if ($this->id === 0) {
            $this->database->execute("
                INSERT INTO Tridy (
                    rok,
                    rocnik,
                    oznaceni,
                    tridni_ucitel_id
                )
                VALUES (
                    :rok,
                    :rocnik,
                    :oznaceni,
                    :tridni_ucitel_id
                )
            ", $parameters);
        } else {
            $this->database->execute("
                UPDATE Tridy
                SET
                    rok = :rok
                    rocnik = :rocnik
                    oznaceni = :oznaceni
                    tridni_ucitel_id = :tridni_ucitel_id 
                WHERE
                    id = :id
            ", $parameters);
        }
    }

    public function remove(): void {
        $this->database->execute("
            DELETE FROM Tridy
            WHERE
                id = :id
            LIMIT 1
        ", [
            new DatabaseParameter("id", $this->id)
        ]);
    }
    
    static public function get(Database $database, string $id): Trida {
        $row = $database->fetchSingle("
            SELECT
                *
            FROM Tridy
            WHERE
                id = :id
        ", [
            new DatabaseParameter("id", $id)
        ]);

        // TODO: Check empty result

        return Trida::fromDatabaseRow($database, $row);
    }

    static public function getAll(Database $database): array {
        $rows = $database->fetchMultiple("
            SELECT
                *
            FROM Tridy
        ");

        return array_map(function (array $row) use($database) {
            return Trida::fromDatabaseRow($database, $row);
        }, $rows);
    }
}
