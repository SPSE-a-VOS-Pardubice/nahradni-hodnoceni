<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class IntermediateDatabaseEntity extends DatabaseEntity {
    public function write(): void {
        // TODO: insert|update
    }

    public function remove(): void {
        // TODO: delete
    }
}