<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class DatabaseEntity {
    protected Database $database;

    /**
     * ID is the only required parameter for a database entity.
     */
    protected int $id;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    /**
     * Handle external `set` call of a database property.
     */
    public function __set(string $name, $value): void {
        $this->setProperty($name, $value);
    }

    public function __get(string $name) {
        return $this->getProperty($name);
    }

    /**
     * Internal method for handling all database `set` calls.
     */
    abstract protected function setProperty(string $key, $value): void;

    abstract protected function getProperty(string $key);

    /**
     * Construct a single instance from given database row.
     */
    abstract public static function fromDatabaseRow(Database $database, array $row);

    /**
     * Write this object to the database. Either update the row or create new if it does not already exist (when `id` equals zero or is empty in case of string).
     */
    abstract public function write(): void;

    /**
     * Remove the object from the database. Works only with non-default `id` values.
     */
    abstract public function remove(): void;

    /**
     * metoda vrátí pole modelů, které naformátuje z furmuláře
     */
    abstract public static function parsePostData(array $data, Database $database, int $id = 0): array;

    /**
     * vyřeší vztady mezi tabulkami (daty, které je představují a provede zápisy)
     */
    abstract public static function applyPostData(array $models): void;
}
