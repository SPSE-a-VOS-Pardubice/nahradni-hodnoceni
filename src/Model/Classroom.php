<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;
    
    class Classroom extends FullDatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

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
            // TODO
            return [];
        }

        public static function parsePostData(Database $database, array $data, int $id = 0): ParsedPostData {
            $model = $id === 0 ? new Classroom($database) : Classroom::get($database, $id);
            if ($model === null) 
                throw new \RuntimeException("Error Processing Request", 1);

            $model->setProperty("label", $data["label"]);

            return new ParsedPostData($model, []); // TODO
        }

        public static function applyPostData(ParsedPostData $parsedData): void {
            $model = $parsedData->model;
            $model->write();

            // TODO
        }
    }

?>