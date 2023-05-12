<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class _Class extends FullDatabaseEntity {

    public static function getProperties(): array {
        $currentYear = new \DateTime();
        
        return [
            new DatabaseEntityProperty("year","Rok", DatabaseEntityPropertyType::INTEGER, null, false, $currentYear->format("w")),
            new DatabaseEntityProperty("label", "Označení", DatabaseEntityPropertyType::STRING, null, false, ""),
            new DatabaseEntityProperty("class_teacher_id", "Třídní učitel", DatabaseEntityPropertyType::EXTERNAL_DATA, Teacher::class, false, 0) 
        ];
    }

    public function getFormatted(): string {
        return sprintf("%s.%s", $this->grade, $this->label);
    }

    public static function getTableName(): string {
        return "Classes";
    }
}
