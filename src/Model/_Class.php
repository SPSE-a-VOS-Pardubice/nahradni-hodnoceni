<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

class _Class extends DatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

    public static function getProperties(): array {
        // TODO
        return [];
    }

    public static function getSelectOptions(Database $database): array {
        // TODO 
        return [];
    }

    public function getFormatted(): string {
        // TODO 
        return "";
    }

    public function getIntermediateData(): array {
        // mělo by vracet učitele
        return [];
    }

    public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
        $model = $id === 0 ? new _Class($database) : _Class::get($database, $id);
        if ($model === null) 
            throw new \RuntimeException("Error Processing Request", 1);

        $model->setProperty("year",             intval($data["year"]));
        $model->setProperty("grade",            intval($data["grade"]));
        $model->setProperty("label",            $data["label"]);
        $model->setProperty("class_teacher_id", intval($data["class_teacher_id"]));

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
