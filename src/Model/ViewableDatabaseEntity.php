<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

abstract class ViewableDatabaseEntity extends DatabaseEntity {
    public abstract static function getProperties(): array;
}