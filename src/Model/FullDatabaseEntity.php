<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class FullDatabaseEntity extends DatabaseEntity {
    /**
     * @var int
     */
    protected int $id;

    public static function get(int $id): FullDatabaseEntity {
        // TODO: read
    }

    public function write(): void {
        // TODO: insert|update
    }

    public function remove(): void {
        // TODO: delete
    }
}