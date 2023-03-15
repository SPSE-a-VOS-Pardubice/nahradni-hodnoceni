<?php

    declare(strict_types=1);

    namespace Spse\NahradniHodnoceni\Model;
    
    class Classroom extends FullDatabaseEntity implements FormattableDatabaseEntity, ViewableDatabaseEntity {

        public static function getProperties(): array {
            return [
                new DatabaseEntityProperty("label", "Označení", DatabaseEntityPropertyType::String, false, false, ""),
                new DatabaseEntityProperty("traits", "Příznaky", DatabaseEntityPropertyType::Intermediate_data, true, true, []) // TODO intermediate_data
            ];
        }

        public static function getSelectOptions(Database $database): array {
            // TODO
            return [];
        }

        public function getFormatted(): string {
            return $this->label;
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
        public static function getDatabaseName(): string {
            return "classrooms";
        }
    }


?>