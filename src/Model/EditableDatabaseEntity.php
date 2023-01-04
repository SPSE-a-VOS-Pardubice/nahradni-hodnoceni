<?php

declare(strict_types=1);

namespace Spse\NahradniHodnoceni\Model;

interface EditableDatabaseEntity extends ViewableDatabaseEntity {
    public static function getSelectOptions(Database $database): array;
}