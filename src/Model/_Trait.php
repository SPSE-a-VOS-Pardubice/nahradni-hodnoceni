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

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new _Trait($database) : _Trait::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("name", $data["name"]);

        return new ParsedPostData($model, []); // TODO
    }

    public static function applyPostData(ParsedPostData $parsedData): void {
        $model = $parsedData->model;
        $model->write();// TODO
    }

    public static function getTableName(): string {
        return "Traits";
    }
}
