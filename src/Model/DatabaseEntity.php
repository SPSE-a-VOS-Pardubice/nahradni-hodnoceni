<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class DatabaseEntity {
    /**
     * @var Database
     */
    protected $database;

    /**
     * @var array<string, mixed>
     */
    protected $properties; 

    public function __construct(Database $database) {
        $this->database = $database;
    }

    /**
     * Handle external `set` call of a database property.
     */
    public function __set(string $name, $value): void {
        $this->setProperty($name, $value);
    }

    public function __get(string $name): mixed {
        return $this->getProperty($name);
    }

    /**
     * @return array<DatabaseEntityProperty>
     */
    public static abstract function getProperties(): array;

    /**
     * Internal method for handling all database `set` calls.
     */
    protected function setProperty(string $key, $value): void {
        $this->properties[$key] = $value;
    }

    protected function getProperty(string $key): mixed {
        return $this->properties[$key];
    }

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
}
