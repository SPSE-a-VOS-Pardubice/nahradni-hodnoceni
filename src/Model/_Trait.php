<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class _Trait extends FullDatabaseEntity {

    public static function getProperties(): array {
        return [
            new DatabaseEntityProperty("name", "Název", DatabaseEntityPropertyType::STRING, null, false, "")
        ];
    }

    public function getFormatted(): string {
        return $this->name;
    }

    public static function getTableName(): string {
        return "Traits";
    }
}
